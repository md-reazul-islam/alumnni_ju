<x-layouts::alumni :title="'My Matrimony Profiles'">
    <x-breadcrumb :items="[['label' => 'My Matrimony Profiles']]" class="mb-4" />

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">My Matrimony Profiles</h1>
        <x-button :href="route('matrimony.profiles.create')" size="sm">Create Profile</x-button>
    </div>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    @if ($profiles->isEmpty())
        <x-empty-state icon="heart" title="No matrimony profiles yet" description="Create a profile for yourself or on behalf of a family member." class="mt-8" />
    @else
        <div class="mt-6 space-y-4">
            @foreach ($profiles as $profile)
                <a href="{{ route('matrimony.profiles.edit', $profile) }}" class="card card-body flex flex-wrap items-center justify-between gap-4 transition hover:shadow-popover">
                    <div>
                        <div class="flex items-center gap-2">
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $profile->display_name ?: $profile->full_name }}</p>
                            <x-badge :variant="match($profile->status) { 'approved' => 'success', 'rejected', 'suspended' => 'danger', default => 'warning' }">
                                {{ ucfirst($profile->status) }}
                            </x-badge>
                            @if ($profile->is_verified)
                                <x-badge variant="info">Verified</x-badge>
                            @endif
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $profile->age }} yrs &middot; {{ $profile->city ? $profile->city . ', ' : '' }}{{ $profile->country }} &middot; {{ ucfirst(str_replace('_', ' ', $profile->managed_by_relation)) }}</p>
                    </div>
                    <p class="text-sm text-slate-400">{{ $profile->profile_completion }}% complete</p>
                </a>
            @endforeach
        </div>
    @endif
</x-layouts::alumni>
