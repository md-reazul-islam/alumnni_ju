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

    public function test_admin_can_reorder_homepage_sections(): void
    {
        $admin = User::factory()->admin()->create();

        $order = ['show_marketplace', 'show_hero', 'show_stats', 'show_featured_alumni', 'show_events', 'show_jobs', 'show_stories', 'show_gallery', 'show_library', 'show_news', 'show_cta', 'show_carpooling', 'show_matrimony', 'show_catering', 'show_media_advocacy'];

        $response = $this->actingAs($admin)->put(route('admin.settings.homepage'), [
            'section_order' => json_encode($order),
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertSame($order, \App\Http\Controllers\Admin\SettingsController::resolveSectionOrder());
    }

    public function test_reordering_ignores_invalid_keys_and_appends_missing_ones(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->put(route('admin.settings.homepage'), [
            'section_order' => json_encode(['show_marketplace', 'not_a_real_section', 'show_news']),
        ]);

        $response->assertRedirect();
        $resolved = \App\Http\Controllers\Admin\SettingsController::resolveSectionOrder();

        $this->assertSame(['show_marketplace', 'show_news'], array_slice($resolved, 0, 2));
        $this->assertNotContains('not_a_real_section', $resolved);
        $this->assertSame(count(\App\Http\Controllers\Admin\SettingsController::HOMEPAGE_SECTIONS), count($resolved));
    }

    public function test_admin_can_set_a_custom_section_description(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->put(route('admin.settings.homepage'), [
            'description_marketplace' => 'Find your next rental or great deals from fellow graduates.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertSame(
            'Find your next rental or great deals from fellow graduates.',
            \App\Http\Controllers\Admin\SettingsController::resolveSectionDescription('marketplace')
        );
    }

    public function test_clearing_a_section_description_restores_the_default(): void
    {
        $admin = User::factory()->admin()->create();
        Setting::set('homepage', 'description_carpooling', 'Custom text');

        $response = $this->actingAs($admin)->put(route('admin.settings.homepage'), [
            'description_carpooling' => '',
        ]);

        $response->assertRedirect();
        $this->assertSame(
            \App\Http\Controllers\Admin\SettingsController::HOMEPAGE_SECTION_DESCRIPTIONS['carpooling'],
            \App\Http\Controllers\Admin\SettingsController::resolveSectionDescription('carpooling')
        );
    }

    public function test_every_configurable_section_description_can_be_set_and_cleared(): void
    {
        $admin = User::factory()->admin()->create();
        $keys = array_keys(\App\Http\Controllers\Admin\SettingsController::HOMEPAGE_SECTION_DESCRIPTIONS);

        $this->assertSame(
            ['featured_alumni', 'jobs', 'marketplace', 'carpooling', 'matrimony', 'catering', 'media_advocacy', 'stories', 'gallery', 'library', 'news'],
            $keys
        );

        $payload = [];
        foreach ($keys as $key) {
            $payload["description_{$key}"] = "Custom {$key} text.";
        }

        $response = $this->actingAs($admin)->put(route('admin.settings.homepage'), $payload);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        foreach ($keys as $key) {
            $this->assertSame(
                "Custom {$key} text.",
                \App\Http\Controllers\Admin\SettingsController::resolveSectionDescription($key)
            );
        }

        $clearPayload = array_fill_keys(array_map(fn ($key) => "description_{$key}", $keys), '');
        $this->actingAs($admin)->put(route('admin.settings.homepage'), $clearPayload);

        foreach ($keys as $key) {
            $this->assertSame(
                \App\Http\Controllers\Admin\SettingsController::HOMEPAGE_SECTION_DESCRIPTIONS[$key],
                \App\Http\Controllers\Admin\SettingsController::resolveSectionDescription($key)
            );
        }
    }

    public function test_homepage_renders_the_configured_section_description(): void
    {
        Setting::set('homepage', 'description_matrimony', 'Custom matrimony blurb for testing.');

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Custom matrimony blurb for testing.');
    }

    public function test_admin_can_set_a_custom_section_name(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->put(route('admin.settings.homepage'), [
            'name_marketplace' => 'Housing & Marketplace',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertSame(
            'Housing & Marketplace',
            \App\Http\Controllers\Admin\SettingsController::resolveSectionName('marketplace')
        );
    }

    public function test_clearing_a_section_name_restores_the_default(): void
    {
        $admin = User::factory()->admin()->create();
        Setting::set('homepage', 'name_marketplace', 'Custom Name');

        $response = $this->actingAs($admin)->put(route('admin.settings.homepage'), [
            'name_marketplace' => '',
        ]);

        $response->assertRedirect();
        $this->assertSame(
            \App\Http\Controllers\Admin\SettingsController::HOMEPAGE_SECTION_NAMES['marketplace'],
            \App\Http\Controllers\Admin\SettingsController::resolveSectionName('marketplace')
        );
    }

    public function test_section_name_default_for_featured_alumni_and_stories_tracks_site_text(): void
    {
        Setting::set('general', 'site_text', 'Springfield Grads');

        $this->assertSame('Featured Springfield Grads', \App\Http\Controllers\Admin\SettingsController::resolveSectionName('featured_alumni'));
        $this->assertSame('Springfield Grads Stories', \App\Http\Controllers\Admin\SettingsController::resolveSectionName('stories'));

        Setting::set('homepage', 'name_featured_alumni', 'Distinguished Graduates');
        $this->assertSame('Distinguished Graduates', \App\Http\Controllers\Admin\SettingsController::resolveSectionName('featured_alumni'));

        Setting::set('general', 'site_text', 'A Different Name');
        $this->assertSame('Distinguished Graduates', \App\Http\Controllers\Admin\SettingsController::resolveSectionName('featured_alumni'));
        $this->assertSame('A Different Name Stories', \App\Http\Controllers\Admin\SettingsController::resolveSectionName('stories'));
    }

    public function test_homepage_renders_the_configured_section_name(): void
    {
        Setting::set('homepage', 'name_matrimony', 'Custom Matrimony Heading');

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Custom Matrimony Heading');
    }

    public function test_homepage_renders_sections_in_the_configured_order(): void
    {
        \App\Models\Setting::set('homepage', 'section_order', json_encode(['show_marketplace', 'show_hero', 'show_stats', 'show_featured_alumni', 'show_events', 'show_jobs', 'show_stories', 'show_gallery', 'show_library', 'show_news', 'show_cta']));

        $response = $this->get(route('home'));

        $response->assertOk();
        $content = $response->getContent();
        $marketplacePos = strpos($content, 'Marketplace');
        $heroPos = strpos($content, 'Connect. Engage. Inspire.');

        $this->assertNotFalse($marketplacePos);
        $this->assertNotFalse($heroPos);
        $this->assertLessThan($heroPos, $marketplacePos);
    }

    public function test_non_admin_cannot_reorder_homepage_sections(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('admin.settings.homepage'), [
            'section_order' => json_encode(['show_marketplace', 'show_hero']),
        ]);

        $response->assertForbidden();
    }

    public function test_navbar_menu_defaults_to_the_current_layout_when_unconfigured(): void
    {
        $defaults = \App\Http\Controllers\Admin\SettingsController::resolveNavbarPrimaryKeys();

        $this->assertSame(\App\Http\Controllers\Admin\SettingsController::DEFAULT_NAVBAR_PRIMARY_KEYS, $defaults);
    }

    public function test_admin_can_move_a_menu_outside_the_dropdown_and_reorder_it(): void
    {
        $admin = User::factory()->admin()->create();

        $primary = ['about', 'marketplace', 'carpooling', 'matrimony', 'catering', 'events', 'alumni', 'stories'];
        $order = ['about', 'marketplace', 'carpooling', 'matrimony', 'catering', 'events', 'alumni', 'stories', 'careers', 'news', 'gallery', 'library', 'media_advocacy', 'donate', 'contact'];

        $payload = ['menu_order' => json_encode($order)];
        foreach ($primary as $key) {
            $payload["primary_{$key}"] = '1';
        }

        $response = $this->actingAs($admin)->put(route('admin.settings.navbar'), $payload);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertSame($primary, \App\Http\Controllers\Admin\SettingsController::resolveNavbarPrimaryKeys());
        $this->assertSame($order, \App\Http\Controllers\Admin\SettingsController::resolveNavbarOrder());
    }

    public function test_navbar_menu_reorder_ignores_invalid_keys_and_appends_missing_ones(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->put(route('admin.settings.navbar'), [
            'menu_order' => json_encode(['stories', 'not_a_real_menu', 'about']),
        ]);

        $response->assertRedirect();
        $resolved = \App\Http\Controllers\Admin\SettingsController::resolveNavbarOrder();

        $this->assertSame(['stories', 'about'], array_slice($resolved, 0, 2));
        $this->assertNotContains('not_a_real_menu', $resolved);
        $this->assertSame(count(\App\Http\Controllers\Admin\SettingsController::NAVBAR_MENU_ITEMS), count($resolved));
    }

    public function test_unchecking_every_primary_toggle_moves_everything_into_the_dropdown(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->put(route('admin.settings.navbar'), []);

        $response->assertRedirect();
        $this->assertSame([], \App\Http\Controllers\Admin\SettingsController::resolveNavbarPrimaryKeys());
    }

    public function test_homepage_nav_reflects_the_configured_navbar_layout(): void
    {
        \App\Models\Setting::set('navbar', 'primary_keys', json_encode(['stories']));

        $response = $this->get(route('home'));

        $response->assertOk();
        $content = $response->getContent();
        $navStart = strpos($content, '<nav class="hidden items-center');
        $navEnd = strpos($content, '</nav>', $navStart);
        $navHtml = substr($content, $navStart, $navEnd - $navStart);

        // The "More" dropdown button/menu is nested inside the same <nav>, right
        // after the primary items — slice it off so only the primary bar remains.
        $moreButtonPos = strpos($navHtml, 'More');
        $primaryNavHtml = substr($navHtml, 0, $moreButtonPos !== false ? $moreButtonPos : strlen($navHtml));

        $this->assertStringContainsString('Stories', $primaryNavHtml);
        $this->assertStringNotContainsString('Marketplace', $primaryNavHtml);
    }

    public function test_non_admin_cannot_update_navbar_menu(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('admin.settings.navbar'), [
            'primary_about' => '1',
        ]);

        $response->assertForbidden();
    }
}
