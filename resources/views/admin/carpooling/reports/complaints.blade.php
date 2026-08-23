<x-layouts::admin :title="'Carpooling Complaints'">
    <x-breadcrumb :items="[['label' => 'Carpooling'], ['label' => 'Reports', 'url' => route('admin.carpooling.reports.index')], ['label' => 'Complaints']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Complaints</h1>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    <form method="GET" class="mt-4 flex gap-3">
        <x-select label="" name="status" placeholder="All statuses" :options="['pending' => 'Pending', 'reviewed' => 'Reviewed', 'dismissed' => 'Dismissed', 'action_taken' => 'Action Taken']" :selected="request('status')" />
        <div class="flex items-end"><x-button type="submit" variant="secondary" size="sm">Filter</x-button></div>
    </form>

    @if ($complaints->isEmpty())
        <x-empty-state icon="triangle-alert" title="No complaints filed" class="mt-8" />
    @else
        <div class="mt-6 space-y-4">
            @foreach ($complaints as $complaint)
                @php
                    $target = $complaint->reportable;
                    $targetLabel = $target instanceof \App\Models\CarpoolBooking
                        ? "Trip #{$target->id}: {$target->schedule?->origin} \u{2192} {$target->schedule?->destination}"
                        : ($target?->full_name ?? 'Deleted user');
                @endphp
                <div class="card card-body">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $targetLabel }}</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Reported by {{ $complaint->reporter?->full_name ?? 'Unknown' }} &middot; {{ $complaint->created_at->diffForHumans() }}</p>
                            <p class="mt-2 text-sm text-slate-700 dark:text-slate-200">{{ $complaint->reason }}</p>
                        </div>
                        <x-badge :variant="match($complaint->status) { 'action_taken' => 'success', 'dismissed' => 'neutral', 'reviewed' => 'info', default => 'warning' }">
                            {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                        </x-badge>
                    </div>

                    @if ($complaint->status === 'pending')
                        <div class="mt-3 flex gap-2">
                            <form method="POST" action="{{ route('admin.carpooling.reports.complaints.resolve', $complaint) }}">
                                @csrf
                                <input type="hidden" name="status" value="dismissed">
                                <button type="submit" class="btn-secondary btn-sm">Dismiss</button>
                            </form>
                            <form method="POST" action="{{ route('admin.carpooling.reports.complaints.resolve', $complaint) }}">
                                @csrf
                                <input type="hidden" name="status" value="action_taken">
                                <button type="submit" class="btn-primary btn-sm">Mark Action Taken</button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $complaints->links() }}</div>
    @endif
</x-layouts::admin>
