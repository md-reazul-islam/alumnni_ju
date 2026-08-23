<x-layouts::admin :title="'Rejected Drivers'">
    <x-breadcrumb :items="[['label' => 'Carpooling'], ['label' => 'Rejected Drivers']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Rejected Driver Applications</h1>

    @if ($profiles->isEmpty())
        <x-empty-state icon="ban" title="No rejected applications" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Applicant</th><th>License #</th><th>Reason</th><th></th></tr></thead>
            <tbody>
                @foreach ($profiles as $profile)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">
                            <a href="{{ route('admin.carpooling.drivers.show', $profile) }}" class="hover:underline">{{ $profile->user->full_name }}</a>
                        </td>
                        <td>{{ $profile->license_number }}</td>
                        <td class="max-w-xs truncate" title="{{ $profile->rejection_reason }}">{{ $profile->rejection_reason }}</td>
                        <td><a href="{{ route('admin.carpooling.drivers.show', $profile) }}" class="text-navy-700 hover:underline dark:text-navy-300">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $profiles->links() }}</div>
    @endif
</x-layouts::admin>
