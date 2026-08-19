<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreModeratorRequest;
use App\Http\Requests\Admin\UpdateModeratorRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModeratorController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-administrators'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $moderators = User::whereHas('role', fn ($q) => $q->where('slug', Role::MODERATOR))
            ->with('permissions')
            ->latest()
            ->paginate(15);

        return view('admin.moderators.index', compact('moderators'));
    }

    public function create(Request $request): View
    {
        $this->ensurePermission($request);

        $permissionsByGroup = Permission::orderBy('name')->get()->groupBy('group');

        return view('admin.moderators.create', compact('permissionsByGroup'));
    }

    public function store(StoreModeratorRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $moderatorRole = Role::where('slug', Role::MODERATOR)->firstOrFail();

        $moderator = User::create([
            'role_id' => $moderatorRole->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'status' => User::STATUS_VERIFIED,
        ]);

        $moderator->forceFill(['email_verified_at' => now()])->save();

        $moderator->permissions()->sync($data['permissions'] ?? []);

        AuditLogger::log('created_moderator', $moderator, "Created moderator account for \"{$moderator->full_name}\".");

        return redirect()->route('admin.moderators.index')->with('status', 'Moderator created.');
    }

    public function edit(Request $request, User $moderator): View
    {
        $this->ensurePermission($request);
        abort_unless($moderator->isModerator(), 404);

        $permissionsByGroup = Permission::orderBy('name')->get()->groupBy('group');
        $assignedPermissionIds = $moderator->permissions()->pluck('permissions.id')->all();

        return view('admin.moderators.edit', compact('moderator', 'permissionsByGroup', 'assignedPermissionIds'));
    }

    public function update(UpdateModeratorRequest $request, User $moderator): RedirectResponse
    {
        abort_unless($moderator->isModerator(), 404);

        $data = $request->validated();

        $moderator->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => $data['password'] ?: $moderator->password,
        ]);

        $moderator->permissions()->sync($data['permissions'] ?? []);

        AuditLogger::log('updated_moderator', $moderator, "Updated moderator account for \"{$moderator->full_name}\".");

        return redirect()->route('admin.moderators.index')->with('status', 'Moderator updated.');
    }

    public function destroy(Request $request, User $moderator): RedirectResponse
    {
        $this->ensurePermission($request);
        abort_unless($moderator->isModerator(), 404);
        abort_if($request->user()->id === $moderator->id, 403);

        $moderator->delete();

        AuditLogger::log('deleted_moderator', $moderator, "Removed moderator account for \"{$moderator->full_name}\".");

        return back()->with('status', 'Moderator removed.');
    }
}
