<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_the_dashboard_with_module_reports(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Module Reports');
        $response->assertSee('Career & Jobs');
        $response->assertSee('Marketplace');
        $response->assertSee('Matrimony');
        $response->assertSee('Catering');
        $response->assertSee('Media Advocacy');
        $response->assertSee('Mentorship');
        $response->assertSee('Library');
        $response->assertSee('Donations');
        $response->assertViewHas('modules');
    }

    public function test_dashboard_modules_contain_the_expected_stat_keys(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $modules = $response->viewData('modules');

        $this->assertArrayHasKey('career', $modules);
        $this->assertArrayHasKey('marketplace', $modules);
        $this->assertArrayHasKey('matrimony', $modules);
        $this->assertArrayHasKey('catering', $modules);
        $this->assertArrayHasKey('media_advocacy', $modules);
        $this->assertArrayHasKey('mentorship', $modules);
        $this->assertArrayHasKey('library', $modules);
        $this->assertArrayHasKey('donations', $modules);

        foreach ($modules as $module) {
            $this->assertArrayHasKey('stats', $module);
            $this->assertArrayHasKey('chart', $module);
        }
    }

    public function test_guest_cannot_view_the_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect();
    }

    public function test_non_admin_cannot_view_the_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }
}
