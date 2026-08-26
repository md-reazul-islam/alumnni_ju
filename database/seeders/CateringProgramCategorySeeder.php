<?php

namespace Database\Seeders;

use App\Models\CateringProgramCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CateringProgramCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Birthday Party', 'icon' => 'gift', 'description' => 'Cakes, snacks, and party trays for birthday celebrations.'],
            ['name' => 'Picnic', 'icon' => 'sun', 'description' => 'Portable, easy-to-share food for outdoor picnics and day trips.'],
            ['name' => 'Friends Hangout', 'icon' => 'users', 'description' => 'Casual finger food and shareable platters for get-togethers with friends.'],
            ['name' => 'Family Party', 'icon' => 'house', 'description' => 'Home-style spreads sized for family gatherings.'],
            ['name' => 'Wedding Reception', 'icon' => 'heart', 'description' => 'Elegant multi-course catering for wedding receptions.'],
            ['name' => 'Corporate Event', 'icon' => 'briefcase', 'description' => 'Professional catering for office parties, meetings, and conferences.'],
            ['name' => 'Graduation Ceremony', 'icon' => 'graduation-cap', 'description' => 'Celebration catering for graduation ceremonies and convocations.'],
            ['name' => 'Religious Ceremony', 'icon' => 'landmark', 'description' => 'Catering suited for religious gatherings and ceremonies.'],
            ['name' => 'Anniversary Celebration', 'icon' => 'sparkles', 'description' => 'Festive catering for anniversaries and milestone celebrations.'],
            ['name' => 'Farewell Party', 'icon' => 'handshake', 'description' => 'Send-off catering for farewell and goodbye gatherings.'],
        ];

        foreach ($categories as $index => $category) {
            CateringProgramCategory::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'icon' => $category['icon'],
                    'description' => $category['description'],
                    'is_active' => true,
                    'sort_order' => $index,
                ]
            );
        }
    }
}
