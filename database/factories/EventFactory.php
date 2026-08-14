<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'organizer_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 100000),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement(['reunion', 'networking', 'workshop', 'webinar']),
            'mode' => 'offline',
            'venue' => fake()->company() . ' Hall',
            'city' => fake()->city(),
            'country' => fake()->country(),
            'event_date' => fake()->dateTimeBetween('now', '+3 months'),
            'status' => Event::STATUS_PUBLISHED,
            'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => Event::STATUS_DRAFT, 'published_at' => null]);
    }
}
