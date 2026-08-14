<x-layouts::app>
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-navy-950 text-white">
        <div class="absolute inset-0 opacity-[0.06]" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 28px 28px;"></div>

        <div class="section-container relative py-20 sm:py-28 lg:py-32">
            <div class="mx-auto max-w-3xl text-center">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-gold-300 ring-1 ring-inset ring-white/20">
                    <x-icon name="sparkles" class="h-3.5 w-3.5" /> {{ config('app.name') }}
                </span>

                <h1 class="mt-6 text-4xl font-bold leading-tight sm:text-5xl lg:text-6xl">
                    Connect. Engage. Inspire. <span class="text-gold-400">Give Back.</span>
                </h1>

                <p class="mx-auto mt-5 max-w-xl text-lg text-navy-200">
                    Reconnect with your university community and build meaningful professional relationships
                    with alumni around the world.
                </p>

                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <x-button :href="route('register')" variant="gold" class="w-full sm:w-auto">
                        Join the Alumni Network
                    </x-button>
                    <x-button :href="route('alumni.directory')" variant="secondary" class="w-full bg-white/10 text-white ring-white/20 hover:bg-white/20 sm:w-auto">
                        Explore Alumni Directory
                    </x-button>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section
        x-data="{
            counted: false,
            animate(el, target) {
                if (this.counted) return;
                let start = 0;
                const duration = 1200;
                const startTime = performance.now();
                const step = (now) => {
                    const progress = Math.min((now - startTime) / duration, 1);
                    el.textContent = Math.floor(progress * target).toLocaleString();
                    if (progress < 1) requestAnimationFrame(step);
                };
                requestAnimationFrame(step);
            }
        }"
        x-intersect.once="counted = true; $refs.alumni && animate($refs.alumni, {{ $stats['total_alumni'] }}); $refs.verified && animate($refs.verified, {{ $stats['verified_alumni'] }}); $refs.countries && animate($refs.countries, {{ $stats['countries'] }}); $refs.events && animate($refs.events, {{ $stats['active_events'] }})"
        class="border-b border-slate-200 bg-slate-50 dark:border-navy-800 dark:bg-navy-900"
    >
        <div class="section-container grid grid-cols-2 gap-8 py-12 sm:grid-cols-4">
            <div class="text-center">
                <p x-ref="alumni" class="text-3xl font-bold text-navy-900 dark:text-white">0</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Total Alumni</p>
            </div>
            <div class="text-center">
                <p x-ref="verified" class="text-3xl font-bold text-navy-900 dark:text-white">0</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Verified Alumni</p>
            </div>
            <div class="text-center">
                <p x-ref="countries" class="text-3xl font-bold text-navy-900 dark:text-white">0</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Countries Represented</p>
            </div>
            <div class="text-center">
                <p x-ref="events" class="text-3xl font-bold text-navy-900 dark:text-white">0</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Upcoming Events</p>
            </div>
        </div>
    </section>

    {{-- Featured Alumni --}}
    <section class="section-container py-16 sm:py-20">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl">Featured Alumni</h2>
                <p class="mt-1.5 text-slate-500 dark:text-slate-400">Meet graduates making an impact around the world.</p>
            </div>
            <a href="{{ route('alumni.directory') }}" class="hidden text-sm font-semibold text-navy-700 hover:text-navy-900 dark:text-navy-300 sm:flex sm:items-center sm:gap-1">
                View directory <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>

        @if ($featuredAlumni->isEmpty())
            <x-empty-state icon="graduation-cap" title="No featured alumni yet" description="Verified alumni profiles will be showcased here." class="mt-8" />
        @else
            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($featuredAlumni as $profile)
                    <div class="card card-body text-center">
                        <x-avatar :src="$profile->user->avatar_url" :name="$profile->user->full_name" size="lg" class="mx-auto" />
                        <p class="mt-4 font-semibold text-slate-900 dark:text-white">{{ $profile->user->full_name }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $profile->degree?->abbreviation }} {{ $profile->graduation_year }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ $profile->job_title }} @if($profile->organization) at {{ $profile->organization }} @endif</p>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Upcoming Events --}}
    <section class="border-t border-slate-100 bg-slate-50 py-16 dark:border-navy-800 dark:bg-navy-900 sm:py-20">
        <div class="section-container">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl">Upcoming Events</h2>
                    <p class="mt-1.5 text-slate-500 dark:text-slate-400">Reunions, workshops, and networking mixers near you.</p>
                </div>
                <a href="{{ route('events.index') }}" class="hidden text-sm font-semibold text-navy-700 hover:text-navy-900 dark:text-navy-300 sm:flex sm:items-center sm:gap-1">
                    View all events <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>

            @if ($upcomingEvents->isEmpty())
                <x-empty-state icon="calendar" title="No upcoming events" description="Check back soon for reunions, workshops, and networking events." class="mt-8" />
            @else
                <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($upcomingEvents as $event)
                        <a href="{{ route('events.show', $event) }}" class="card overflow-hidden transition hover:shadow-popover">
                            <div class="flex h-36 items-center justify-center bg-navy-100 text-navy-400 dark:bg-navy-800">
                                @if ($event->image_url)
                                    <img src="{{ $event->image_url }}" class="h-full w-full object-cover" alt="{{ $event->title }}">
                                @else
                                    <x-icon name="calendar" class="h-10 w-10" />
                                @endif
                            </div>
                            <div class="card-body">
                                <x-badge variant="info">{{ ucfirst(str_replace('_', ' ', $event->category)) }}</x-badge>
                                <p class="mt-2 font-semibold text-slate-900 dark:text-white">{{ $event->title }}</p>
                                <p class="mt-1 flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400">
                                    <x-icon name="calendar" class="h-4 w-4" /> {{ $event->event_date->format('M d, Y') }}
                                </p>
                                <p class="mt-1 flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400">
                                    <x-icon name="map-pin" class="h-4 w-4" /> {{ $event->mode === 'online' ? 'Online' : ($event->city ?? 'TBA') }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Career Opportunities --}}
    <section class="section-container py-16 sm:py-20">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl">Career Opportunities</h2>
                <p class="mt-1.5 text-slate-500 dark:text-slate-400">Jobs and internships shared by fellow alumni.</p>
            </div>
            <a href="{{ route('jobs.index') }}" class="hidden text-sm font-semibold text-navy-700 hover:text-navy-900 dark:text-navy-300 sm:flex sm:items-center sm:gap-1">
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
    </section>

    {{-- Alumni Stories --}}
    <section class="border-t border-slate-100 bg-slate-50 py-16 dark:border-navy-800 dark:bg-navy-900 sm:py-20">
        <div class="section-container">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl">Alumni Stories</h2>
                    <p class="mt-1.5 text-slate-500 dark:text-slate-400">Inspiring journeys from our graduates.</p>
                </div>
                <a href="{{ route('stories.index') }}" class="hidden text-sm font-semibold text-navy-700 hover:text-navy-900 dark:text-navy-300 sm:flex sm:items-center sm:gap-1">
                    Read all stories <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>

            @if ($stories->isEmpty())
                <x-empty-state icon="book-open" title="No alumni stories published yet" class="mt-8" />
            @else
                <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-3">
                    @foreach ($stories as $story)
                        <a href="{{ route('stories.show', $story) }}" class="card overflow-hidden transition hover:shadow-popover">
                            <div class="flex h-40 items-center justify-center bg-gold-50 text-gold-500 dark:bg-navy-800">
                                @if ($story->cover_image_url)
                                    <img src="{{ $story->cover_image_url }}" class="h-full w-full object-cover" alt="{{ $story->title }}">
                                @else
                                    <x-icon name="book-open" class="h-10 w-10" />
                                @endif
                            </div>
                            <div class="card-body">
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $story->title }}</p>
                                <p class="mt-1 line-clamp-2 text-sm text-slate-500 dark:text-slate-400">{{ Str::limit(strip_tags($story->story), 100) }}</p>
                                <p class="mt-3 text-xs font-medium text-navy-600 dark:text-navy-300">{{ $story->alumniProfile->user->full_name }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- News --}}
    <section class="section-container py-16 sm:py-20">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl">News &amp; Announcements</h2>
                <p class="mt-1.5 text-slate-500 dark:text-slate-400">The latest from our institution and alumni association.</p>
            </div>
            <a href="{{ route('news.index') }}" class="hidden text-sm font-semibold text-navy-700 hover:text-navy-900 dark:text-navy-300 sm:flex sm:items-center sm:gap-1">
                View all news <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>

        @if ($news->isEmpty())
            <x-empty-state icon="newspaper" title="No news published yet" class="mt-8" />
        @else
            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-3">
                @foreach ($news as $article)
                    <a href="{{ route('news.show', $article) }}" class="card overflow-hidden transition hover:shadow-popover">
                        <div class="flex h-36 items-center justify-center bg-navy-100 text-navy-400 dark:bg-navy-800">
                            @if ($article->featured_image_url)
                                <img src="{{ $article->featured_image_url }}" class="h-full w-full object-cover" alt="{{ $article->title }}">
                            @else
                                <x-icon name="newspaper" class="h-10 w-10" />
                            @endif
                        </div>
                        <div class="card-body">
                            <p class="text-xs font-medium text-navy-600 dark:text-navy-300">{{ $article->published_at?->format('M d, Y') }}</p>
                            <p class="mt-1 font-semibold text-slate-900 dark:text-white">{{ $article->title }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    {{-- CTA --}}
    <section class="bg-navy-900">
        <div class="section-container flex flex-col items-center justify-between gap-6 py-14 text-center sm:flex-row sm:text-left">
            <div>
                <h2 class="text-2xl font-bold text-white sm:text-3xl">Your university journey continues.</h2>
                <p class="mt-2 text-navy-200">Update your profile and stay connected with your alma mater.</p>
            </div>
            <div class="flex flex-shrink-0 flex-col gap-3 sm:flex-row">
                <x-button :href="route('register')" variant="gold">Become an Alumni Member</x-button>
                @auth
                    <x-button :href="route('profile.edit')" variant="secondary" class="bg-white/10 text-white ring-white/20 hover:bg-white/20">Update Your Profile</x-button>
                @else
                    <x-button :href="route('login')" variant="secondary" class="bg-white/10 text-white ring-white/20 hover:bg-white/20">Update Your Profile</x-button>
                @endauth
            </div>
        </div>
    </section>
</x-layouts::app>
