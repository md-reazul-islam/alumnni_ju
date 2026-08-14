@if ($alumni->isEmpty())
    <x-empty-state icon="graduation-cap" title="No alumni found" class="mt-8" />
@else
    <form method="POST" action="{{ route('admin.alumni.bulk-action') }}" x-data="{ selected: [] }" onsubmit="return confirm('Apply this action to selected alumni?')">
        @csrf

        <div class="mb-3 flex items-center gap-2" x-show="selected.length > 0" x-cloak>
            <span class="text-sm text-slate-500" x-text="selected.length + ' selected'"></span>
            <button type="submit" name="action" value="verify" class="btn-secondary btn-sm">Verify</button>
            <button type="submit" name="action" value="suspend" class="btn-secondary btn-sm">Suspend</button>
            <button type="submit" name="action" value="notify" class="btn-secondary btn-sm">Notify</button>
        </div>

        <x-table>
            <thead>
                <tr>
                    <th><input type="checkbox" @click="selected = $event.target.checked ? Array.from(document.querySelectorAll('.alumni-check')).map(c => c.value) : []; document.querySelectorAll('.alumni-check').forEach(c => c.checked = $event.target.checked)"></th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($alumni as $user)
                    <tr>
                        <td><input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="alumni-check" x-model="selected"></td>
                        <td class="flex items-center gap-2 font-medium text-slate-900 dark:text-white">
                            <x-avatar :src="$user->avatar_url" :name="$user->full_name" size="xs" />
                            <a href="{{ route('admin.alumni.show', $user) }}" class="hover:underline">{{ $user->full_name }}</a>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->alumniProfile?->department?->name ?? '—' }}</td>
                        <td>
                            <x-badge :variant="match($user->status) { 'verified' => 'success', 'pending' => 'warning', 'suspended' => 'danger', 'rejected' => 'neutral', default => 'neutral' }">
                                {{ ucfirst($user->status) }}
                            </x-badge>
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-2">
                                @if ($user->status === 'pending')
                                    <form method="POST" action="{{ route('admin.alumni.verify', $user) }}">
                                        @csrf
                                        <button type="submit" class="text-emerald-600 hover:text-emerald-700" title="Verify"><x-icon name="circle-check" class="h-4 w-4" /></button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.alumni.reject', $user) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Reject this application?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Reject'}).then(r=>r.isConfirmed&&this.submit())">
                                        @csrf
                                        <button type="submit" class="text-red-500 hover:text-red-700" title="Reject"><x-icon name="circle-x" class="h-4 w-4" /></button>
                                    </form>
                                @elseif ($user->status === 'verified')
                                    <form method="POST" action="{{ route('admin.alumni.suspend', $user) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Suspend this account?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Suspend'}).then(r=>r.isConfirmed&&this.submit())">
                                        @csrf
                                        <button type="submit" class="text-amber-500 hover:text-amber-700" title="Suspend"><x-icon name="ban" class="h-4 w-4" /></button>
                                    </form>
                                @elseif ($user->status === 'suspended')
                                    <form method="POST" action="{{ route('admin.alumni.restore', $user) }}">
                                        @csrf
                                        <button type="submit" class="text-emerald-600 hover:text-emerald-700" title="Restore"><x-icon name="refresh-cw" class="h-4 w-4" /></button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.alumni.show', $user) }}" class="text-slate-400 hover:text-navy-700" title="View"><x-icon name="eye" class="h-4 w-4" /></a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    </form>

    <div class="mt-6">{{ $alumni->links() }}</div>
@endif
