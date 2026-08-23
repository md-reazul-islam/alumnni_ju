<?php

namespace Database\Factories;

use App\Models\CarpoolBooking;
use App\Models\CarpoolSchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CarpoolBooking>
 */
class CarpoolBookingFactory extends Factory
{
    public function definition(): array
    {
        $seats = 1;
        $price = fake()->randomFloat(2, 5, 50);

        return [
            'carpool_schedule_id' => CarpoolSchedule::factory(),
            'passenger_id' => User::factory(),
            'seats' => $seats,
            'seat_price_snapshot' => $price,
            'total_fare' => $seats * $price,
            'status' => CarpoolBooking::STATUS_REQUESTED,
        ];
    }
}
