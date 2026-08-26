<?php

namespace Database\Factories;

use App\Models\CateringHomemadeCategory;
use App\Models\CateringHomemadeListing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CateringHomemadeListing>
 */
class CateringHomemadeListingFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true) . ' ' . fake()->unique()->numberBetween(1, 100000);

        return [
            'user_id' => User::factory(),
            'catering_homemade_category_id' => CateringHomemadeCategory::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 5, 100),
            'price_unit' => fake()->randomElement(['per_item', 'per_box', 'per_dozen', 'total']),
            'status' => CateringHomemadeListing::STATUS_PENDING,
            'is_active' => true,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => CateringHomemadeListing::STATUS_APPROVED,
            'approved_at' => now(),
        ]);
    }
}
