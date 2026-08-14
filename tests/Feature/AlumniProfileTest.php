<?php

namespace Tests\Feature;

use App\Models\AlumniProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlumniProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_directory_lists_public_profiles_to_guests(): void
    {
        $profile = AlumniProfile::factory()->verified()->public()->create();

        $response = $this->get(route('alumni.directory'));

        $response->assertOk();
        $response->assertSee($profile->user->full_name);
    }

    public function test_directory_search_filters_by_graduation_year(): void
    {
        $matching = AlumniProfile::factory()->verified()->public()->create(['graduation_year' => 2020]);
        $other = AlumniProfile::factory()->verified()->public()->create(['graduation_year' => 2015]);

        $response = $this->get(route('alumni.directory', ['graduation_year' => 2020]));

        $response->assertOk();
        $response->assertSee($matching->user->full_name);
        $response->assertDontSee($other->user->full_name);
    }

    public function test_private_profile_is_not_visible_to_guests(): void
    {
        $profile = AlumniProfile::factory()->verified()->create(['profile_visibility' => AlumniProfile::VISIBILITY_PRIVATE]);

        $response = $this->get(route('alumni.profile.show', $profile->user));

        $response->assertForbidden();
    }

    public function test_alumni_can_update_their_own_profile(): void
    {
        $profile = AlumniProfile::factory()->verified()->create();

        $response = $this->actingAs($profile->user)->patch(route('alumni.profile.update'), [
            'bio' => 'A short bio about me.',
            'profile_visibility' => AlumniProfile::VISIBILITY_ALUMNI,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame('A short bio about me.', $profile->fresh()->bio);
    }

    public function test_admin_can_verify_a_pending_alumnus(): void
    {
        $admin = User::factory()->admin()->create();
        $profile = AlumniProfile::factory()->create(['verified_at' => null]);
        $profile->user->update(['status' => User::STATUS_PENDING]);

        $response = $this->actingAs($admin)->post(route('admin.alumni.verify', $profile->user));

        $response->assertRedirect();
        $this->assertTrue($profile->user->fresh()->isVerified());
        $this->assertNotNull($profile->fresh()->verified_at);
    }
}
