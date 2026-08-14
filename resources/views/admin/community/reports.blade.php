<x-layouts::admin :title="'Reports'">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Content Reports</h1>

    @if ($reports->isEmpty())
        <x-empty-state icon="flag" title="No reports to review" class="mt-8" />
    @else
        <div class="mt-6 space-y-3">
            @foreach ($reports as $report)
                <div class="card card-body flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ class_basename($report->reportable_type) }} reported for "{{ $report->reason }}"</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Reported by {{ $report->reporter->full_name }} &middot; {{ $report->created_at->diffForHumans() }}</p>
                        @if ($report->details)
                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ $report->details }}</p>
                        @endif
                    </div>
                    @if ($report->status === 'pending')
                        <form method="POST" action="{{ route('admin.community.reports.dismiss', $report) }}" class="flex-shrink-0">
                            @csrf
                            <button type="submit" class="btn-secondary btn-sm">Dismiss</button>
                        </form>
                    @else
                        <x-badge variant="neutral">{{ ucfirst(str_replace('_', ' ', $report->status)) }}</x-badge>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $reports->links() }}</div>
    @endif
</x-layouts::admin>
