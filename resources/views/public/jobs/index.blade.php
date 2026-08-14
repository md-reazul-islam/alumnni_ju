<x-layouts::app>
    <div class="section-container py-12">
        <x-breadcrumb :items="[['label' => 'Career Center']]" class="mb-4" />

        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Career Center</h1>
                <p class="mt-1.5 text-slate-500 dark:text-slate-400">Jobs, internships, and remote opportunities shared by alumni employers.</p>
            </div>
            @auth
                @if (Route::has('jobs.create'))
                    <x-button :href="route('jobs.create')" size="sm">Post a Job</x-button>
                @endif
            @endauth
        </div>

        <form method="GET" class="mt-6 flex flex-col gap-3 sm:flex-row">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title or company"
                   class="form-input sm:max-w-xs" />
            <select name="type" class="form-select sm:max-w-xs">
                <option value="">All employment types</option>
                @foreach (['full_time' => 'Full Time', 'part_time' => 'Part Time', 'internship' => 'Internship', 'remote' => 'Remote', 'contract' => 'Contract', 'freelance' => 'Freelance'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-button type="submit" variant="secondary">Filter</x-button>
        </form>

        @if ($jobs->isEmpty())
            <x-empty-state icon="briefcase" title="No job opportunities available" description="New postings from alumni employers will appear here." class="mt-8" />
        @else
            <div class="mt-8 space-y-4">
                @foreach ($jobs as $job)
                    <a href="{{ route('jobs.show', $job) }}" class="card card-body flex flex-col gap-3 transition hover:shadow-popover sm:flex-row sm:items-center">
                        <span class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-navy-50 text-navy-700 dark:bg-navy-800 dark:text-navy-200">
                            <x-icon name="briefcase" class="h-6 w-6" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-slate-900 dark:text-white">{{ $job->title }}</p>
                            <p class="truncate text-sm text-slate-500 dark:text-slate-400">{{ $job->displayCompanyName() }} &middot; {{ $job->location }}</p>
                        </div>
                        <x-badge variant="neutral">{{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}</x-badge>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">{{ $jobs->links() }}</div>
        @endif
    </div>
</x-layouts::app>
