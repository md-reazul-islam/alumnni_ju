<x-layouts::app>
    {{-- Hero --}}
    @if (\App\Models\Setting::get('homepage', 'show_hero', true) !== '0')
    <x-hero-slider :slides="$sliders">
        <section class="relative overflow-hidden rounded-3xl bg-navy-950 text-white shadow-xl">
            <div class="absolute inset-0 opacity-[0.06]" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 28px 28px;"></div>

            <div class="section-container relative py-14 sm:py-20 lg:py-24">
                <div class="mx-auto max-w-3xl text-center">
                    <h1 class="text-3xl font-bold leading-tight sm:text-4xl lg:text-5xl">
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
    </x-hero-slider>
    @endif

    {{-- Stats --}}
    @if (\App\Models\Setting::get('homepage', 'show_stats', true) !== '0')
    <section
        x-data="{
            animate(el, target) {
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
        x-intersect.once="$refs.alumni && animate($refs.alumni, {{ $stats['total_alumni'] }}); $refs.verified && animate($refs.verified, {{ $stats['verified_alumni'] }}); $refs.countries && animate($refs.countries, {{ $stats['countries'] }}); $refs.events && animate($refs.events, {{ $stats['active_events'] }})"
    >
        <div class="section-container py-8 sm:py-10">
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 sm:gap-6">
                <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:border-navy-200 hover:shadow-lg dark:border-white/10 dark:bg-white/5 dark:hover:border-navy-400/30">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-navy-50 text-navy-700 transition-transform duration-300 group-hover:scale-110 dark:bg-navy-400/10 dark:text-navy-300">
                        <x-icon name="graduation-cap" class="h-6 w-6" />
                    </div>
                    <p x-ref="alumni" class="mt-4 text-3xl font-bold text-navy-900 dark:text-white sm:text-4xl">0</p>
                    <p class="mt-1.5 text-sm font-medium text-slate-500 dark:text-slate-400">Total Alumni</p>
                    <span class="mx-auto mt-3 block h-0.5 w-8 rounded-full bg-navy-200 transition-all duration-300 group-hover:w-12 dark:bg-navy-500/40"></span>
                </div>

                <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:border-emerald-200 hover:shadow-lg dark:border-white/10 dark:bg-white/5 dark:hover:border-emerald-400/30">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 transition-transform duration-300 group-hover:scale-110 dark:bg-emerald-400/10 dark:text-emerald-300">
                        <x-icon name="badge-check" class="h-6 w-6" />
                    </div>
                    <p x-ref="verified" class="mt-4 text-3xl font-bold text-navy-900 dark:text-white sm:text-4xl">0</p>
                    <p class="mt-1.5 text-sm font-medium text-slate-500 dark:text-slate-400">Verified Alumni</p>
                    <span class="mx-auto mt-3 block h-0.5 w-8 rounded-full bg-emerald-200 transition-all duration-300 group-hover:w-12 dark:bg-emerald-500/40"></span>
                </div>

                <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:border-sky-200 hover:shadow-lg dark:border-white/10 dark:bg-white/5 dark:hover:border-sky-400/30">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-sky-700 transition-transform duration-300 group-hover:scale-110 dark:bg-sky-400/10 dark:text-sky-300">
                        <x-icon name="globe" class="h-6 w-6" />
                    </div>
                    <p x-ref="countries" class="mt-4 text-3xl font-bold text-navy-900 dark:text-white sm:text-4xl">0</p>
                    <p class="mt-1.5 text-sm font-medium text-slate-500 dark:text-slate-400">Countries Represented</p>
                    <span class="mx-auto mt-3 block h-0.5 w-8 rounded-full bg-sky-200 transition-all duration-300 group-hover:w-12 dark:bg-sky-500/40"></span>
                </div>

                <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:border-gold-200 hover:shadow-lg dark:border-white/10 dark:bg-white/5 dark:hover:border-gold-400/30">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-gold-50 text-gold-700 transition-transform duration-300 group-hover:scale-110 dark:bg-gold-400/10 dark:text-gold-300">
                        <x-icon name="calendar" class="h-6 w-6" />
                    </div>
                    <p x-ref="events" class="mt-4 text-3xl font-bold text-navy-900 dark:text-white sm:text-4xl">0</p>
                    <p class="mt-1.5 text-sm font-medium text-slate-500 dark:text-slate-400">Upcoming Events</p>
                    <span class="mx-auto mt-3 block h-0.5 w-8 rounded-full bg-gold-300 transition-all duration-300 group-hover:w-12 dark:bg-gold-500/40"></span>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Featured Alumni --}}
    @if (\App\Models\Setting::get('homepage', 'show_featured_alumni', true) !== '0')
    <section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
      <div class="section-container py-5 sm:py-7">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white sm:text-3xl">Featured Alumni</h2>
                <p class="mt-1.5 text-navy-200">Meet graduates making an impact around the world.</p>
            </div>
            <a href="{{ route('alumni.directory') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
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
      </div>
    </section>
    @endif

    {{-- Upcoming Events --}}
    @if (\App\Models\Setting::get('homepage', 'show_events', true) !== '0')
    <section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
        <div class="section-container py-5 sm:py-7">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white sm:text-3xl">Upcoming Events</h2>
                    <p class="mt-1.5 text-navy-200">Reunions, workshops, and networking mixers near you.</p>
                </div>
                <a href="{{ route('events.index') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
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
    @endif

    {{-- Career Opportunities --}}
    @if (\App\Models\Setting::get('homepage', 'show_jobs', true) !== '0')
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
    @endif

    {{-- Marketplace --}}
    @if (\App\Models\Setting::get('homepage', 'show_marketplace', true) !== '0')
    <section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
      <div class="section-container py-5 sm:py-7">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white sm:text-3xl">Marketplace</h2>
                <p class="mt-1.5 text-navy-200">House rentals, property, and used items posted by alumni.</p>
            </div>
            <a href="{{ route('marketplace.index') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
                View all <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>

        @if ($marketplaceListings->isEmpty())
            <x-empty-state icon="shopping-bag" title="No listings available" description="Approved listings from alumni will appear here." class="mt-8" />
        @else
            <div class="mt-8">
                @include('public.home.partials.marketplace')
            </div>
        @endif
      </div>
    </section>
    @endif

    {{-- Alumni Stories --}}
    @if (\App\Models\Setting::get('homepage', 'show_stories', true) !== '0')
    <section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
        <div class="section-container py-5 sm:py-7">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white sm:text-3xl">Alumni Stories</h2>
                    <p class="mt-1.5 text-navy-200">Inspiring journeys from our graduates.</p>
                </div>
                <a href="{{ route('stories.index') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
                    Read all stories <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>

            @if ($stories->isEmpty())
                <x-empty-state icon="book-open" title="No alumni stories published yet" class="mt-8" />
            @else
                <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-3">
                    @foreach ($stories as $story)
                        <div class="card overflow-hidden transition hover:shadow-popover">
                            <a href="{{ route('stories.show', $story) }}" class="block">
                                <div class="flex h-40 items-center justify-center bg-gold-50 text-gold-500 dark:bg-navy-800">
                                    @if ($story->cover_image_url)
                                        <img src="{{ $story->cover_image_url }}" class="h-full w-full object-cover" alt="{{ $story->title }}">
                                    @else
                                        <x-icon name="book-open" class="h-10 w-10" />
                                    @endif
                                </div>
                                <div class="card-body pb-0">
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $story->title }}</p>
                                    <p class="mt-1 line-clamp-2 text-sm text-slate-500 dark:text-slate-400">{{ Str::limit(strip_tags($story->story), 100) }}</p>
                                </div>
                            </a>
                            <div class="card-body pt-3">
                                @can('view', $story->alumniProfile)
                                    <a href="{{ route('alumni.profile.show', $story->alumniProfile->user) }}" class="text-xs font-medium text-navy-600 hover:underline dark:text-navy-300">
                                        {{ $story->alumniProfile->user->full_name }}
                                    </a>
                                @else
                                    <p class="text-xs font-medium text-navy-600 dark:text-navy-300">{{ $story->alumniProfile->user->full_name }}</p>
                                @endcan
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
    @endif

    {{-- Gallery --}}
    @if (\App\Models\Setting::get('homepage', 'show_gallery', true) !== '0')
    <section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
        <div class="section-container py-5 sm:py-7">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white sm:text-3xl">Gallery</h2>
                    <p class="mt-1.5 text-navy-200">Moments from our alumni community.</p>
                </div>
                <a href="{{ route('gallery.index') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
                    View gallery <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>

            @if ($gallery->isEmpty())
                <x-empty-state icon="image" title="No gallery photos yet" class="mt-8" />
            @else
                <div class="mt-8">
                    <x-gallery-grid :photos="$gallery" />
                </div>
            @endif
        </div>
    </section>
    @endif

    {{-- Your Library --}}
    @if (\App\Models\Setting::get('homepage', 'show_library', true) !== '0')
    <section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
        <div class="section-container py-5 sm:py-7">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white sm:text-3xl">Your Library</h2>
                    <p class="mt-1.5 text-navy-200">Books donated by alumni, available to borrow.</p>
                </div>
                <a href="{{ route('library.index') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
                    View library <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>

            @if ($library->isEmpty())
                <x-empty-state icon="book-open" title="No books available yet" class="mt-8" />
            @else
                <div class="mt-8">
                    <x-book-grid :books="$library" />
                </div>
            @endif
        </div>
    </section>
    @endif

    {{-- News --}}
    @if (\App\Models\Setting::get('homepage', 'show_news', true) !== '0')
    <section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
      <div class="section-container py-5 sm:py-7">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white sm:text-3xl">News &amp; Announcements</h2>
                <p class="mt-1.5 text-navy-200">The latest from our institution and alumni association.</p>
            </div>
            <a href="{{ route('news.index') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
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
      </div>
    </section>
    @endif

    {{-- CTA --}}
    @if (\App\Models\Setting::get('homepage', 'show_cta', true) !== '0')
    <section class="overflow-hidden rounded-3xl bg-navy-900 shadow-xl">
        <div class="section-container flex flex-col items-center justify-between gap-6 py-8 text-center sm:flex-row sm:text-left">
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
    @endif
</x-layouts::app>
