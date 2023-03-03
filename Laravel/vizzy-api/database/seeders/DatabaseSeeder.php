<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $categories = [
            'Arts & Entertainment',
            'Business and Technology',
            'Comedy',
            'Educational',
            'Games',
            'Lifestyle and Health',
            'Music',
            'News and Politics',
            'Sport and Recreation',
            'Fiction',
            'True Crime',
            'Kids and Family'            
        ];
        foreach ($categories as $category) {
            $cat = new \App\Models\PodcastCategory();
            $cat->name = $category;
            $cat->image = '';
            $cat->save();
        }
    }
}
