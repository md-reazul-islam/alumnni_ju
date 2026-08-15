<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
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
        ];

        $association = [
            'name' => Setting::get('association', 'name'),
            'description' => Setting::get('association', 'description'),
            'contact_email' => Setting::get('association', 'contact_email'),
        ];

        return view('admin.settings.index', compact('institution', 'association'));
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
}
