<?php

namespace Tests\Feature;

use App\Models\AlumniProfile;
use App\Models\AlumniStory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_published_story_for_an_alumnus(): void
    {
        $admin = User::factory()->admin()->create();
        $profile = AlumniProfile::factory()->verified()->create();

        $response = $this->actingAs($admin)->post(route('admin.stories.store'), [
            'alumni_profile_id' => $profile->id,
            'title' => 'From Intern to Director',
            'story' => 'A long story about career growth.',
            'status' => AlumniStory::STATUS_PUBLISHED,
        ]);

        $response->assertRedirect(route('admin.stories.index'));
        $this->assertDatabaseHas('alumni_stories', [
            'title' => 'From Intern to Director',
            'alumni_profile_id' => $profile->id,
            'status' => 'published',
        ]);

        $story = AlumniStory::where('title', 'From Intern to Director')->first();
        $this->assertNotNull($story->published_at);
        $this->assertSame($admin->id, $story->reviewed_by);
    }

    public function test_non_admin_cannot_create_a_story(): void
    {
        $user = User::factory()->create();
        $profile = AlumniProfile::factory()->verified()->create();

        $response = $this->actingAs($user)->post(route('admin.stories.store'), [
            'alumni_profile_id' => $profile->id,
            'title' => 'Should Not Save',
            'story' => 'Body text.',
            'status' => AlumniStory::STATUS_PUBLISHED,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('alumni_stories', ['title' => 'Should Not Save']);
    }

    public function test_admin_can_update_a_story(): void
    {
        $admin = User::factory()->admin()->create();
        $profile = AlumniProfile::factory()->verified()->create();
        $story = AlumniStory::create([
            'alumni_profile_id' => $profile->id,
            'title' => 'Original Title',
            'slug' => 'original-title',
            'story' => 'Original story body.',
            'status' => AlumniStory::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.stories.update', $story), [
            'alumni_profile_id' => $profile->id,
            'title' => 'Updated Title',
            'story' => 'Updated story body.',
            'status' => AlumniStory::STATUS_PUBLISHED,
        ]);

        $response->assertRedirect(route('admin.stories.index'));
        $story->refresh();
        $this->assertSame('Updated Title', $story->title);
        $this->assertSame('published', $story->status);
        $this->assertNotNull($story->published_at);
    }

    public function test_admin_can_delete_a_story(): void
    {
        $admin = User::factory()->admin()->create();
        $profile = AlumniProfile::factory()->verified()->create();
        $story = AlumniStory::create([
            'alumni_profile_id' => $profile->id,
            'title' => 'To Be Deleted',
            'slug' => 'to-be-deleted',
            'story' => 'Body text.',
            'status' => AlumniStory::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.stories.destroy', $story));

        $response->assertRedirect();
        $this->assertSoftDeleted($story);
    }
}
