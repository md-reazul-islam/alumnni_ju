<?php

namespace Database\Factories;

use App\Models\MatrimonyProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MatrimonyProfile>
 */
class MatrimonyProfileFactory extends Factory
{
    public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female']);

        return [
            'created_by' => User::factory(),
            'managed_by_relation' => 'self',
            'full_name' => $gender === 'male' ? fake()->firstNameMale() . ' ' . fake()->lastName() : fake()->firstNameFemale() . ' ' . fake()->lastName(),
            'display_name' => fake()->firstName($gender),
            'gender' => $gender,
            'date_of_birth' => fake()->dateTimeBetween('-40 years', '-20 years')->format('Y-m-d'),
            'marital_status' => 'never_married',
            'religion' => fake()->randomElement(['Islam', 'Hinduism', 'Christianity', 'Other']),
            'nationality' => fake()->randomElement(['Bangladeshi', 'American']),
            'country' => fake()->randomElement(['USA', 'Bangladesh']),
            'city' => fake()->randomElement(['Albany', 'New York', 'Dhaka', 'Chittagong']),
            'education_level' => fake()->randomElement(["Bachelor's", "Master's", 'PhD']),
            'occupation' => fake()->jobTitle(),
            'about_me' => fake()->paragraph(),
            'photo_visibility' => 'private',
            'status' => MatrimonyProfile::STATUS_DRAFT,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => MatrimonyProfile::STATUS_PENDING,
            'profile_completion' => 100,
            'terms_accepted_at' => now(),
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => MatrimonyProfile::STATUS_APPROVED,
            'profile_completion' => 100,
            'terms_accepted_at' => now(),
            'reviewed_at' => now(),
        ]);
    }
}
