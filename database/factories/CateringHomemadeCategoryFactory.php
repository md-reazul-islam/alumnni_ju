<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\CateringHomemadeCategory>
 */
class CateringHomemadeCategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Traditional Bangladeshi', 'Bakery & Desserts', 'Snacks', 'Halal Meals', 'Vegetarian',
        ]) . ' ' . fake()->unique()->numberBetween(1, 100000);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'icon' => 'cooking-pot',
            'commission_percentage' => 10,
            'is_active' => true,
        ];
    }
}
