<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\HierarchyBanner;
use App\Models\Service;
use App\Models\ServiceGroup;
use App\Models\ServiceType;
use App\Models\ServiceVariant;
use Illuminate\Database\Seeder;

class HierarchyBannerSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::query()->where('slug', 'salon-for-women')->first();
        $group = ServiceGroup::query()->where('slug', 'salon-for-women-luxe')->first();
        $type = ServiceType::query()->where('slug', 'facials')->first();
        $service = Service::query()->where('slug', 'korean-facial')->first();
        $variant = ServiceVariant::query()->where('slug', 'age-rewind-facial')->first();

        $records = array_filter([
            $category ? [
                'placement_type' => 'page_header',
                'target_type' => 'category',
                'target_id' => $category->id,
                'title' => 'Salon for Women',
                'subtitle' => 'Top booked beauty services with curated seasonal offers.',
                'media_type' => 'image',
                'media_path' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=1400&q=80',
                'thumbnail_path' => null,
                'action_link' => null,
                'button_text' => 'Explore',
                'sort_order' => 1,
                'status' => 'Active',
            ] : null,
            $group ? [
                'placement_type' => 'page_header',
                'target_type' => 'service_group',
                'target_id' => $group->id,
                'title' => 'Luxe',
                'subtitle' => 'Premium at-home salon experiences for visible results.',
                'media_type' => 'image',
                'media_path' => 'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?auto=format&fit=crop&w=1400&q=80',
                'thumbnail_path' => null,
                'action_link' => null,
                'button_text' => 'Book Luxe',
                'sort_order' => 1,
                'status' => 'Active',
            ] : null,
            $group ? [
                'placement_type' => 'promo_banner',
                'target_type' => 'service_group',
                'target_id' => $group->id,
                'title' => 'Luxe Offers',
                'subtitle' => 'Save more on premium treatments this week.',
                'media_type' => 'image',
                'media_path' => 'https://images.unsplash.com/photo-1515377905703-c4788e51af15?auto=format&fit=crop&w=1400&q=80',
                'thumbnail_path' => null,
                'action_link' => null,
                'button_text' => 'View Offer',
                'sort_order' => 2,
                'status' => 'Active',
            ] : null,
            $type ? [
                'placement_type' => 'page_header',
                'target_type' => 'service_type',
                'target_id' => $type->id,
                'title' => 'Facials',
                'subtitle' => 'Advanced skincare rituals designed for glow and recovery.',
                'media_type' => 'image',
                'media_path' => 'https://images.unsplash.com/photo-1519823551278-64ac92734fb1?auto=format&fit=crop&w=1400&q=80',
                'thumbnail_path' => null,
                'action_link' => null,
                'button_text' => 'See Facials',
                'sort_order' => 1,
                'status' => 'Active',
            ] : null,
            $type ? [
                'placement_type' => 'promo_banner',
                'target_type' => 'service_type',
                'target_id' => $type->id,
                'title' => 'Facial Upgrade',
                'subtitle' => 'Add a glow booster and save on combo pricing.',
                'media_type' => 'video',
                'media_path' => 'https://samplelib.com/lib/preview/mp4/sample-5s.mp4',
                'thumbnail_path' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=1200&q=80',
                'action_link' => null,
                'button_text' => 'Upgrade',
                'sort_order' => 2,
                'status' => 'Active',
            ] : null,
            $service ? [
                'placement_type' => 'popup_banner',
                'target_type' => 'service',
                'target_id' => $service->id,
                'title' => 'Korean Facial',
                'subtitle' => 'Hydration-led skin renewal with premium finishing care.',
                'media_type' => 'image',
                'media_path' => 'https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?auto=format&fit=crop&w=1400&q=80',
                'thumbnail_path' => null,
                'action_link' => null,
                'button_text' => 'Add Now',
                'sort_order' => 1,
                'status' => 'Active',
            ] : null,
            $variant ? [
                'placement_type' => 'popup_banner',
                'target_type' => 'variant',
                'target_id' => $variant->id,
                'title' => 'Age-Rewind Facial',
                'subtitle' => 'Focused anti-ageing care with collagen-rich actives.',
                'media_type' => 'video',
                'media_path' => 'https://samplelib.com/lib/preview/mp4/sample-10s.mp4',
                'thumbnail_path' => 'https://images.unsplash.com/photo-1552693673-1bf958298935?auto=format&fit=crop&w=1200&q=80',
                'action_link' => null,
                'button_text' => 'Choose Variant',
                'sort_order' => 1,
                'status' => 'Active',
            ] : null,
        ]);

        foreach ($records as $record) {
            HierarchyBanner::query()->updateOrCreate(
                [
                    'placement_type' => $record['placement_type'],
                    'target_type' => $record['target_type'],
                    'target_id' => $record['target_id'],
                    'sort_order' => $record['sort_order'],
                ],
                $record
            );
        }

        $this->command?->info('Hierarchy banners seeded.');
    }
}
