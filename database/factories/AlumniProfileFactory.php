<?php

namespace Database\Factories;

use App\Models\AlumniProfile;
use App\Models\Degree;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AlumniProfile>
 */
class AlumniProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'student_id' => 'STU-' . fake()->unique()->numberBetween(10000, 99999),
            'department_id' => Department::factory(),
            'degree_id' => Degree::factory(),
            'admission_year' => fake()->numberBetween(2010, 2018),
            'graduation_year' => fake()->numberBetween(2019, 2024),
            'country' => fake()->country(),
            'city' => fake()->city(),
            'job_title' => fake()->jobTitle(),
            'organization' => fake()->company(),
            'industry' => fake()->randomElement(['Technology', 'Finance', 'Healthcare', 'Education']),
            'profile_visibility' => AlumniProfile::VISIBILITY_ALUMNI,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn () => [
            'verified_at' => now(),
        ]);
    }

    public function public(): static
    {
        return $this->state(fn () => [
            'profile_visibility' => AlumniProfile::VISIBILITY_PUBLIC,
        ]);
    }
}
