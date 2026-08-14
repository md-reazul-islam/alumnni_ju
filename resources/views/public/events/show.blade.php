<x-layouts::app>
    <div class="section-container max-w-4xl py-12">
        <x-breadcrumb :items="[['label' => 'Events', 'url' => route('events.index')], ['label' => $event->title]]" class="mb-6" />

        <div class="card overflow-hidden">
            <div class="flex h-56 items-center justify-center bg-navy-100 text-navy-400 dark:bg-navy-800 sm:h-72">
                @if ($event->image_url)
                    <img src="{{ $event->image_url }}" class="h-full w-full object-cover" alt="{{ $event->title }}">
                @else
                    <x-icon name="calendar" class="h-14 w-14" />
                @endif
            </div>

            <div class="card-body">
                <x-badge variant="info">{{ ucfirst(str_replace('_', ' ', $event->category)) }}</x-badge>
                <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">{{ $event->title }}</h1>

                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <p class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><x-icon name="calendar" class="h-4 w-4 text-slate-400" /> {{ $event->event_date->format('l, F j, Y') }}</p>
                    @if ($event->start_time)
                        <p class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><x-icon name="clock" class="h-4 w-4 text-slate-400" /> {{ \Illuminate\Support\Carbon::parse($event->start_time)->format('g:i A') }} @if($event->end_time) &ndash; {{ \Illuminate\Support\Carbon::parse($event->end_time)->format('g:i A') }} @endif</p>
                    @endif
                    <p class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <x-icon name="map-pin" class="h-4 w-4 text-slate-400" />
                        {{ $event->mode === 'online' ? 'Online Event' : collect([$event->venue, $event->city, $event->country])->filter()->implode(', ') }}
                    </p>
                    @if ($event->max_participants)
                        <p class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300"><x-icon name="users" class="h-4 w-4 text-slate-400" /> Limited to {{ $event->max_participants }} participants</p>
                    @endif
                </div>

                @if ($event->description)
                    <div class="prose prose-slate mt-6 max-w-none dark:prose-invert">
                        <p class="whitespace-pre-line">{{ $event->description }}</p>
                    </div>
                @endif

                <div class="mt-8 flex flex-wrap items-center gap-3 border-t border-slate-100 pt-6 dark:border-navy-800">
                    @auth
                        @if ($isRegistered)
                            <x-badge variant="success" class="text-sm">You're registered for this event</x-badge>
                            <form method="POST" action="{{ route('events.cancel', $event) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-red-600 hover:underline">Cancel registration</button>
                            </form>
                        @elseif (Route::has('events.register') && $event->isRegistrationOpen())
                            <form method="POST" action="{{ route('events.register', $event) }}">
                                @csrf
                                <x-button type="submit">Register for this Event</x-button>
                            </form>
                        @elseif ($event->isFull())
                            <x-badge variant="warning" class="text-sm">This event is fully booked</x-badge>
                        @else
                            <x-badge variant="neutral" class="text-sm">Registration is closed</x-badge>
                        @endif
                    @else
                        <x-button :href="route('login')">Log In to Register</x-button>
                    @endauth

                    @php
                        $calendarDates = $event->event_date->format('Ymd') . ($event->start_time ? 'T' . str_replace(':', '', \Illuminate\Support\Carbon::parse($event->start_time)->format('Hi')) . '00' : '');
                        $calendarUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE&text=' . urlencode($event->title)
                            . '&dates=' . $calendarDates . '/' . $calendarDates
                            . '&details=' . urlencode($event->description ?? '')
                            . '&location=' . urlencode($event->mode === 'online' ? ($event->meeting_url ?? '') : collect([$event->venue, $event->city])->filter()->implode(', '));
                    @endphp
                    <x-button :href="$calendarUrl" variant="ghost" size="sm">
                        <x-icon name="calendar-days" class="h-4 w-4" /> Add to Calendar
                    </x-button>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
