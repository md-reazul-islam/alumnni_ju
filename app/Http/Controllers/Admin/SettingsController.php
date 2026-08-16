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

        return view('admin.settings.index', compact('institution', 'association', 'general'));
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
}
