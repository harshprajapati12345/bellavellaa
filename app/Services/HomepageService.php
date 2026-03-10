<?php

namespace App\Services;

use App\Models\Category;
use App\Models\HomepageContent;
use App\Models\Service;
use App\Models\Media;
use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\ServiceResource;
use App\Http\Resources\Api\MediaResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomepageService
{
    /**
     * Build the homepage payload for the API.
     */
    public static function build(): array
    {
        $sectionsConfig = Cache::remember('homepage_sections', 300, function () {
            return HomepageContent::where('status', 'Active')
                ->orderBy('sort_order', 'asc')
                ->get();
        });

        $resolvedSections = $sectionsConfig->map(function ($section) {
            $content = is_array($section->content) ? $section->content : [];

            return [
                'id'           => $section->id,
                'type'         => $section->section,
                'name'         => $section->name,
                'title'        => $section->title,
                'subtitle'     => $section->subtitle,
                'media_type'   => $section->media_type,
                'content_type' => $section->content_type,
                'data_source'  => $section->data_source,
                'description'  => $section->description,
                'btn_text'     => $section->btn_text,
                'btn_link'     => $section->btn_link,
                'sort_order'   => $section->sort_order,
                'items'        => self::resolveSectionItems($section, $content),
            ];
        })->values()->toArray();

        return $resolvedSections;
    }

    /**
     * Route to specific resolver based on section type.
     * Now receives the full $section model for FK access.
     */
    protected static function resolveSectionItems(HomepageContent $section, array $content)
    {
        // Normalize section type (handle hyphens)
        $sectionType = str_replace('-', '_', $section->section);
        
        return match ($sectionType) {
            'hero_banner'       => self::resolveMediaItems($section->id, $content),
            'image_banner'      => self::resolveMediaItems($section->id, $content),
            'testimonials'      => self::resolveMediaItems($section->id, $content),
            'trending_packages' => self::resolveMediaItems($section->id, $content),
            'download_app'      => self::resolveMediaItems($section->id, $content),
            'test'              => self::resolveMediaItems($section->id, $content),
            'harsh'             => self::resolveMediaItems($section->id, $content),
            'sdfasd'            => self::resolveMediaItems($section->id, $content),
            'category_carousel' => self::resolveCategoryCarousel($content),
            'service_grid'      => self::resolveServiceGrid($content),
            'service_carousel'  => self::resolveServiceCarousel($content),
            'video_stories'     => self::resolveVideoStories($section->id, $content),
            'active_booking'    => self::resolveActiveBooking(),
            default             => self::resolveFallbackOption($section->section, $section->id),
        };
    }

    /**
     * Load media items linked to this section via FK (hero_banner, image_banner).
     */
    protected static function resolveMediaItems(int $sectionId, array $content): array
    {
        $limit = (int) ($content['limit'] ?? 20);

        $mediaItems = Media::where('homepage_content_id', $sectionId)
            ->where('status', 'Active')
            ->orderBy('order')
            ->limit($limit > 0 ? $limit : 20)
            ->get();

        return MediaResource::collection($mediaItems)->resolve();
    }

    protected static function resolveCategoryCarousel(array $content)
    {
        $limit = (int) ($content['limit'] ?? 8);
        $featuredOnly = $content['featured_only'] ?? true;

        $query = Category::where('status', 'Active');
        if ($featuredOnly) {
            $query->where('featured', true);
        }

        $categories = $query->limit($limit > 0 ? $limit : 8)
                            ->get(['id', 'name', 'slug', 'image', 'color', 'badge']);

        return CategoryResource::collection($categories);
    }

    protected static function resolveServiceGrid(array $content)
    {
        return self::fetchServices($content);
    }

    protected static function resolveServiceCarousel(array $content)
    {
        return self::fetchServices($content);
    }

    protected static function fetchServices(array $content)
    {
        $limit = (int) ($content['limit'] ?? 10);
        $dataSource = $content['data_source'] ?? 'featured_services';

        $query = Service::where('status', 'Active');

        switch ($dataSource) {
            case 'trending':
                $query->orderBy('bookings', 'desc');
                break;
            case 'featured_services':
            default:
                $query->where('featured', true);
                break;
        }

        return ServiceResource::collection($query->limit($limit > 0 ? $limit : 10)->get());
    }

    /**
     * Load video media linked to this section via FK.
     */
    protected static function resolveVideoStories(int $sectionId, array $content)
    {
        $limit = (int) ($content['limit'] ?? 10);

        $videos = Media::where('homepage_content_id', $sectionId)
            ->where('type', 'video')
            ->where('status', 'Active')
            ->orderBy('order')
            ->limit($limit > 0 ? $limit : 10)
            ->get();

        return MediaResource::collection($videos)->resolve();
    }

    protected static function resolveActiveBooking(): array
    {
        if (!Auth::guard('client')->check() && !Auth::guard('api')->check()) {
            return [];
        }

        $user = Auth::guard('client')->user() ?? Auth::guard('api')->user();

        if (!$user && auth()->check()) {
            $user = auth()->user();
        }

        if (!$user) {
            return [];
        }

        return [];
    }

    protected static function resolveFallbackOption(string $type, $sectionId): array
    {
        Log::info('Homepage section type has no resolver, returning empty items', [
            'section_id' => $sectionId,
            'type'       => $type,
        ]);

        return []; // Return empty array — do NOT return false (false causes the section to be dropped)
    }
}
