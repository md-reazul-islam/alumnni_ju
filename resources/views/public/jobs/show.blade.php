<x-layouts::app>
  <div class="overflow-hidden rounded-3xl bg-white shadow-xl dark:bg-navy-900">
    <div class="section-container max-w-4xl py-8">
        <x-breadcrumb :items="[['label' => 'Career Center', 'url' => route('jobs.index')], ['label' => $job->title]]" class="mb-6" />

        <div class="card card-body">
            <div class="flex items-start gap-4">
                <span class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-xl bg-navy-50 text-navy-700 dark:bg-navy-800 dark:text-navy-200">
                    <x-icon name="briefcase" class="h-7 w-7" />
                </span>
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $job->title }}</h1>
                    <p class="mt-1 text-slate-500 dark:text-slate-400">{{ $job->displayCompanyName() }} &middot; {{ $job->location }}</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <x-badge variant="neutral">{{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}</x-badge>
                        @if ($job->industry)<x-badge variant="info">{{ $job->industry }}</x-badge>@endif
                        @if ($job->salary_min || $job->salary_max)
                            <x-badge variant="success">
                                {{ $job->salary_currency }} {{ number_format((float) $job->salary_min) }} &ndash; {{ number_format((float) $job->salary_max) }}
                            </x-badge>
                        @endif
                    </div>
                </div>
            </div>

            <div class="prose prose-slate mt-8 max-w-none dark:prose-invert">
                <h2>Description</h2>
                <p class="whitespace-pre-line">{{ $job->description }}</p>

                @if ($job->requirements)
                    <h2>Requirements</h2>
                    <p class="whitespace-pre-line">{{ $job->requirements }}</p>
                @endif
            </div>

            @if ($job->deadline)
                <p class="mt-6 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                    <x-icon name="clock" class="h-4 w-4" /> Apply before {{ $job->deadline->format('F j, Y') }}
                </p>
            @endif

            <div class="mt-8 flex flex-wrap gap-3 border-t border-slate-100 pt-6 dark:border-navy-800">
                @auth
                    @if ($hasApplied)
                        <x-badge variant="success" class="text-sm">You've applied to this job</x-badge>
                    @elseif ($job->application_url)
                        <x-button :href="$job->application_url" variant="primary">Apply Externally</x-button>
                    @else
                        <form method="POST" action="{{ route('jobs.apply', $job) }}">
                            @csrf
                            <x-button type="submit">Apply Now</x-button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('jobs.save', $job) }}">
                        @csrf
                        <x-button type="submit" variant="secondary">{{ $isSaved ? 'Unsave' : 'Save Job' }}</x-button>
                    </form>
                @else
                    <x-button :href="route('login')">Log In to Apply</x-button>
                @endauth

                <x-share-button :url="route('jobs.show', $job)" :title="$job->title" label="Share this job" class="!bg-slate-100 dark:!bg-navy-800" />
            </div>
        </div>
    </div>
  </div>
</x-layouts::app>
