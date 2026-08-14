<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DegreeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Bachelor of Science', 'Bachelor of Arts', 'Master of Science', 'Master of Business Administration']),
            'abbreviation' => fake()->unique()->lexify('???'),
        ];
    }
}
