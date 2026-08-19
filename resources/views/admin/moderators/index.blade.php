<x-layouts::admin :title="'Moderators'">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Moderators</h1>
        <x-button :href="route('admin.moderators.create')" size="sm">
            <x-icon name="plus" class="h-4 w-4" /> Create Moderator
        </x-button>
    </div>

    @if ($moderators->isEmpty())
        <x-empty-state icon="shield" title="No moderators yet" description="Create a moderator account and choose which features they can manage." class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Name</th><th>Email</th><th>Permissions</th><th></th></tr></thead>
            <tbody>
                @foreach ($moderators as $moderator)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $moderator->full_name }}</td>
                        <td>{{ $moderator->email }}</td>
                        <td>
                            @if ($moderator->permissions->isEmpty())
                                <span class="text-xs text-slate-400">Default moderation access only</span>
                            @else
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $moderator->permissions->count() }} feature(s)</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.moderators.edit', $moderator) }}" class="text-slate-400 hover:text-navy-700" title="Edit"><x-icon name="pencil" class="h-4 w-4" /></a>
                                <form method="POST" action="{{ route('admin.moderators.destroy', $moderator) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Remove this moderator?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Remove'}).then(r=>r.isConfirmed&&this.submit())">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600" title="Remove"><x-icon name="trash-2" class="h-4 w-4" /></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $moderators->links() }}</div>
    @endif
</x-layouts::admin>
