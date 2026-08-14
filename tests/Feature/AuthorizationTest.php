<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_away_from_the_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_regular_alumnus_cannot_access_the_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_moderator_cannot_access_audit_logs(): void
    {
        $role = Role::firstOrCreate(['slug' => Role::MODERATOR], ['name' => 'Moderator']);
        $moderator = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($moderator)->get(route('admin.audit-logs.index'));

        $response->assertForbidden();
    }

    public function test_super_admin_can_access_audit_logs(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.audit-logs.index'));

        $response->assertOk();
    }

    public function test_suspended_user_is_logged_out_on_next_request(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertOk();

        $user->update(['status' => User::STATUS_SUSPENDED]);

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_pending_alumnus_cannot_reach_the_dashboard(): void
    {
        $user = User::factory()->pending()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('verification.pending'));
    }
}
