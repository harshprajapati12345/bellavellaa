<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\HomepageContent;
use Illuminate\Database\Seeder;

class HomepageSampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find sections
        $testimonialSection = HomepageContent::where('section', 'testimonials')->first();
        $trendingSection = HomepageContent::where('section', 'trending_packages')->first();
        $downloadSection = HomepageContent::where('section', 'download_app')->first();

        // Add sample testimonials
        if ($testimonialSection) {
            Media::create([
                'homepage_content_id' => $testimonialSection->id,
                'title' => 'Amazing Service!',
                'url' => 'https://via.placeholder.com/100x100?text=JD',
                'type' => 'banner',
                'status' => 'Active',
                'order' => 1,
            ]);

            Media::create([
                'homepage_content_id' => $testimonialSection->id,
                'title' => 'Very Satisfied',
                'url' => 'https://via.placeholder.com/100x100?text=SS',
                'type' => 'banner',
                'status' => 'Active',
                'order' => 2,
            ]);

            Media::create([
                'homepage_content_id' => $testimonialSection->id,
                'title' => 'Excellent Quality',
                'url' => 'https://via.placeholder.com/100x100?text=MJ',
                'type' => 'banner',
                'status' => 'Active',
                'order' => 3,
            ]);
        }

        // Add sample trending packages
        if ($trendingSection) {
            Media::create([
                'homepage_content_id' => $trendingSection->id,
                'title' => 'Premium Home Cleaning',
                'url' => 'https://via.placeholder.com/300x200?text=Home+Cleaning',
                'type' => 'banner',
                'status' => 'Active',
                'order' => 1,
            ]);

            Media::create([
                'homepage_content_id' => $trendingSection->id,
                'title' => 'Professional Plumbing',
                'url' => 'https://via.placeholder.com/300x200?text=Plumbing',
                'type' => 'banner',
                'status' => 'Active',
                'order' => 2,
            ]);

            Media::create([
                'homepage_content_id' => $trendingSection->id,
                'title' => 'Electrical Services',
                'url' => 'https://via.placeholder.com/300x200?text=Electrical',
                'type' => 'banner',
                'status' => 'Active',
                'order' => 3,
            ]);
        }

        // Add download app section media
        if ($downloadSection) {
            Media::create([
                'homepage_content_id' => $downloadSection->id,
                'title' => 'Download App',
                'url' => 'https://via.placeholder.com/400x300?text=Download+App',
                'type' => 'banner',
                'status' => 'Active',
                'order' => 1,
            ]);
        }
    }
}
