<?php

namespace Database\Factories;

use App\Models\PublishedMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PublishedMedia>
 */
class PublishedMediaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'type' => PublishedMedia::TYPE_IMAGE,
            'tag' => fake()->randomElement(['product promotion', 'banner', 'poster', 'news', 'blog']),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function video(): static
    {
        return $this->state(fn () => [
            'type' => PublishedMedia::TYPE_VIDEO,
            'image' => null,
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);
    }
}
