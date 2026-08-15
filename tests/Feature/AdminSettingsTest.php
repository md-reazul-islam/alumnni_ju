<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_institution_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->put(route('admin.settings.institution'), [
            'name' => 'Springfield State University',
            'email' => 'info@springfield.edu',
            'phone' => '555-0100',
            'address' => '1 University Way',
            'website' => 'https://springfield.edu',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertSame('Springfield State University', Setting::get('institution', 'name'));
        $this->assertSame('info@springfield.edu', Setting::get('institution', 'email'));
    }

    public function test_admin_can_update_association_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->put(route('admin.settings.association'), [
            'name' => 'Springfield Alumni Association',
            'description' => 'Connecting graduates worldwide.',
            'contact_email' => 'assoc@springfield.edu',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertSame('Springfield Alumni Association', Setting::get('association', 'name'));
    }

    public function test_updating_association_does_not_reset_which_tab_is_shown(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->put(route('admin.settings.association'), [
            'name' => 'Springfield Alumni Association',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('active_tab', 'association');
    }

    public function test_invalid_institution_submission_reports_errors_in_the_institution_bag(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->from(route('admin.settings.index'))->put(route('admin.settings.institution'), [
            'name' => '',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHasErrorsIn('institution', ['name']);
    }

    public function test_non_admin_cannot_update_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('admin.settings.institution'), [
            'name' => 'Hacked Name',
        ]);

        $response->assertForbidden();
    }
}
