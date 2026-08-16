<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_admin_can_update_the_contact_page_message(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->put(route('admin.settings.institution'), [
            'name' => config('app.name'),
            'contact_message' => 'Reach out any time — our alumni office responds within one business day.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertSame(
            'Reach out any time — our alumni office responds within one business day.',
            Setting::get('institution', 'contact_message')
        );
    }

    public function test_contact_page_shows_the_configured_message(): void
    {
        Setting::set('institution', 'contact_message', 'Custom contact page message.');

        $response = $this->get(route('contact'));

        $response->assertOk();
        $response->assertSee('Custom contact page message.');
    }

    public function test_contact_page_falls_back_to_the_default_message(): void
    {
        $response = $this->get(route('contact'));

        $response->assertOk();
        $response->assertSee(\App\Http\Controllers\Admin\SettingsController::DEFAULT_CONTACT_MESSAGE);
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

    public function test_admin_can_update_general_settings_with_uploads(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->put(route('admin.settings.general'), [
            'site_text' => 'Springfield Alumni',
            'site_title' => 'Springfield Alumni Portal',
            'logo' => UploadedFile::fake()->image('logo.png'),
            'favicon' => UploadedFile::fake()->image('favicon.png'),
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('active_tab', 'general');
        $this->assertSame('Springfield Alumni', Setting::get('general', 'site_text'));
        $this->assertSame('Springfield Alumni Portal', Setting::get('general', 'site_title'));

        $logoPath = Setting::get('general', 'logo');
        $this->assertNotNull($logoPath);
        Storage::disk('public')->assertExists($logoPath);
    }

    public function test_admin_can_update_the_footer_tagline(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->put(route('admin.settings.general'), [
            'footer_tagline' => 'Building lifelong bonds, one graduate at a time.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertSame('Building lifelong bonds, one graduate at a time.', Setting::get('general', 'footer_tagline'));
    }

    public function test_clearing_the_footer_tagline_restores_the_default(): void
    {
        $admin = User::factory()->admin()->create();
        Setting::set('general', 'footer_tagline', 'Custom tagline');

        $response = $this->actingAs($admin)->put(route('admin.settings.general'), [
            'footer_tagline' => '',
        ]);

        $response->assertRedirect();
        $this->assertSame(
            \App\Http\Controllers\Admin\SettingsController::DEFAULT_FOOTER_TAGLINE,
            Setting::get('general', 'footer_tagline', \App\Http\Controllers\Admin\SettingsController::DEFAULT_FOOTER_TAGLINE)
        );
    }

    public function test_admin_can_remove_an_existing_logo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $path = UploadedFile::fake()->image('logo.png')->store('branding', 'public');
        Setting::set('general', 'logo', $path);

        $response = $this->actingAs($admin)->put(route('admin.settings.general'), [
            'remove_logo' => '1',
        ]);

        $response->assertRedirect();
        $this->assertNull(Setting::get('general', 'logo'));
        Storage::disk('public')->assertMissing($path);
    }

    public function test_non_admin_cannot_update_general_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('admin.settings.general'), [
            'site_text' => 'Hacked',
        ]);

        $response->assertForbidden();
    }
}
