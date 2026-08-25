<x-layouts::admin :title="'Suspended Matrimony Profiles'">
    <x-breadcrumb :items="[['label' => 'Matrimony'], ['label' => 'Suspended Profiles']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Suspended Matrimony Profiles</h1>

    @if ($profiles->isEmpty())
        <x-empty-state icon="ban" title="No suspended profiles" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Profile</th><th>Managed By</th><th>Reason</th><th></th></tr></thead>
            <tbody>
                @foreach ($profiles as $profile)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">
                            <a href="{{ route('admin.matrimony.profiles.show', $profile) }}" class="hover:underline">{{ $profile->display_name }}</a>
                        </td>
                        <td>{{ $profile->creator->full_name }}</td>
                        <td class="max-w-xs truncate" title="{{ $profile->rejection_reason }}">{{ $profile->rejection_reason }}</td>
                        <td><a href="{{ route('admin.matrimony.profiles.show', $profile) }}" class="text-navy-700 hover:underline dark:text-navy-300">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $profiles->links() }}</div>
    @endif
</x-layouts::admin>
