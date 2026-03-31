<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryBanner;
use Illuminate\Database\Seeder;

class CategoryBannerSeeder extends Seeder
{
    public function run(): void
    {
        $bannerMap = [
            'salon-for-women' => [
                [
                    'title' => 'Salon At Home',
                    'subtitle' => 'Facials, waxing and threading by trained professionals',
                    'image' => 'category-banners/salon-home-hero.jpg',
                    'link_url' => '/categories/salon-for-women',
                    'banner_type' => 'slider',
                    'sort_order' => 1,
                ],
                [
                    'title' => 'Luxe Beauty Sessions',
                    'subtitle' => 'Premium skin and beauty treatments with better products',
                    'image' => 'category-banners/salon-luxe-slider.jpg',
                    'link_url' => '/categories/salon-for-women?page=luxe',
                    'banner_type' => 'slider',
                    'sort_order' => 2,
                ],
                [
                    'title' => 'Flat 20% Off On Facials',
                    'subtitle' => 'Limited-time promo for glow and cleanup services',
                    'image' => 'category-banners/salon-promo-facial.jpg',
                    'link_url' => '/categories/salon-for-women',
                    'banner_type' => 'promo',
                    'sort_order' => 1,
                ],
            ],
            'spa-for-women' => [
                [
                    'title' => 'Spa Calm At Home',
                    'subtitle' => 'Massage and relaxation experiences without leaving home',
                    'image' => 'category-banners/spa-hero.jpg',
                    'link_url' => '/categories/spa-for-women',
                    'banner_type' => 'slider',
                    'sort_order' => 3,
                ],
                [
                    'title' => 'Ayurveda Wellness',
                    'subtitle' => 'Traditional spa rituals focused on recovery and balance',
                    'image' => 'category-banners/spa-ayurveda-slider.jpg',
                    'link_url' => '/categories/spa-for-women?page=ayurveda',
                    'banner_type' => 'slider',
                    'sort_order' => 4,
                ],
                [
                    'title' => 'De-Stress Week Offer',
                    'subtitle' => 'Special pricing on selected massage services',
                    'image' => 'category-banners/spa-promo-offer.jpg',
                    'link_url' => '/categories/spa-for-women',
                    'banner_type' => 'promo',
                    'sort_order' => 2,
                ],
            ],
            'hair-studio-for-women' => [
                [
                    'title' => 'Hair Studio At Home',
                    'subtitle' => 'Cuts, styling and treatments by beauty experts',
                    'image' => 'category-banners/hair-studio-hero.jpg',
                    'link_url' => '/categories/hair-studio-for-women',
                    'banner_type' => 'slider',
                    'sort_order' => 5,
                ],
                [
                    'title' => 'Smoothening And Care',
                    'subtitle' => 'Hair spa and treatment combos curated for quick booking',
                    'image' => 'category-banners/hair-studio-slider.jpg',
                    'link_url' => '/categories/hair-studio-for-women',
                    'banner_type' => 'slider',
                    'sort_order' => 6,
                ],
                [
                    'title' => 'Style Refresh Promo',
                    'subtitle' => 'Book styling services and save on add-ons',
                    'image' => 'category-banners/hair-studio-promo.jpg',
                    'link_url' => '/categories/hair-studio-for-women',
                    'banner_type' => 'promo',
                    'sort_order' => 3,
                ],
            ],
            'bridal' => [
                [
                    'title' => 'Bridal Booking Window Open',
                    'subtitle' => 'Reserve makeup, hair and skincare packages early',
                    'image' => 'category-banners/bridal-hero.jpg',
                    'link_url' => '/categories/bridal',
                    'banner_type' => 'slider',
                    'sort_order' => 7,
                ],
                [
                    'title' => 'Wedding Season Packages',
                    'subtitle' => 'Curated bridal looks for family events and ceremonies',
                    'image' => 'category-banners/bridal-slider.jpg',
                    'link_url' => '/categories/bridal',
                    'banner_type' => 'slider',
                    'sort_order' => 8,
                ],
                [
                    'title' => 'Trial Session Offer',
                    'subtitle' => 'Reduced pricing on pre-event bridal consultation slots',
                    'image' => 'category-banners/bridal-promo.jpg',
                    'link_url' => '/categories/bridal',
                    'banner_type' => 'promo',
                    'sort_order' => 4,
                ],
            ],
        ];

        foreach ($bannerMap as $categorySlug => $banners) {
            $category = Category::query()->where('slug', $categorySlug)->first();

            if (!$category) {
                continue;
            }

            foreach ($banners as $banner) {
                CategoryBanner::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'title' => $banner['title'],
                    ],
                    [
                        'subtitle' => $banner['subtitle'],
                        'image' => $banner['image'],
                        'link_url' => $banner['link_url'],
                        'banner_type' => $banner['banner_type'],
                        'sort_order' => $banner['sort_order'],
                        'status' => 'Active',
                    ]
                );
            }
        }

        $this->command->info('CategoryBannerSeeder seeded category banner demo data.');
    }
}
