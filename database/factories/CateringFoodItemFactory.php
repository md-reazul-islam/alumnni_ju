<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\CateringFoodItem>
 */
class CateringFoodItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Chicken Biryani', 'Beef Curry', 'Vegetable Samosa', 'Fish Fry', 'Pulao',
                'Kabab Platter', 'Mixed Salad', 'Fruit Tray', 'Cake', 'Soft Drinks',
            ]) . ' ' . fake()->unique()->numberBetween(1, 100000),
            'description' => fake()->sentence(),
            'base_price' => fake()->randomFloat(2, 5, 50),
            'unit_label' => fake()->randomElement(['per plate', 'per tray', 'per dozen', 'per person']),
            'is_active' => true,
        ];
    }
}
