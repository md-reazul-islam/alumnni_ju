<x-layouts::app>
  <div class="overflow-hidden rounded-3xl bg-white shadow-xl dark:bg-navy-900">
    <div class="h-48 bg-gradient-to-r from-navy-900 to-navy-700 sm:h-56">
        @if ($profile->cover_image_url)
            <img src="{{ $profile->cover_image_url }}" class="h-full w-full object-cover" alt="">
        @endif
    </div>

    <div class="section-container">
        <div class="-mt-16 flex flex-col items-center sm:-mt-20 sm:flex-row sm:items-end sm:gap-6">
            <x-avatar :src="$user->avatar_url" :name="$user->full_name" size="xl" class="ring-4 ring-white dark:ring-navy-950" />

            <div class="mt-4 flex-1 text-center sm:mt-0 sm:pb-2 sm:text-left">
                <h1 class="flex items-center justify-center gap-2 text-2xl font-bold text-slate-900 dark:text-white sm:justify-start">
                    {{ $user->full_name }}
                    @if ($profile->isVerified())
                        <x-icon name="badge-check" class="h-5 w-5 text-navy-600" />
                    @endif
                </h1>
                <p class="mt-1 text-slate-500 dark:text-slate-400">
                    {{ $profile->job_title }} @if($profile->organization) at {{ $profile->organization }} @endif
                </p>
                <p class="mt-1 flex items-center justify-center gap-3 text-sm text-slate-400 sm:justify-start">
                    <span class="flex items-center gap-1"><x-icon name="graduation-cap" class="h-4 w-4" /> {{ $profile->degree?->abbreviation }} {{ $profile->graduation_year }}</span>
                    @if ($profile->city || $profile->country)
                        <span class="flex items-center gap-1"><x-icon name="map-pin" class="h-4 w-4" /> {{ collect([$profile->city, $profile->country])->filter()->implode(', ') }}</span>
                    @endif
                </p>
            </div>

            @auth
                @if (auth()->id() !== $user->id)
                    <div class="mt-4 flex gap-2 sm:mt-0 sm:pb-2">
                        @if (Route::has('connections.store'))
                            <button type="button" onclick="AlumniNetwork.sendConnectionRequest({{ $user->id }}, this)" class="btn-primary btn-sm">Connect</button>
                        @endif
                        @if (Route::has('messages.create'))
                            <x-button :href="route('messages.create', $user)" variant="secondary" size="sm">Message</x-button>
                        @endif
                    </div>
                @endif
            @endauth
        </div>

        <div class="mt-10 grid grid-cols-1 gap-8 pb-16 lg:grid-cols-3">
            <div class="space-y-8 lg:col-span-2">
                @if ($profile->bio)
                    <section class="card card-body">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">About</h2>
                        <p class="mt-3 whitespace-pre-line text-slate-600 dark:text-slate-300">{{ $profile->bio }}</p>
                    </section>
                @endif

                @if ($profile->employments->isNotEmpty())
                    <section class="card card-body">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Career</h2>
                        <div class="mt-4 space-y-5">
                            @foreach ($profile->employments as $job)
                                <div class="flex gap-4">
                                    <span class="mt-1 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-navy-50 text-navy-700 dark:bg-navy-800 dark:text-navy-200">
                                        <x-icon name="briefcase" class="h-5 w-5" />
                                    </span>
                                    <div>
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ $job->job_title }}</p>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $job->company?->name ?? $job->company_name }}</p>
                                        <p class="text-xs text-slate-400">
                                            {{ $job->start_date?->format('M Y') }} &ndash; {{ $job->is_current ? 'Present' : $job->end_date?->format('M Y') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($profile->educations->isNotEmpty())
                    <section class="card card-body">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Education</h2>
                        <div class="mt-4 space-y-5">
                            @foreach ($profile->educations as $education)
                                <div class="flex gap-4">
                                    <span class="mt-1 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-gold-50 text-gold-600 dark:bg-navy-800 dark:text-gold-300">
                                        <x-icon name="graduation-cap" class="h-5 w-5" />
                                    </span>
                                    <div>
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ $education->institution }}</p>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $education->degree }} &middot; {{ $education->field_of_study }}</p>
                                        <p class="text-xs text-slate-400">{{ $education->start_year }} &ndash; {{ $education->end_year }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($profile->achievements->isNotEmpty() || $profile->certifications->isNotEmpty() || $profile->publications->isNotEmpty() || $profile->projects->isNotEmpty())
                    <section class="card card-body">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Achievements &amp; Credentials</h2>
                        <div class="mt-4 space-y-4">
                            @foreach ($profile->achievements as $item)
                                <div class="flex items-start gap-3">
                                    <x-icon name="award" class="mt-0.5 h-4 w-4 flex-shrink-0 text-gold-500" />
                                    <div><p class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ $item->title }}</p><p class="text-xs text-slate-400">{{ $item->description }}</p></div>
                                </div>
                            @endforeach
                            @foreach ($profile->certifications as $item)
                                <div class="flex items-start gap-3">
                                    <x-icon name="badge-check" class="mt-0.5 h-4 w-4 flex-shrink-0 text-emerald-500" />
                                    <div><p class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ $item->name }}</p><p class="text-xs text-slate-400">{{ $item->issuing_organization }}</p></div>
                                </div>
                            @endforeach
                            @foreach ($profile->publications as $item)
                                <div class="flex items-start gap-3">
                                    <x-icon name="book-open" class="mt-0.5 h-4 w-4 flex-shrink-0 text-navy-500" />
                                    <div><p class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ $item->title }}</p><p class="text-xs text-slate-400">{{ $item->publisher }}</p></div>
                                </div>
                            @endforeach
                            @foreach ($profile->projects as $item)
                                <div class="flex items-start gap-3">
                                    <x-icon name="layers" class="mt-0.5 h-4 w-4 flex-shrink-0 text-sky-500" />
                                    <div><p class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ $item->title }}</p><p class="text-xs text-slate-400">{{ $item->description }}</p></div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            <div class="space-y-8">
                @if ($profile->skills->isNotEmpty())
                    <section class="card card-body">
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Skills</h2>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($profile->skills as $skill)
                                <x-badge variant="neutral">{{ $skill->name }}</x-badge>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($profile->interests->isNotEmpty())
                    <section class="card card-body">
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Interests</h2>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($profile->interests as $interest)
                                <x-badge variant="info">{{ $interest->name }}</x-badge>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($profile->linkedin_url || $profile->website_url)
                    <section class="card card-body">
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Social Links</h2>
                        <div class="mt-3 space-y-2">
                            @if ($profile->linkedin_url)
                                <a href="{{ $profile->linkedin_url }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-sm text-navy-700 hover:underline dark:text-navy-300">
                                    <x-icon name="link" class="h-4 w-4" /> LinkedIn
                                </a>
                            @endif
                            @if ($profile->website_url)
                                <a href="{{ $profile->website_url }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-sm text-navy-700 hover:underline dark:text-navy-300">
                                    <x-icon name="globe" class="h-4 w-4" /> Personal Website
                                </a>
                            @endif
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </div>
  </div>
</x-layouts::app>
