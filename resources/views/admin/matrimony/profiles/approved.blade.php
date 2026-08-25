<x-layouts::admin :title="'Approved Matrimony Profiles'">
    <x-breadcrumb :items="[['label' => 'Matrimony'], ['label' => 'Approved Profiles']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Approved Matrimony Profiles</h1>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    @if ($profiles->isEmpty())
        <x-empty-state icon="heart" title="No approved profiles yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Profile</th><th>Age</th><th>Gender</th><th>Location</th><th>Managed By</th><th>Status</th></tr></thead>
            <tbody>
                @foreach ($profiles as $profile)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">
                            <a href="{{ route('admin.matrimony.profiles.show', $profile) }}" class="hover:underline">{{ $profile->display_name }}</a>
                        </td>
                        <td>{{ $profile->age }}</td>
                        <td>{{ ucfirst($profile->gender) }}</td>
                        <td>{{ $profile->city ? $profile->city.', ' : '' }}{{ $profile->country }}</td>
                        <td>{{ $profile->creator->full_name }}</td>
                        <td>
                            <x-badge :variant="$profile->is_active ? 'success' : 'neutral'">{{ $profile->is_active ? 'Active' : 'Paused' }}</x-badge>
                            @if ($profile->is_verified)
                                <x-badge variant="info">Verified</x-badge>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $profiles->links() }}</div>
    @endif
</x-layouts::admin>
