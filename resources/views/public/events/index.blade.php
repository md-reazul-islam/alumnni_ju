<x-layouts::app>
    <div class="section-container py-12">
        <x-breadcrumb :items="[['label' => 'Events']]" class="mb-4" />

        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Alumni Events</h1>
        <p class="mt-1.5 text-slate-500 dark:text-slate-400">Reunions, workshops, webinars, and networking mixers.</p>

        @if ($events->isEmpty())
            <x-empty-state icon="calendar" title="No upcoming events" description="Check back soon — new events are added regularly." class="mt-8" />
        @else
            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($events as $event)
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

            <div class="mt-8">{{ $events->links() }}</div>
        @endif
    </div>
</x-layouts::app>
