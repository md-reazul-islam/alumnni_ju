<section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
  <div class="section-container py-5 sm:py-7">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white sm:text-3xl">Career Opportunities</h2>
            <p class="mt-1.5 text-navy-200">Jobs and internships shared by fellow alumni.</p>
        </div>
        <a href="{{ route('jobs.index') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
            Visit career center <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>

    @if ($jobs->isEmpty())
        <x-empty-state icon="briefcase" title="No job opportunities available" description="New postings from alumni employers will appear here." class="mt-8" />
    @else
        <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
            @foreach ($jobs as $job)
                <a href="{{ route('jobs.show', $job) }}" class="card card-body flex items-center gap-4 transition hover:shadow-popover">
                    <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-navy-50 text-navy-700 dark:bg-navy-800 dark:text-navy-200">
                        <x-icon name="briefcase" class="h-5 w-5" />
                    </span>
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-slate-900 dark:text-white">{{ $job->title }}</p>
                        <p class="truncate text-sm text-slate-500 dark:text-slate-400">{{ $job->displayCompanyName() }} &middot; {{ $job->location }}</p>
                    </div>
                    <x-badge variant="neutral" class="ml-auto flex-shrink-0">{{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}</x-badge>
                </a>
            @endforeach
        </div>
    @endif
  </div>
</section>
