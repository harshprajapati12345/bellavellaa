<?php

namespace Database\Seeders;

use App\Models\HomepageContent;
use App\Models\Media;
use Illuminate\Database\Seeder;

/**
 * ClientHomeSeeder
 *
 * Seeds all sections for the client home screen:
 * hero_banner, category_carousel, service_carousel,
 * video_stories, image_banner, active_booking,
 * testimonials, trending_packages, download_app
 *
 * Run: php artisan db:seed --class=ClientHomeSeeder
 */
class ClientHomeSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing homepage content and related media
        Media::query()->delete();
        HomepageContent::query()->delete();

        $order = 1;

        // ─────────────────────────────────────────────────────────────────
        // 1. Hero Banner
        // ─────────────────────────────────────────────────────────────────
        $heroBanner = HomepageContent::create([
            'section'      => 'hero_banner',
            'name'         => 'Hero Banner',
            'title'        => 'Premium Beauty at Your Doorstep',
            'subtitle'     => 'Book top-rated beauty professionals in minutes',
            'content_type' => 'dynamic',
            'media_type'   => 'banner',
            'status'       => 'Active',
            'sort_order'   => $order++,
            'content'      => [],
        ]);

        // Hero banner slides
        $heroBanners = [
            [
                'title'       => 'Book a Facial',
                'subtitle'    => 'Glow up at home',
                'url'         => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=800',
                'target_page' => 'services',
            ],
            [
                'title'       => 'Bridal Makeup',
                'subtitle'    => 'Look stunning on your big day',
                'url'         => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=800',
                'target_page' => 'packages',
            ],
            [
                'title'       => 'Hair Styling',
                'subtitle'    => 'Transform your look today',
                'url'         => 'https://images.unsplash.com/photo-1519699047748-de8e457a634e?w=800',
                'target_page' => 'services',
            ],
        ];

        foreach ($heroBanners as $i => $banner) {
            Media::create([
                'homepage_content_id' => $heroBanner->id,
                'title'               => $banner['title'],
                'subtitle'            => $banner['subtitle'],
                'url'                 => $banner['url'],
                'target_page'         => $banner['target_page'],
                'type'                => 'banner',
                'status'              => 'Active',
                'order'               => $i + 1,
            ]);
        }

        // ─────────────────────────────────────────────────────────────────
        // 2. Active Booking Banner (no media rows needed)
        // ─────────────────────────────────────────────────────────────────
        HomepageContent::create([
            'section'      => 'active_booking',
            'name'         => 'Active Booking',
            'title'        => 'Your Active Booking',
            'subtitle'     => 'Track your current appointment',
            'content_type' => 'dynamic',
            'media_type'   => 'banner',
            'status'       => 'Active',
            'sort_order'   => $order++,
            'content'      => [],
        ]);

        // ─────────────────────────────────────────────────────────────────
        // 3. Category Carousel
        // ─────────────────────────────────────────────────────────────────
        $categorySection = HomepageContent::create([
            'section'      => 'category_carousel',
            'name'         => 'Category Carousel',
            'title'        => 'Our Services',
            'subtitle'     => 'Find the perfect beauty treatment',
            'content_type' => 'dynamic',
            'media_type'   => 'banner',
            'status'       => 'Active',
            'sort_order'   => $order++,
            'content'      => [],
        ]);

        $categories = [
            ['title' => 'Facial',       'url' => 'https://images.unsplash.com/photo-1516975080664-ed2fc6a32937?w=300'],
            ['title' => 'Hair Care',    'url' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=300'],
            ['title' => 'Makeup',       'url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=300'],
            ['title' => 'Waxing',       'url' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=300'],
            ['title' => 'Nail Art',     'url' => 'https://images.unsplash.com/photo-1604654894610-df63bc536371?w=300'],
            ['title' => 'Massage',      'url' => 'https://images.unsplash.com/photo-1519823551278-64ac92734fb1?w=300'],
        ];

        foreach ($categories as $i => $cat) {
            Media::create([
                'homepage_content_id' => $categorySection->id,
                'title'               => $cat['title'],
                'url'                 => $cat['url'],
                'type'                => 'banner',
                'status'              => 'Active',
                'order'               => $i + 1,
            ]);
        }

        // ─────────────────────────────────────────────────────────────────
        // 4. Service Carousel — Featured Services
        // ─────────────────────────────────────────────────────────────────
        $serviceSection = HomepageContent::create([
            'section'      => 'service_carousel',
            'name'         => 'Service Carousel',
            'title'        => 'Featured Services',
            'subtitle'     => 'Hand-picked for you',
            'content_type' => 'dynamic',
            'media_type'   => 'banner',
            'status'       => 'Active',
            'sort_order'   => $order++,
            'content'      => [],
        ]);

        $services = [
            ['title' => 'Glow Facial',          'subtitle' => 'Hydration + Glow treatment',        'url' => 'https://images.unsplash.com/photo-1516975080664-ed2fc6a32937?w=400'],
            ['title' => 'Bridal Package',       'subtitle' => 'Complete bridal beauty package',     'url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=400'],
            ['title' => 'Hair Smoothening',     'subtitle' => 'Frizz-free silky smooth hair',       'url' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=400'],
            ['title' => 'Pedicure + Manicure',  'subtitle' => 'Pamper your hands and feet',         'url' => 'https://images.unsplash.com/photo-1604654894610-df63bc536371?w=400'],
        ];

        foreach ($services as $i => $svc) {
            Media::create([
                'homepage_content_id' => $serviceSection->id,
                'title'               => $svc['title'],
                'subtitle'            => $svc['subtitle'],
                'url'                 => $svc['url'],
                'type'                => 'banner',
                'status'              => 'Active',
                'order'               => $i + 1,
            ]);
        }

        // ─────────────────────────────────────────────────────────────────
        // 5. Image Banner (Promo)
        // ─────────────────────────────────────────────────────────────────
        $imageBannerSection = HomepageContent::create([
            'section'      => 'image_banner',
            'name'         => 'Image Banner',
            'title'        => '30% Off Bridal Packages',
            'subtitle'     => 'Book now and save big on your special day',
            'content_type' => 'dynamic',
            'media_type'   => 'banner',
            'status'       => 'Active',
            'sort_order'   => $order++,
            'content'      => [],
        ]);

        Media::create([
            'homepage_content_id' => $imageBannerSection->id,
            'title'               => '30% Off Bridal Packages',
            'subtitle'            => 'Book now and save big on your special day',
            'url'                 => 'https://images.unsplash.com/photo-1522338242992-e1a54906a8da?w=800',
            'target_page'         => 'packages',
            'type'                => 'banner',
            'status'              => 'Active',
            'order'               => 1,
        ]);

        // ─────────────────────────────────────────────────────────────────
        // 6. Trending Packages
        // ─────────────────────────────────────────────────────────────────
        $trendingSection = HomepageContent::create([
            'section'      => 'trending_packages',
            'name'         => 'Trending Packages',
            'title'        => 'Trending Right Now',
            'subtitle'     => 'Most booked this week',
            'content_type' => 'dynamic',
            'media_type'   => 'banner',
            'status'       => 'Active',
            'sort_order'   => $order++,
            'content'      => [],
        ]);

        $trending = [
            ['title' => 'Full Body Wax',         'subtitle' => '₹999',   'url' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=400'],
            ['title' => 'Party Makeup',           'subtitle' => '₹1,499', 'url' => 'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?w=400'],
            ['title' => 'Hair Spa + Treatment',  'subtitle' => '₹799',   'url' => 'https://images.unsplash.com/photo-1519699047748-de8e457a634e?w=400'],
            ['title' => 'Gel Nail Art',           'subtitle' => '₹599',   'url' => 'https://images.unsplash.com/photo-1604654894610-df63bc536371?w=400'],
        ];

        foreach ($trending as $i => $pkg) {
            Media::create([
                'homepage_content_id' => $trendingSection->id,
                'title'               => $pkg['title'],
                'subtitle'            => $pkg['subtitle'],
                'url'                 => $pkg['url'],
                'type'                => 'banner',
                'status'              => 'Active',
                'order'               => $i + 1,
            ]);
        }

        // ─────────────────────────────────────────────────────────────────
        // 7. Testimonials
        // ─────────────────────────────────────────────────────────────────
        $testimonialSection = HomepageContent::create([
            'section'      => 'testimonials',
            'name'         => 'Testimonials',
            'title'        => 'What Our Clients Say',
            'subtitle'     => 'Real experiences from real customers',
            'content_type' => 'dynamic',
            'media_type'   => 'banner',
            'status'       => 'Active',
            'sort_order'   => $order++,
            'content'      => [],
        ]);

        $testimonials = [
            ['title' => 'Priya Sharma',    'subtitle' => '⭐⭐⭐⭐⭐ Absolutely loved the facial! Skin is glowing.',       'url' => 'https://i.pravatar.cc/100?img=1'],
            ['title' => 'Anita Mehta',     'subtitle' => '⭐⭐⭐⭐⭐ Bridal makeup was perfect. Will book again!',          'url' => 'https://i.pravatar.cc/100?img=2'],
            ['title' => 'Sneha Patel',     'subtitle' => '⭐⭐⭐⭐ Very professional team. On time and meticulous.',        'url' => 'https://i.pravatar.cc/100?img=3'],
            ['title' => 'Ritu Agarwal',    'subtitle' => '⭐⭐⭐⭐⭐ Hair smoothening was flawless. Highly recommended!',   'url' => 'https://i.pravatar.cc/100?img=4'],
        ];

        foreach ($testimonials as $i => $t) {
            Media::create([
                'homepage_content_id' => $testimonialSection->id,
                'title'               => $t['title'],
                'subtitle'            => $t['subtitle'],
                'url'                 => $t['url'],
                'type'                => 'banner',
                'status'              => 'Active',
                'order'               => $i + 1,
            ]);
        }

        // ─────────────────────────────────────────────────────────────────
        // 8. Download App Section
        // ─────────────────────────────────────────────────────────────────
        $downloadSection = HomepageContent::create([
            'section'      => 'download_app',
            'name'         => 'Download App',
            'title'        => 'Get the Bellavella App',
            'subtitle'     => 'Book beauty services anytime, anywhere',
            'content_type' => 'dynamic',
            'media_type'   => 'banner',
            'btn_text'     => 'Download Now',
            'btn_link'     => 'https://play.google.com',
            'status'       => 'Active',
            'sort_order'   => $order++,
            'content'      => [],
        ]);

        Media::create([
            'homepage_content_id' => $downloadSection->id,
            'title'               => 'Download Bellavella',
            'url'                 => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=600',
            'type'                => 'banner',
            'status'              => 'Active',
            'order'               => 1,
        ]);

        $this->command->info('✅ ClientHomeSeeder completed — ' . ($order - 1) . ' sections created!');
    }
}
