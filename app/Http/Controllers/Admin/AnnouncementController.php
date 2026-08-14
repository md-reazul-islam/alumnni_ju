<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Role;
use App\Models\User;
use App\Notifications\AdminAnnouncementPosted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-announcements'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $announcements = Announcement::with('creator')->latest()->paginate(15);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:3000'],
            'audience' => ['required', Rule::in(['all', 'alumni', 'admins'])],
            'is_pinned' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $data['created_by'] = $request->user()->id;
        $data['is_pinned'] = $request->boolean('is_pinned');
        $data['published_at'] = now();

        $announcement = Announcement::create($data);

        $recipients = match ($announcement->audience) {
            'alumni' => User::whereHas('role', fn ($q) => $q->where('slug', Role::ALUMNI))->get(),
            'admins' => User::whereHas('role', fn ($q) => $q->whereIn('slug', [Role::SUPER_ADMIN, Role::ALUMNI_ADMIN, Role::MODERATOR]))->get(),
            default => User::verified()->get(),
        };

        Notification::send($recipients, new AdminAnnouncementPosted($announcement));

        return back()->with('status', 'Announcement published and members notified.');
    }

    public function destroy(Request $request, Announcement $announcement): RedirectResponse
    {
        $this->ensurePermission($request);

        $announcement->delete();

        return back()->with('status', 'Announcement removed.');
    }
}
