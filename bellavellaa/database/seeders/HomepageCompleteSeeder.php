<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HomepageContent;
use App\Models\Media;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomepageCompleteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Cleanup existing homepage data
        Media::whereNotNull('homepage_content_id')->delete();
        HomepageContent::query()->delete();

        // 2. Ensure we have featured Categories and Services
        Category::query()->update(['featured' => false]);
        Category::limit(6)->update(['featured' => true]);

        Service::query()->update(['featured' => false, 'bookings' => 0]);
        Service::limit(10)->update(['featured' => true, 'bookings' => rand(50, 200)]);

        // 3. Define Sections
        $sections = [
            [
                'section'      => 'hero_banner',
                'name'         => 'Main Hero',
                'title'        => 'Professional Services at Your Doorstep',
                'subtitle'     => 'Book top-rated experts for all your needs',
                'content_type' => 'dynamic',
                'media_type'   => 'banner',
                'sort_order'   => 1,
                'media'        => [
                    [
                        'title' => 'Salon for Women',
                        'url'   => 'https://images.unsplash.com/photo-1560750588-73207b1ef5b8?q=80&w=1200',
                        'type'  => 'banner',
                    ],
                    [
                        'title' => 'Home Cleaning',
                        'url'   => 'https://images.unsplash.com/photo-1581578731548-c64695ce6958?q=80&w=1200',
                        'type'  => 'banner',
                    ],
                    [
                        'title' => 'Massage for Men',
                        'url'   => 'https://images.unsplash.com/photo-1544161515-4af62f4b92ba?q=80&w=1200',
                        'type'  => 'banner',
                    ],
                ],
            ],
            [
                'section'      => 'category_carousel',
                'name'         => 'Top Categories',
                'title'        => 'What are you looking for?',
                'subtitle'     => 'Explore our range of professional services',
                'content_type' => 'dynamic',
                'media_type'   => 'icon',
                'sort_order'   => 2,
                'content'      => ['limit' => 8, 'featured_only' => true],
            ],
            [
                'section'      => 'service_grid',
                'name'         => 'Featured Services',
                'title'        => 'Most Popular Services',
                'subtitle'     => 'Handpicked services for you',
                'content_type' => 'dynamic',
                'data_source'  => 'featured_services',
                'media_type'   => 'card',
                'sort_order'   => 3,
                'content'      => ['limit' => 4],
            ],
            [
                'section'      => 'image_banner',
                'name'         => 'Special Promo',
                'title'        => 'Summer Glow Sale',
                'subtitle'     => 'Get up to 50% off on all facials',
                'content_type' => 'static',
                'media_type'   => 'banner',
                'sort_order'   => 4,
                'btn_text'     => 'Book Now',
                'btn_link'     => '/categories/salon-for-women',
                'media'        => [
                    [
                        'title' => '50% Off Facials',
                        'url'   => 'https://images.unsplash.com/photo-1512290923902-8a9f81dc2069?q=80&w=1200',
                        'type'  => 'banner',
                    ],
                ],
            ],
            [
                'section'      => 'video_stories',
                'name'         => 'Customer Stories',
                'title'        => 'Hear from our customers',
                'subtitle'     => 'Real experiences, real results',
                'content_type' => 'dynamic',
                'media_type'   => 'video',
                'sort_order'   => 5,
                'media'        => [
                    [
                        'title' => 'Amazing Experience',
                        'url'   => 'https://assets.mixkit.co/videos/preview/mixkit-girl-in-white-t-shirt-doing-her-hair-40040-large.mp4',
                        'type'  => 'video',
                    ],
                    [
                        'title' => 'Professional & Punctual',
                        'url'   => 'https://assets.mixkit.co/videos/preview/mixkit-woman-cleaning-the-floor-with-a-mop-40151-large.mp4',
                        'type'  => 'video',
                    ],
                    [
                        'title' => 'Highly Recommended',
                        'url'   => 'https://assets.mixkit.co/videos/preview/mixkit-young-woman-applying-makeup-to-her-face-40038-large.mp4',
                        'type'  => 'video',
                    ],
                ],
            ],
            [
                'section'      => 'service_carousel',
                'name'         => 'Trending Services',
                'title'        => 'Trending this week',
                'subtitle'     => 'What everyone is booking right now',
                'content_type' => 'dynamic',
                'data_source'  => 'trending',
                'media_type'   => 'card',
                'sort_order'   => 6,
                'content'      => ['limit' => 6],
            ],
            [
                'section'      => 'testimonials',
                'name'         => 'Trust & Safety',
                'title'        => 'Why choose BellaVella?',
                'subtitle'     => 'Over 1 Million happy customers',
                'content_type' => 'dynamic',
                'media_type'   => 'banner',
                'sort_order'   => 7,
                'media'        => [
                    [
                        'title' => 'Verified Professionals',
                        'url'   => 'https://via.placeholder.com/100x100?text=Expert',
                        'type'  => 'banner',
                        'description' => 'All our experts go through rigorous background checks.',
                    ],
                    [
                        'title' => 'Safe & Secure',
                        'url'   => 'https://via.placeholder.com/100x100?text=Safe',
                        'type'  => 'banner',
                        'description' => 'Contactless services and premium safety protocols.',
                    ],
                ],
            ],
            [
                'section'      => 'trending_packages',
                'name'         => 'Value Packs',
                'title'        => 'Save more with Packages',
                'subtitle'     => 'Curated bundles for maximum savings',
                'content_type' => 'dynamic',
                'media_type'   => 'banner',
                'sort_order'   => 8,
                'media'        => [
                    [
                        'title' => 'Full Home Makeover',
                        'url'   => 'https://images.unsplash.com/photo-1527515545081-5db817172677?q=80&w=600',
                        'type'  => 'banner',
                    ],
                    [
                        'title' => 'Bridal Party Pack',
                        'url'   => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?q=80&w=600',
                        'type'  => 'banner',
                    ],
                ],
            ],
            [
                'section'      => 'download_app',
                'name'         => 'Mobile App',
                'title'        => 'Get the BellaVella App',
                'subtitle'     => 'Book services in seconds and track your professional',
                'content_type' => 'static',
                'media_type'   => 'banner',
                'sort_order'   => 9,
                'media'        => [
                    [
                        'title' => 'Download Now',
                        'url'   => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?q=80&w=800',
                        'type'  => 'banner',
                    ],
                ],
            ],
        ];

        // 4. Create Records
        foreach ($sections as $data) {
            $mediaItems = $data['media'] ?? [];
            unset($data['media']);

            // Convert content array to JSON string for the DB insert
            if (isset($data['content'])) {
                $data['content'] = json_encode($data['content']);
            } else {
                $data['content'] = json_encode([]);
            }

            $content = HomepageContent::create($data);

            foreach ($mediaItems as $idx => $m) {
                Media::create([
                    'homepage_content_id' => $content->id,
                    'title'               => $m['title'],
                    'subtitle'            => $m['subtitle'] ?? ($m['description'] ?? null),
                    'url'                 => $m['url'],
                    'type'                => $m['type'],
                    'status'              => 'Active',
                    'order'               => $idx + 1,
                ]);
            }
        }

        echo "Homepage Seeding Completed Successfully!" . PHP_EOL;
    }
}
