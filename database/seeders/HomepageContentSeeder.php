<?php

namespace Database\Seeders;

use App\Models\HomepageContent;
use App\Models\Media;
use Illuminate\Database\Seeder;

class HomepageContentSeeder extends Seeder
{
    private const SECTION_TYPES = [
        'hero_banner',
        'category_carousel',
        'service_carousel',
        'service_grid',
        'video_stories',
        'image_banner',
        'active_booking',
        'testimonials',
        'trending_packages',
        'download_app',
    ];

    public function run(): void
    {
        Media::whereNotNull('homepage_content_id')->delete();
        HomepageContent::query()->delete();

        $sections = [
            [
                'section' => 'hero_banner',
                'name' => 'Hero Banner',
                'title' => 'Premium Beauty Services At Home',
                'subtitle' => 'Salon, spa, hair and bridal bookings in a few taps.',
                'content_type' => 'static',
                'media_type' => 'banner',
                'sort_order' => 1,
                'content' => ['limit' => 3],
                'media' => [
                    [
                        'title' => 'Salon for Women',
                        'subtitle' => 'Glow sessions, facials and wax services at home',
                        'url' => 'https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?q=80&w=1200',
                        'type' => 'banner',
                        'target_page' => 'services',
                    ],
                    [
                        'title' => 'Spa for Women',
                        'subtitle' => 'Relaxing massage and wellness care',
                        'url' => 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?q=80&w=1200',
                        'type' => 'banner',
                        'target_page' => 'services',
                    ],
                    [
                        'title' => 'Bridal Packages',
                        'subtitle' => 'Book a complete event-ready beauty setup',
                        'url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?q=80&w=1200',
                        'type' => 'banner',
                        'target_page' => 'packages',
                    ],
                ],
            ],
            [
                'section' => 'category_carousel',
                'name' => 'Category Carousel',
                'title' => 'Explore Categories',
                'subtitle' => 'Start from the service category you want today.',
                'content_type' => 'dynamic',
                'media_type' => 'banner',
                'sort_order' => 2,
                'content' => ['limit' => 8, 'featured_only' => true],
            ],
            [
                'section' => 'service_carousel',
                'name' => 'Service Carousel',
                'title' => 'Most Booked Services',
                'subtitle' => 'Fast moving services customers book every day.',
                'content_type' => 'dynamic',
                'data_source' => 'trending',
                'media_type' => 'banner',
                'sort_order' => 3,
                'content' => ['limit' => 10, 'data_source' => 'trending'],
            ],
            [
                'section' => 'service_grid',
                'name' => 'Service Grid',
                'title' => 'Featured Services',
                'subtitle' => 'Highlighted treatments picked for homepage visibility.',
                'content_type' => 'dynamic',
                'data_source' => 'featured_services',
                'media_type' => 'banner',
                'sort_order' => 4,
                'content' => ['limit' => 8, 'data_source' => 'featured_services'],
            ],
            [
                'section' => 'video_stories',
                'name' => 'Video Stories',
                'title' => 'Watch Real Service Moments',
                'subtitle' => 'Short clips from beauty and wellness sessions.',
                'content_type' => 'static',
                'media_type' => 'video',
                'sort_order' => 5,
                'content' => ['limit' => 3],
                'media' => [
                    [
                        'title' => 'Facial Session',
                        'subtitle' => 'Hydration and glow service walkthrough',
                        'url' => 'https://assets.mixkit.co/videos/preview/mixkit-young-woman-applying-makeup-to-her-face-40038-large.mp4',
                        'type' => 'video',
                    ],
                    [
                        'title' => 'Hair Styling',
                        'subtitle' => 'Professional styling at home',
                        'url' => 'https://assets.mixkit.co/videos/preview/mixkit-girl-in-white-t-shirt-doing-her-hair-40040-large.mp4',
                        'type' => 'video',
                    ],
                    [
                        'title' => 'Wellness Massage',
                        'subtitle' => 'Spa experience in your living room',
                        'url' => 'https://assets.mixkit.co/videos/preview/mixkit-woman-relaxing-in-a-spa-39792-large.mp4',
                        'type' => 'video',
                    ],
                ],
            ],
            [
                'section' => 'image_banner',
                'name' => 'Image Banner',
                'title' => 'Flat 25% Off Luxe Facials',
                'subtitle' => 'Limited-time promo on premium salon experiences.',
                'content_type' => 'static',
                'media_type' => 'banner',
                'description' => 'Homepage promotional banner for spotlight campaigns.',
                'btn_text' => 'Book Now',
                'btn_link' => '/categories/salon-for-women',
                'sort_order' => 6,
                'content' => [],
                'media' => [
                    [
                        'title' => 'Luxe Facial Offer',
                        'subtitle' => 'Premium skin care offers available this week',
                        'url' => 'https://images.unsplash.com/photo-1515377905703-c4788e51af15?q=80&w=1200',
                        'type' => 'banner',
                        'target_page' => 'services',
                    ],
                ],
            ],
            [
                'section' => 'active_booking',
                'name' => 'Active Booking',
                'title' => 'Track Your Active Booking',
                'subtitle' => 'This section appears when the customer has a live order.',
                'content_type' => 'dynamic',
                'media_type' => 'banner',
                'sort_order' => 7,
                'content' => [],
            ],
            [
                'section' => 'testimonials',
                'name' => 'Testimonials',
                'title' => 'Why Clients Trust Bellavella',
                'subtitle' => 'Safety, consistency and premium professionals.',
                'content_type' => 'static',
                'media_type' => 'banner',
                'sort_order' => 8,
                'content' => ['limit' => 4],
                'media' => [
                    [
                        'title' => 'Verified Professionals',
                        'subtitle' => 'Every professional is screened and verified before going live.',
                        'url' => 'https://via.placeholder.com/240x240?text=Verified',
                        'type' => 'banner',
                    ],
                    [
                        'title' => 'On-Time Arrival',
                        'subtitle' => 'Appointments are scheduled with reliable time windows.',
                        'url' => 'https://via.placeholder.com/240x240?text=On+Time',
                        'type' => 'banner',
                    ],
                    [
                        'title' => 'Premium Products',
                        'subtitle' => 'Service partners use curated kits and branded consumables.',
                        'url' => 'https://via.placeholder.com/240x240?text=Premium',
                        'type' => 'banner',
                    ],
                    [
                        'title' => 'Easy Support',
                        'subtitle' => 'Quick assistance before and after the appointment.',
                        'url' => 'https://via.placeholder.com/240x240?text=Support',
                        'type' => 'banner',
                    ],
                ],
            ],
            [
                'section' => 'trending_packages',
                'name' => 'Trending Packages',
                'title' => 'Packages Customers Love',
                'subtitle' => 'Bundle offers with better value than single bookings.',
                'content_type' => 'static',
                'media_type' => 'banner',
                'sort_order' => 9,
                'content' => ['limit' => 4],
                'media' => [
                    [
                        'title' => 'Bridal Glow Package',
                        'subtitle' => 'Makeup, hairstyle and skin prep in one combo',
                        'url' => 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?q=80&w=900',
                        'type' => 'banner',
                    ],
                    [
                        'title' => 'Weekend Self-Care Pack',
                        'subtitle' => 'Facial, cleanup and relaxing head massage',
                        'url' => 'https://images.unsplash.com/photo-1519415943484-9fa1873496d4?q=80&w=900',
                        'type' => 'banner',
                    ],
                    [
                        'title' => 'Smooth Skin Bundle',
                        'subtitle' => 'Waxing and post-care package with savings',
                        'url' => 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?q=80&w=900',
                        'type' => 'banner',
                    ],
                    [
                        'title' => 'Hair Revival Combo',
                        'subtitle' => 'Cut, spa and styling package for quick refresh',
                        'url' => 'https://images.unsplash.com/photo-1517832606299-7ae9b720a186?q=80&w=900',
                        'type' => 'banner',
                    ],
                ],
            ],
            [
                'section' => 'download_app',
                'name' => 'Download App',
                'title' => 'Book Faster On The App',
                'subtitle' => 'Track professionals, offers and bookings from one place.',
                'content_type' => 'static',
                'media_type' => 'banner',
                'btn_text' => 'Download App',
                'btn_link' => 'https://play.google.com/store',
                'sort_order' => 10,
                'content' => [],
                'media' => [
                    [
                        'title' => 'Bellavella App',
                        'subtitle' => 'Install the app for faster repeat bookings',
                        'url' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?q=80&w=900',
                        'type' => 'banner',
                    ],
                ],
            ],
        ];

        foreach ($sections as $sectionData) {
            $mediaItems = $sectionData['media'] ?? [];
            unset($sectionData['media']);

            $sectionData['status'] = 'Active';
            $sectionData['content'] = $sectionData['content'] ?? [];

            $section = HomepageContent::create($sectionData);

            foreach ($mediaItems as $index => $media) {
                Media::create([
                    'homepage_content_id' => $section->id,
                    'title' => $media['title'],
                    'subtitle' => $media['subtitle'] ?? null,
                    'url' => $media['url'],
                    'target_page' => $media['target_page'] ?? null,
                    'type' => $media['type'],
                    'status' => 'Active',
                    'order' => $index + 1,
                ]);
            }
        }

        $this->command->info('HomepageContentSeeder seeded ' . count(self::SECTION_TYPES) . ' homepage sections.');
    }
}
