<?php

namespace Database\Factories;

use App\Models\CarpoolCar;
use App\Models\CarpoolDriverProfile;
use App\Models\CarpoolSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CarpoolSchedule>
 */
class CarpoolScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'carpool_driver_profile_id' => CarpoolDriverProfile::factory(),
            'carpool_car_id' => CarpoolCar::factory(),
            'origin' => fake()->city(),
            'destination' => fake()->city(),
            'departure_date' => fake()->dateTimeBetween('+1 day', '+30 days')->format('Y-m-d'),
            'departure_time' => fake()->time('H:i'),
            'price_per_seat' => fake()->randomFloat(2, 5, 50),
            'seats_offered' => fake()->numberBetween(1, 4),
            'status' => CarpoolSchedule::STATUS_PENDING,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => CarpoolSchedule::STATUS_APPROVED,
            'approved_at' => now(),
        ]);
    }
}
