<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MasterSeeder::class,
            ServiceHierarchySeeder::class,
            HairColourHierarchySeeder::class,
            CategoryBannerSeeder::class,
            HomepageContentSeeder::class,
            HierarchyBannerSeeder::class,
            ClientHomeSeeder::class,
            ClientTestSeeder::class,
            ScratchCardSeeder::class,
        ]);

    }
}
