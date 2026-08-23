<?php

namespace Database\Factories;

use App\Models\CarpoolDriverProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CarpoolDriverProfile>
 */
class CarpoolDriverProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'license_number' => fake()->unique()->bothify('DL-########'),
            'status' => CarpoolDriverProfile::STATUS_PENDING,
            'is_active' => true,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => CarpoolDriverProfile::STATUS_APPROVED,
            'reviewed_at' => now(),
        ]);
    }
}
