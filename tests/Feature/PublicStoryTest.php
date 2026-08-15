<?php

namespace Tests\Feature;

use App\Models\AlumniProfile;
use App\Models\AlumniStory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicStoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_story_page_links_to_a_public_alumnus_profile(): void
    {
        $profile = AlumniProfile::factory()->verified()->public()->create();
        $story = AlumniStory::create([
            'alumni_profile_id' => $profile->id,
            'title' => 'Best Paper Award',
            'slug' => 'best-paper-award',
            'story' => 'A story about winning a best paper award.',
            'status' => AlumniStory::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $response = $this->get(route('stories.show', $story));

        $response->assertOk();
        $response->assertSee(route('alumni.profile.show', $profile->user), false);
    }

    public function test_story_page_does_not_link_to_a_non_public_alumnus_profile(): void
    {
        $profile = AlumniProfile::factory()->verified()->create([
            'profile_visibility' => AlumniProfile::VISIBILITY_ALUMNI,
        ]);
        $story = AlumniStory::create([
            'alumni_profile_id' => $profile->id,
            'title' => 'A Private Journey',
            'slug' => 'a-private-journey',
            'story' => 'A story from an alumnus who keeps their profile private.',
            'status' => AlumniStory::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $response = $this->get(route('stories.show', $story));

        $response->assertOk();
        $response->assertDontSee(route('alumni.profile.show', $profile->user), false);
        $response->assertSee($profile->user->full_name);
    }
}
