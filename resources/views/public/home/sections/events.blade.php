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
