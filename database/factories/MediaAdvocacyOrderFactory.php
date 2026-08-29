<?php

namespace Database\Factories;

use App\Models\MediaAdvocacyCategory;
use App\Models\MediaAdvocacyOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MediaAdvocacyOrder>
 */
class MediaAdvocacyOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => User::factory(),
            'media_advocacy_category_id' => MediaAdvocacyCategory::factory(),
            'status' => MediaAdvocacyOrder::STATUS_PENDING,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn () => [
            'status' => MediaAdvocacyOrder::STATUS_CONFIRMED,
            'final_price' => fake()->randomFloat(2, 50, 2000),
        ]);
    }
}
