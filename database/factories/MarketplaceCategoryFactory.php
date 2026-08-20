<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\MarketplaceCategory>
 */
class MarketplaceCategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 100000),
            'commission_percentage' => fake()->randomFloat(2, 0, 15),
            'is_active' => true,
        ];
    }
}
