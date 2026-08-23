<?php

namespace Database\Factories;

use App\Models\CarpoolBooking;
use App\Models\CarpoolPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CarpoolPayment>
 */
class CarpoolPaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'carpool_booking_id' => CarpoolBooking::factory(),
            'amount' => fake()->randomFloat(2, 5, 50),
            'status' => CarpoolPayment::STATUS_PENDING,
            'stripe_checkout_session_id' => 'cs_test_' . fake()->unique()->bothify('??????????????'),
        ];
    }
}
