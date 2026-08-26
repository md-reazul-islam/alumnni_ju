<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CateringSettingsController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-catering'), 403);
    }

    public function edit(Request $request): View
    {
        $this->ensurePermission($request);

        $settings = [
            'tax_percentage' => Setting::get('catering', 'tax_percentage', 8),
            'vat_percentage' => Setting::get('catering', 'vat_percentage', 0),
            'service_fee_percentage' => Setting::get('catering', 'service_fee_percentage', 10),
            'cancellation_window_hours' => Setting::get('catering', 'cancellation_window_hours', 48),
        ];

        return view('admin.catering.settings', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate([
            'tax_percentage' => ['required', 'numeric', 'min:0', 'max:50'],
            'vat_percentage' => ['required', 'numeric', 'min:0', 'max:50'],
            'service_fee_percentage' => ['required', 'numeric', 'min:0', 'max:50'],
            'cancellation_window_hours' => ['required', 'integer', 'min:0', 'max:720'],
        ]);

        foreach ($data as $key => $value) {
            Setting::set('catering', $key, $value);
        }

        AuditLogger::log('updated_catering_settings', null, 'Updated catering tax, VAT, service fee, and cancellation window settings.', [], $data);

        return back()->with('status', 'Settings updated.');
    }
}
