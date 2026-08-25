<x-layouts::admin :title="'Pending Matrimony Profiles'">
    <x-breadcrumb :items="[['label' => 'Matrimony'], ['label' => 'Pending Profiles']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Pending Matrimony Profiles</h1>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    @if ($profiles->isEmpty())
        <x-empty-state icon="clipboard-check" title="No profiles awaiting review" class="mt-8" />
    @else
        <div class="mt-6 space-y-4">
            @foreach ($profiles as $profile)
                <div class="card card-body">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $profile->display_name }} &middot; {{ $profile->age }} yrs &middot; {{ ucfirst($profile->gender) }}</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $profile->city ? $profile->city.', ' : '' }}{{ $profile->country }} &middot; Managed by {{ $profile->creator->full_name }}</p>
                            <p class="mt-1 text-xs text-slate-400">Submitted {{ $profile->updated_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('admin.matrimony.profiles.show', $profile) }}" class="btn-primary btn-sm flex-shrink-0">Review</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $profiles->links() }}</div>
    @endif
</x-layouts::admin>
