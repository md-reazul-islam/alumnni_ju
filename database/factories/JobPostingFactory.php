<?php

namespace Database\Factories;

use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<JobPosting>
 */
class JobPostingFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->jobTitle();

        return [
            'posted_by' => User::factory(),
            'company_name' => fake()->company(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 100000),
            'location' => fake()->city(),
            'employment_type' => 'full_time',
            'description' => fake()->paragraph(),
            'status' => JobPosting::STATUS_PENDING,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => JobPosting::STATUS_APPROVED,
            'approved_at' => now(),
        ]);
    }
}
