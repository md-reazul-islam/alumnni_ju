@php $assignedPermissionIds = $assignedPermissionIds ?? old('permissions', []); @endphp

<div>
    <label class="form-label">Feature Access Permissions</label>
    <p class="form-hint">Check the features this moderator is allowed to manage.</p>

    <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($permissionsByGroup as $group => $permissions)
            <div class="card card-body">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ \Illuminate\Support\Str::headline($group) }}</p>
                <div class="mt-2 space-y-2">
                    @foreach ($permissions as $permission)
                        <x-checkbox
                            name="permissions[]"
                            :value="$permission->id"
                            :label="$permission->name"
                            :checked="in_array($permission->id, (array) $assignedPermissionIds)"
                        />
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
