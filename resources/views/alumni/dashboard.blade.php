@php
    $quickActions = [
        ['label' => 'Update Profile', 'icon' => 'square-pen', 'route' => 'alumni.profile.edit'],
        ['label' => 'Find Alumni', 'icon' => 'search', 'route' => 'alumni.directory'],
        ['label' => 'Upcoming Events', 'icon' => 'calendar', 'route' => 'events.index'],
        ['label' => 'Post a Job', 'icon' => 'briefcase', 'route' => 'jobs.create'],
        ['label' => 'Send Message', 'icon' => 'message-circle', 'route' => 'messages.index'],
        ['label' => 'Make Donation', 'icon' => 'heart', 'route' => 'donations.index'],
        ['label' => 'Find Mentor', 'icon' => 'handshake', 'route' => 'mentorship.index'],
        ['label' => 'My Gallery', 'icon' => 'image', 'route' => 'gallery.mine'],
        ['label' => 'Add Photo', 'icon' => 'camera', 'route' => 'gallery.create'],
        ['label' => 'My Library', 'icon' => 'book-open', 'route' => 'library.mine'],
        ['label' => 'Donate a Book', 'icon' => 'gift', 'route' => 'library.create'],
        ['label' => 'Post a Listing', 'icon' => 'tag', 'route' => 'marketplace.create'],
        ['label' => 'My Listings', 'icon' => 'shopping-bag', 'route' => 'marketplace.mine'],
    ];
@endphp

<x-layouts::alumni :title="'Dashboard'">
    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Welcome back, {{ $user->first_name }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Here's what's happening in your alumni network.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="h-2 w-32 overflow-hidden rounded-full bg-slate-100 dark:bg-navy-800">
                <div class="h-full rounded-full bg-navy-700" style="width: {{ $profile?->profile_completion ?? 0 }}%"></div>
            </div>
            <span class="text-sm font-medium text-slate-600 dark:text-slate-300">{{ $profile?->profile_completion ?? 0 }}% complete</span>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-7">
        @foreach ($quickActions as $action)
            @if (Route::has($action['route']))
                <a href="{{ route($action['route']) }}" class="card card-body flex flex-col items-center gap-2 py-4 text-center transition hover:shadow-popover">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-navy-50 text-navy-700 dark:bg-navy-800 dark:text-navy-200">
                        <x-icon :name="$action['icon']" class="h-5 w-5" />
                    </span>
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300">{{ $action['label'] }}</span>
                </a>
            @endif
        @endforeach
    </div>

    {{-- Stats --}}
    <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
        <x-stat-card label="Profile Views" :value="$stats['profile_views']" icon="eye" accent="navy" />
        <x-stat-card label="Connections" :value="$stats['connections']" icon="users" accent="sky" />
        <x-stat-card label="Events Registered" :value="$stats['events_registered']" icon="calendar-check" accent="emerald" />
        <x-stat-card label="Jobs Posted" :value="$stats['jobs_posted']" icon="briefcase" accent="gold" />
        <x-stat-card label="Donations" :value="'$' . number_format((float) $stats['donations'])" icon="dollar-sign" accent="emerald" />
        <x-stat-card label="Conversations" :value="$stats['conversations']" icon="message-circle" accent="sky" />
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            {{-- Upcoming Events --}}
            <section class="card card-body">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Upcoming Events</h2>
                    <a href="{{ route('events.index') }}" class="text-sm font-medium text-navy-700 hover:text-navy-900 dark:text-navy-300">View all</a>
                </div>

                @if ($upcomingEvents->isEmpty())
                    <x-empty-state icon="calendar" title="No upcoming events" class="mt-4" />
                @else
                    <div class="mt-4 space-y-3">
                        @foreach ($upcomingEvents as $event)
                            <a href="{{ route('events.show', $event) }}" class="flex items-center gap-3 rounded-lg p-2 hover:bg-slate-50 dark:hover:bg-navy-800">
                                <span class="flex h-11 w-11 flex-col items-center justify-center rounded-lg bg-navy-50 text-navy-800 dark:bg-navy-800 dark:text-white">
                                    <span class="text-[10px] font-semibold uppercase leading-none">{{ $event->event_date->format('M') }}</span>
                                    <span class="text-sm font-bold leading-none">{{ $event->event_date->format('d') }}</span>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $event->title }}</p>
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $event->mode === 'online' ? 'Online' : $event->city }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>

            {{-- Career Opportunities --}}
            <section class="card card-body">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Career Opportunities</h2>
                    <a href="{{ route('jobs.index') }}" class="text-sm font-medium text-navy-700 hover:text-navy-900 dark:text-navy-300">View all</a>
                </div>

                @if ($jobs->isEmpty())
                    <x-empty-state icon="briefcase" title="No job opportunities available" class="mt-4" />
                @else
                    <div class="mt-4 space-y-3">
                        @foreach ($jobs as $job)
                            <a href="{{ route('jobs.show', $job) }}" class="flex items-center gap-3 rounded-lg p-2 hover:bg-slate-50 dark:hover:bg-navy-800">
                                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-gold-50 text-gold-600 dark:bg-navy-800 dark:text-gold-300">
                                    <x-icon name="briefcase" class="h-5 w-5" />
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $job->title }}</p>
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $job->displayCompanyName() }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>

            {{-- Recent News --}}
            <section class="card card-body">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Recent News</h2>
                    <a href="{{ route('news.index') }}" class="text-sm font-medium text-navy-700 hover:text-navy-900 dark:text-navy-300">View all</a>
                </div>

                @if ($news->isEmpty())
                    <x-empty-state icon="newspaper" title="No news published yet" class="mt-4" />
                @else
                    <div class="mt-4 space-y-3">
                        @foreach ($news as $article)
                            <a href="{{ route('news.show', $article) }}" class="flex items-center gap-3 rounded-lg p-2 hover:bg-slate-50 dark:hover:bg-navy-800">
                                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-navy-50 text-navy-700 dark:bg-navy-800 dark:text-navy-200">
                                    <x-icon name="newspaper" class="h-5 w-5" />
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $article->title }}</p>
                                    <p class="text-xs text-slate-400">{{ $article->published_at?->format('M d, Y') }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <div class="space-y-6">
            {{-- Recommended Alumni --}}
            <section class="card card-body">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Recommended Alumni</h2>

                @if ($recommendedAlumni->isEmpty())
                    <x-empty-state icon="users" title="No recommendations yet" class="mt-4" />
                @else
                    <div class="mt-4 space-y-3">
                        @foreach ($recommendedAlumni as $rec)
                            <a href="{{ route('alumni.profile.show', $rec->user) }}" class="flex items-center gap-3 rounded-lg p-2 hover:bg-slate-50 dark:hover:bg-navy-800">
                                <x-avatar :src="$rec->user->avatar_url" :name="$rec->user->full_name" size="sm" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $rec->user->full_name }}</p>
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $rec->degree?->abbreviation }} {{ $rec->graduation_year }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>

            {{-- Announcements --}}
            <section class="card card-body">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Announcements</h2>

                @if ($announcements->isEmpty())
                    <x-empty-state icon="megaphone" title="No announcements" class="mt-4" />
                @else
                    <div class="mt-4 space-y-3">
                        @foreach ($announcements as $announcement)
                            <div class="rounded-lg border border-slate-100 p-3 dark:border-navy-800">
                                <p class="flex items-center gap-1.5 text-sm font-semibold text-slate-900 dark:text-white">
                                    @if ($announcement->is_pinned)<x-icon name="megaphone" class="h-3.5 w-3.5 text-gold-500" />@endif
                                    {{ $announcement->title }}
                                </p>
                                <p class="mt-1 line-clamp-2 text-xs text-slate-500 dark:text-slate-400">{{ $announcement->body }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-layouts::alumni>
