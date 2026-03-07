<?php

namespace App\Services;

use App\Models\Category;
use App\Models\HomepageContent;
use App\Models\Service;
use App\Models\Media;
use App\Models\Booking; // Assuming Booking model exists
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
        // 1. Fetch cached structural sections (not user specific data)
        $sectionsConfig = Cache::remember('homepage_sections', 300, function () {
            return HomepageContent::where('status', 'Active')
                ->orderBy('sort_order', 'asc')
                ->get();
        });

        // 2. Resolve data for each section
        $resolvedSections = $sectionsConfig->map(function ($section) {
            $content = is_array($section->content) ? $section->content : [];
            
            return [
                'id'         => $section->id,
                'type'       => $section->section,
                'title'      => $section->title,
                'subtitle'   => $section->subtitle ?? ($content['subtitle'] ?? null),
                'sort_order' => $section->sort_order,
                'items'      => self::resolveSectionItems($section->section, $content, $section->id),
            ];
        })->filter(function ($sectionData) {
            // Optional: Filter out empty sections if needed, or non-matching types
            return $sectionData['items'] !== false; // if it returns false, it's an unsupported section
        })->values()->toArray();

        return $resolvedSections;
    }

    /**
     * Route to specific resolver based on section type.
     */
    protected static function resolveSectionItems(string $type, array $content, $sectionId)
    {
        return match ($type) {
            'hero_banner'       => self::resolveHeroBanner($content),
            'image_banner'      => self::resolveImageBanner($content),
            'category_carousel' => self::resolveCategoryCarousel($content),
            'service_grid'      => self::resolveServiceGrid($content),
            'service_carousel'  => self::resolveServiceCarousel($content),
            'video_stories'     => self::resolveVideoStories($content),
            'active_booking'    => self::resolveActiveBooking(),
            default             => self::resolveFallbackOption($type, $sectionId),
        };
    }

    protected static function resolveHeroBanner(array $content): array
    {
        return $content['items'] ?? [];
    }

    protected static function resolveImageBanner(array $content): array
    {
        return $content['items'] ?? [];
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

        $services = $query->limit($limit > 0 ? $limit : 10)->get();

        return ServiceResource::collection($services);
    }

    protected static function resolveVideoStories(array $content)
    {
        $limit = (int) ($content['limit'] ?? 10);

        $videos = Media::where('type', 'video')
            ->where('status', 'Active')
            ->limit($limit > 0 ? $limit : 10)
            ->get();

        return MediaResource::collection($videos);
    }

    protected static function resolveActiveBooking(): array
    {
        // Must be guest safe
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

        // Fetch user's active/upcoming bookings. Assuming 'bookings' table exists.
        // Return resource or array
        // Fallback to empty if bookings logic not fully wired in this context
        return [];
    }

    protected static function resolveFallbackOption(string $type, $sectionId)
    {
        Log::warning('Unsupported homepage section type requested', [
            'section_id' => $sectionId,
            'type'       => $type,
        ]);

        return false; // Tells the map to skip it
    }
}
