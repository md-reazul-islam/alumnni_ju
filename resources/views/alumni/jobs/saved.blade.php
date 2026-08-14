<x-layouts::alumni :title="'Saved Jobs'">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Saved Jobs</h1>

    @if ($savedJobs->isEmpty())
        <x-empty-state icon="bookmark" title="No saved jobs" description="Save interesting opportunities from the career center to view them here." class="mt-8" />
    @else
        <div class="mt-6 space-y-3">
            @foreach ($savedJobs as $saved)
                <a href="{{ route('jobs.show', $saved->jobPosting) }}" class="card card-body flex items-center gap-4 transition hover:shadow-popover">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-navy-50 text-navy-700 dark:bg-navy-800 dark:text-navy-200">
                        <x-icon name="briefcase" class="h-5 w-5" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-semibold text-slate-900 dark:text-white">{{ $saved->jobPosting->title }}</p>
                        <p class="truncate text-sm text-slate-500 dark:text-slate-400">{{ $saved->jobPosting->displayCompanyName() }}</p>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $savedJobs->links() }}</div>
    @endif
</x-layouts::alumni>
