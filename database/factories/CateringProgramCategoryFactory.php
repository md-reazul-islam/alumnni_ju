<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\CateringProgramCategory>
 */
class CateringProgramCategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Birthday Party', 'Family Gathering', 'Friends Hangout', 'Picnic',
            'Wedding Reception', 'Corporate Event', 'Graduation Party', 'Holiday Celebration',
        ]) . ' ' . fake()->unique()->numberBetween(1, 100000);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'icon' => 'utensils',
            'description' => fake()->sentence(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
