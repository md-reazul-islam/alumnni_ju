<?php

namespace Database\Factories;

use App\Models\MarketplaceCategory;
use App\Models\MarketplaceListing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MarketplaceListing>
 */
class MarketplaceListingFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->streetAddress();

        return [
            'user_id' => User::factory(),
            'marketplace_category_id' => MarketplaceCategory::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 100000),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 500, 500000),
            'price_unit' => 'total',
            'address' => fake()->address(),
            'city' => fake()->city(),
            'status' => MarketplaceListing::STATUS_PENDING,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => MarketplaceListing::STATUS_APPROVED,
            'approved_at' => now(),
        ]);
    }
}
