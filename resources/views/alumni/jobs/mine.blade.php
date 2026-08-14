<x-layouts::alumni :title="'My Job Postings'">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">My Job Postings</h1>
        <x-button :href="route('jobs.create')" size="sm"><x-icon name="plus" class="h-4 w-4" /> Post a Job</x-button>
    </div>

    @if ($jobs->isEmpty())
        <x-empty-state icon="briefcase" title="You haven't posted any jobs yet" class="mt-8" />
    @else
        <div class="mt-6 space-y-3">
            @foreach ($jobs as $job)
                <div class="card card-body flex items-center gap-4">
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-semibold text-slate-900 dark:text-white">{{ $job->title }}</p>
                        <p class="truncate text-sm text-slate-500 dark:text-slate-400">{{ $job->company_name }} &middot; {{ $job->applications_count }} applications</p>
                    </div>
                    <x-badge :variant="match($job->status) { 'approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger', default => 'neutral' }">
                        {{ ucfirst($job->status) }}
                    </x-badge>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $jobs->links() }}</div>
    @endif
</x-layouts::alumni>
