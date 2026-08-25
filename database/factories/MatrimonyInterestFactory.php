<?php

namespace Database\Factories;

use App\Models\MatrimonyInterest;
use App\Models\MatrimonyProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MatrimonyInterest>
 */
class MatrimonyInterestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'matrimony_profile_id' => MatrimonyProfile::factory(),
            'requested_by' => User::factory(),
            'status' => MatrimonyInterest::STATUS_PENDING,
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn () => [
            'status' => MatrimonyInterest::STATUS_ACCEPTED,
            'responded_at' => now(),
        ]);
    }
}
