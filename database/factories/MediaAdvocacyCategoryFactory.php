<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\MediaAdvocacyCategory>
 */
class MediaAdvocacyCategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'TV News', 'Newspaper News', 'Story Publish', 'Facebook Marketing',
            'Product Promotion Video', 'Banner Design', 'Poster Design', 'Logo Design',
        ]) . ' ' . fake()->unique()->numberBetween(1, 100000);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'icon' => 'megaphone',
            'description' => fake()->sentence(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
