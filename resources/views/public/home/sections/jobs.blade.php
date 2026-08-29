<section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
  <div class="section-container py-5 sm:py-7">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white sm:text-3xl">Career Opportunities</h2>
            <p class="mt-1.5 text-navy-200">{{ \App\Http\Controllers\Admin\SettingsController::resolveSectionDescription('jobs') }}</p>
        </div>
        <a href="{{ route('jobs.index') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
            Visit career center <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>

    @if ($jobs->isEmpty())
        <x-empty-state icon="briefcase" title="No job opportunities available" description="New postings from alumni employers will appear here." class="mt-8" />
    @else
        <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-6">
            @foreach ($jobs as $job)
                <a href="{{ route('jobs.show', $job) }}" class="card overflow-hidden transition hover:shadow-popover">
                    <div class="flex h-20 items-center justify-center bg-navy-50 text-navy-700 dark:bg-navy-800 dark:text-navy-200 sm:h-24">
                        @if ($job->company?->logo_url)
                            <img src="{{ $job->company->logo_url }}" class="h-full w-full object-cover" alt="{{ $job->displayCompanyName() }}">
                        @else
                            <x-icon name="briefcase" class="h-6 w-6" />
                        @endif
                    </div>
                    <div class="p-2.5">
                        <p class="w-full truncate text-xs font-semibold text-slate-900 dark:text-white sm:text-sm">{{ $job->title }}</p>
                        <p class="mt-1 w-full truncate text-[10px] text-slate-500 dark:text-slate-400 sm:text-xs">{{ $job->displayCompanyName() }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
  </div>
</section>
