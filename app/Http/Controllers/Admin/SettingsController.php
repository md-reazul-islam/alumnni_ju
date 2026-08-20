<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public const DEFAULT_FOOTER_TAGLINE = 'Connecting graduates worldwide through networking, mentorship, career opportunities, and lifelong community.';
    public const DEFAULT_CONTACT_MESSAGE = 'Questions about your account, an upcoming event, or the alumni association in general? Send us a message.';

    public const DEFAULT_ABOUT_HERO_SUBTITLE = 'A lifelong community connecting graduates, faculty, and friends of the university across the world.';
    public const DEFAULT_ABOUT_HERO_TITLE_PREFIX = 'About ';
    public const DEFAULT_ABOUT_MISSION_HEADING = 'Our Mission';
    public const DEFAULT_ABOUT_MISSION_TEXT = 'The Alumni Association exists to strengthen the bond between the university and its graduates — fostering professional networking, mentorship, philanthropy, and community engagement that spans generations. We believe an alumni network is a lifelong resource, not a one-time membership.';
    public const DEFAULT_ABOUT_ITEMS_HEADING = 'What We Do';
    public const DEFAULT_ABOUT_ITEMS = [
        ['icon' => 'users', 'text' => 'Connect alumni with each other through a searchable global directory.'],
        ['icon' => 'briefcase', 'text' => 'Power a career center for job postings, mentorship, and referrals.'],
        ['icon' => 'calendar', 'text' => 'Host reunions, workshops, and regional meetups worldwide.'],
        ['icon' => 'heart', 'text' => 'Support scholarships and student programs through alumni giving.'],
    ];
    public const DEFAULT_ABOUT_CTA_HEADING = 'Ready to reconnect?';
    public const DEFAULT_ABOUT_CTA_TEXT = 'Join thousands of alumni already part of the network.';
    public const DEFAULT_ABOUT_CTA_BUTTON_TEXT = 'Join the Alumni Network';

    public const ABOUT_ICON_OPTIONS = [
        'users', 'briefcase', 'calendar', 'heart', 'graduation-cap', 'handshake',
        'globe', 'award', 'gift', 'megaphone', 'book-open', 'target', 'star', 'map-pin', 'building',
    ];

    public const DEFAULT_LOGIN_HERO_TITLE = 'Connect. Engage. Inspire. Give Back.';
    public const DEFAULT_LOGIN_HERO_SUBTITLE = 'Reconnect with your university community and build meaningful professional relationships with alumni around the world.';

    public const HOMEPAGE_SECTIONS = [
        'show_hero' => 'Hero Slider',
        'show_stats' => 'Stats Bar',
        'show_featured_alumni' => 'Featured Alumni',
        'show_events' => 'Upcoming Events',
        'show_jobs' => 'Career Opportunities',
        'show_marketplace' => 'Marketplace (House Rent & Property)',
        'show_stories' => 'Alumni Stories',
        'show_gallery' => 'Gallery',
        'show_library' => 'Your Library',
        'show_news' => 'News & Announcements',
        'show_cta' => 'Bottom Call-to-Action',
    ];

    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-settings'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $institution = [
            'name' => Setting::get('institution', 'name', config('app.name')),
            'email' => Setting::get('institution', 'email'),
            'phone' => Setting::get('institution', 'phone'),
            'address' => Setting::get('institution', 'address'),
            'website' => Setting::get('institution', 'website'),
            'contact_message' => Setting::get('institution', 'contact_message', self::DEFAULT_CONTACT_MESSAGE),
        ];

        $association = [
            'name' => Setting::get('association', 'name'),
            'description' => Setting::get('association', 'description'),
            'contact_email' => Setting::get('association', 'contact_email'),
        ];

        $general = [
            'site_text' => Setting::get('general', 'site_text', config('app.name')),
            'site_title' => Setting::get('general', 'site_title', config('app.name')),
            'footer_tagline' => Setting::get('general', 'footer_tagline', self::DEFAULT_FOOTER_TAGLINE),
            'logo' => Setting::get('general', 'logo'),
            'icon' => Setting::get('general', 'icon'),
            'favicon' => Setting::get('general', 'favicon'),
        ];

        $aboutItemsRaw = Setting::get('about', 'items');
        $aboutItems = $aboutItemsRaw ? json_decode($aboutItemsRaw, true) : self::DEFAULT_ABOUT_ITEMS;

        $about = [
            'hero_title' => Setting::get('about', 'hero_title', self::DEFAULT_ABOUT_HERO_TITLE_PREFIX . config('app.name')),
            'hero_subtitle' => Setting::get('about', 'hero_subtitle', self::DEFAULT_ABOUT_HERO_SUBTITLE),
            'mission_heading' => Setting::get('about', 'mission_heading', self::DEFAULT_ABOUT_MISSION_HEADING),
            'mission_text' => Setting::get('about', 'mission_text', self::DEFAULT_ABOUT_MISSION_TEXT),
            'items_heading' => Setting::get('about', 'items_heading', self::DEFAULT_ABOUT_ITEMS_HEADING),
            'items' => $aboutItems,
            'cta_heading' => Setting::get('about', 'cta_heading', self::DEFAULT_ABOUT_CTA_HEADING),
            'cta_text' => Setting::get('about', 'cta_text', self::DEFAULT_ABOUT_CTA_TEXT),
            'cta_button_text' => Setting::get('about', 'cta_button_text', self::DEFAULT_ABOUT_CTA_BUTTON_TEXT),
        ];

        $login = [
            'hero_title' => Setting::get('login', 'hero_title', self::DEFAULT_LOGIN_HERO_TITLE),
            'hero_subtitle' => Setting::get('login', 'hero_subtitle', self::DEFAULT_LOGIN_HERO_SUBTITLE),
        ];

        $homepage = [];
        foreach (self::HOMEPAGE_SECTIONS as $key => $label) {
            $homepage[$key] = Setting::get('homepage', $key, true) !== '0';
        }

        return view('admin.settings.index', compact('institution', 'association', 'general', 'about', 'login', 'homepage'));
    }

    public function updateInstitution(Request $request): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validateWithBag('institution', [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'website' => ['nullable', 'url', 'max:255'],
            'contact_message' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($data as $key => $value) {
            Setting::set('institution', $key, $value);
        }

        AuditLogger::log('updated_settings', null, 'Updated institution settings.', [], $data);

        return back()->with('status', 'Institution settings updated.')->with('active_tab', 'institution');
    }

    public function updateAssociation(Request $request): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validateWithBag('association', [
            'name' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'contact_email' => ['nullable', 'email', 'max:255'],
        ]);

        foreach ($data as $key => $value) {
            Setting::set('association', $key, $value);
        }

        AuditLogger::log('updated_settings', null, 'Updated alumni association settings.', [], $data);

        return back()->with('status', 'Alumni association settings updated.')->with('active_tab', 'association');
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validateWithBag('general', [
            'site_text' => ['nullable', 'string', 'max:100'],
            'site_title' => ['nullable', 'string', 'max:100'],
            'footer_tagline' => ['nullable', 'string', 'max:300'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:1024'],
            'favicon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,ico', 'max:512'],
        ]);

        Setting::set('general', 'site_text', $data['site_text'] ?? null);
        Setting::set('general', 'site_title', $data['site_title'] ?? null);
        Setting::set('general', 'footer_tagline', $data['footer_tagline'] ?? null);

        foreach (['logo', 'icon', 'favicon'] as $field) {
            if ($request->hasFile($field)) {
                $existing = Setting::get('general', $field);
                if ($existing) {
                    Storage::disk('public')->delete($existing);
                }
                Setting::set('general', $field, $request->file($field)->store('branding', 'public'));
            } elseif ($request->boolean("remove_{$field}")) {
                $existing = Setting::get('general', $field);
                if ($existing) {
                    Storage::disk('public')->delete($existing);
                }
                Setting::set('general', $field, null);
            }
        }

        AuditLogger::log('updated_settings', null, 'Updated general branding settings.');

        return back()->with('status', 'General settings updated.')->with('active_tab', 'general');
    }

    public function updateAbout(Request $request): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validateWithBag('about', [
            'hero_title' => ['nullable', 'string', 'max:150'],
            'hero_subtitle' => ['nullable', 'string', 'max:300'],
            'mission_heading' => ['nullable', 'string', 'max:150'],
            'mission_text' => ['nullable', 'string', 'max:2000'],
            'items_heading' => ['nullable', 'string', 'max:150'],
            'items' => ['nullable', 'array'],
            'items.*.icon' => ['nullable', 'string', 'in:' . implode(',', self::ABOUT_ICON_OPTIONS)],
            'items.*.text' => ['nullable', 'string', 'max:300'],
            'cta_heading' => ['nullable', 'string', 'max:150'],
            'cta_text' => ['nullable', 'string', 'max:300'],
            'cta_button_text' => ['nullable', 'string', 'max:100'],
        ]);

        $items = collect($data['items'] ?? [])
            ->filter(fn ($item) => filled($item['text'] ?? null))
            ->map(fn ($item) => ['icon' => $item['icon'] ?? 'star', 'text' => $item['text']])
            ->values()
            ->all();

        Setting::set('about', 'hero_title', $data['hero_title'] ?? null);
        Setting::set('about', 'hero_subtitle', $data['hero_subtitle'] ?? null);
        Setting::set('about', 'mission_heading', $data['mission_heading'] ?? null);
        Setting::set('about', 'mission_text', $data['mission_text'] ?? null);
        Setting::set('about', 'items_heading', $data['items_heading'] ?? null);
        Setting::set('about', 'items', $items ? json_encode($items) : null);
        Setting::set('about', 'cta_heading', $data['cta_heading'] ?? null);
        Setting::set('about', 'cta_text', $data['cta_text'] ?? null);
        Setting::set('about', 'cta_button_text', $data['cta_button_text'] ?? null);

        AuditLogger::log('updated_settings', null, 'Updated about page settings.', [], $data);

        return back()->with('status', 'About page settings updated.')->with('active_tab', 'about');
    }

    public function updateLoginPage(Request $request): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validateWithBag('login', [
            'hero_title' => ['nullable', 'string', 'max:150'],
            'hero_subtitle' => ['nullable', 'string', 'max:300'],
        ]);

        Setting::set('login', 'hero_title', $data['hero_title'] ?? null);
        Setting::set('login', 'hero_subtitle', $data['hero_subtitle'] ?? null);

        AuditLogger::log('updated_settings', null, 'Updated login page settings.', [], $data);

        return back()->with('status', 'Login page settings updated.')->with('active_tab', 'login');
    }

    public function updateHomepage(Request $request): RedirectResponse
    {
        $this->ensurePermission($request);

        $rules = array_fill_keys(array_map(fn ($key) => $key, array_keys(self::HOMEPAGE_SECTIONS)), ['nullable', 'boolean']);
        $data = $request->validateWithBag('homepage', $rules);

        foreach (array_keys(self::HOMEPAGE_SECTIONS) as $key) {
            Setting::set('homepage', $key, $request->boolean($key) ? '1' : '0');
        }

        AuditLogger::log('updated_settings', null, 'Updated homepage section visibility.', [], $data);

        return back()->with('status', 'Homepage sections updated.')->with('active_tab', 'homepage');
    }
}
