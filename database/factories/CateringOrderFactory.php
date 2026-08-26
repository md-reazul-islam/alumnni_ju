<?php

namespace Database\Factories;

use App\Models\CateringOrder;
use App\Models\CateringProgramCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CateringOrder>
 */
class CateringOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => User::factory(),
            'catering_program_category_id' => CateringProgramCategory::factory(),
            'event_date' => fake()->dateTimeBetween('+3 days', '+60 days')->format('Y-m-d'),
            'guest_count' => fake()->numberBetween(10, 200),
            'delivery_address' => fake()->address(),
            'contact_phone' => fake()->phoneNumber(),
            'notes' => fake()->optional()->sentence(),
            'status' => CateringOrder::STATUS_SUBMITTED,
        ];
    }

    public function priced(): static
    {
        return $this->state(fn () => [
            'status' => CateringOrder::STATUS_PRICED,
            'subtotal' => 500,
            'tax_percentage_snapshot' => 5,
            'tax_amount' => 25,
            'vat_percentage_snapshot' => 5,
            'vat_amount' => 25,
            'service_fee_percentage_snapshot' => 10,
            'service_fee_amount' => 50,
            'total_amount' => 600,
            'priced_at' => now(),
        ]);
    }
}
