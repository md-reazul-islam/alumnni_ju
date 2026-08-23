<?php

namespace Database\Factories;

use App\Models\CarpoolDriverProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\CarpoolCar>
 */
class CarpoolCarFactory extends Factory
{
    public function definition(): array
    {
        return [
            'carpool_driver_profile_id' => CarpoolDriverProfile::factory(),
            'make' => fake()->randomElement(['Toyota', 'Honda', 'Ford', 'Nissan', 'Hyundai']),
            'model' => fake()->word(),
            'year' => fake()->numberBetween(2010, 2026),
            'color' => fake()->safeColorName(),
            'plate_number' => fake()->unique()->bothify('???-####'),
            'total_seats' => fake()->numberBetween(2, 6),
        ];
    }
}
