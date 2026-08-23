<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarpoolSettingsController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-carpooling'), 403);
    }

    public function edit(Request $request): View
    {
        $this->ensurePermission($request);

        $settings = [
            'commission_percentage' => Setting::get('carpooling', 'commission_percentage', 10),
            'payment_window_minutes' => Setting::get('carpooling', 'payment_window_minutes', 30),
            'cancellation_window_hours' => Setting::get('carpooling', 'cancellation_window_hours', 24),
        ];

        return view('admin.carpooling.settings', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate([
            'commission_percentage' => ['required', 'numeric', 'min:0', 'max:50'],
            'payment_window_minutes' => ['required', 'integer', 'min:5', 'max:1440'],
            'cancellation_window_hours' => ['required', 'integer', 'min:0', 'max:168'],
        ]);

        foreach ($data as $key => $value) {
            Setting::set('carpooling', $key, $value);
        }

        AuditLogger::log('updated_carpool_settings', null, 'Updated carpooling commission and timing settings.', [], $data);

        return back()->with('status', 'Settings updated.');
    }
}
