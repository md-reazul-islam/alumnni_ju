<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DepartmentFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Computer Science', 'Business Administration', 'Electrical Engineering',
            'Mechanical Engineering', 'Economics', 'Law', 'Medicine', 'Architecture',
            'Psychology', 'Fine Arts',
        ]) . ' ' . fake()->unique()->numberBetween(1, 10000);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
