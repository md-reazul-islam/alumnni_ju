<x-layouts::admin :title="$profile->user->full_name">
    <x-breadcrumb :items="[['label' => 'Carpooling'], ['label' => 'Pending Drivers', 'url' => route('admin.carpooling.drivers.pending')], ['label' => $profile->user->full_name]]" class="mb-4" />

    <div x-data="{ rejecting: false }">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <x-badge :variant="match($profile->status) { 'approved' => 'success', 'rejected' => 'danger', 'suspended' => 'danger', default => 'warning' }">
                    {{ ucfirst($profile->status) }}
                </x-badge>
                <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $profile->user->full_name }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $profile->user->email }} &middot; License #{{ $profile->license_number }}</p>
            </div>

            @if ($profile->status === 'pending')
                <div class="flex flex-shrink-0 gap-2">
                    <form method="POST" action="{{ route('admin.carpooling.drivers.approve', $profile) }}">
                        @csrf
                        <button type="submit" class="btn-primary btn-sm">Approve</button>
                    </form>
                    <button type="button" @click="rejecting = !rejecting" class="btn-secondary btn-sm">Reject</button>
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.carpooling.drivers.reject', $profile) }}" x-show="rejecting" x-cloak class="card card-body mt-4 space-y-3">
            @csrf
            <x-textarea label="Reason for rejection" name="rejection_reason" rows="3" required />
            <div class="flex justify-end"><x-button type="submit" variant="danger" size="sm">Confirm Rejection</x-button></div>
        </form>

        @if ($profile->rejection_reason)
            <x-alert variant="danger" class="mt-4">{{ $profile->rejection_reason }}</x-alert>
        @endif

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="card card-body">
                    <h2 class="font-semibold text-slate-900 dark:text-white">Bio</h2>
                    <p class="mt-2 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $profile->bio ?: 'No bio provided.' }}</p>
                </div>

                <div class="card card-body mt-6">
                    <h2 class="font-semibold text-slate-900 dark:text-white">Cars</h2>
                    @if ($profile->cars->isEmpty())
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">No cars added yet.</p>
                    @else
                        <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                            @foreach ($profile->cars as $car)
                                <div class="rounded-lg border border-slate-100 p-3 dark:border-navy-800">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $car->display_name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $car->plate_number }} &middot; {{ $car->total_seats }} seats</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-4">
                <div class="card card-body">
                    <p class="text-sm text-slate-400">License Expiry</p>
                    <p class="font-medium text-slate-900 dark:text-white">{{ $profile->license_expiry?->format('M j, Y') ?? '—' }}</p>
                </div>
                <div class="card card-body">
                    <p class="text-sm text-slate-400">Total Earned</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-white">${{ number_format($profile->total_earned, 2) }}</p>
                </div>
                @if ($profile->reviewer)
                    <div class="card card-body">
                        <p class="text-sm text-slate-400">Reviewed By</p>
                        <p class="font-medium text-slate-900 dark:text-white">{{ $profile->reviewer->full_name }}</p>
                        <p class="text-xs text-slate-400">{{ $profile->reviewed_at?->format('M j, Y') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts::admin>
