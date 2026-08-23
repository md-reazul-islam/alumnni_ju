<x-layouts::admin :title="'Pending Drivers'">
    <x-breadcrumb :items="[['label' => 'Carpooling'], ['label' => 'Pending Drivers']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Pending Driver Applications</h1>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    @if ($profiles->isEmpty())
        <x-empty-state icon="clipboard-check" title="No driver applications awaiting review" class="mt-8" />
    @else
        <div class="mt-6 space-y-4">
            @foreach ($profiles as $profile)
                <div class="card card-body">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $profile->user->full_name }}</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">License #{{ $profile->license_number }}</p>
                            <p class="mt-1 text-xs text-slate-400">Applied {{ $profile->created_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('admin.carpooling.drivers.show', $profile) }}" class="btn-primary btn-sm flex-shrink-0">Review</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $profiles->links() }}</div>
    @endif
</x-layouts::admin>
