@php
    $schedulesByDate = $carpoolSchedules
        ->groupBy(fn ($schedule) => $schedule->departure_date->format('Y-m-d'))
        ->map(fn ($group) => $group->map(fn ($schedule) => [
            'origin' => $schedule->origin,
            'destination' => $schedule->destination,
            'time' => \Illuminate\Support\Carbon::parse($schedule->departure_time)->format('g:i A'),
            'driver' => $schedule->driverProfile->user->full_name,
            'price' => number_format($schedule->price_per_seat, 2),
        ])->values());
@endphp

<section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
  <div class="section-container py-5 sm:py-7">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white sm:text-3xl">Carpooling</h2>
            <p class="mt-1.5 text-navy-200">{{ \App\Http\Controllers\Admin\SettingsController::resolveSectionDescription('carpooling') }}</p>
        </div>
        <a href="{{ route('carpooling.search') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
            Find a ride <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>

    @if ($carpoolSchedules->isEmpty())
        <x-empty-state icon="car" title="No trips scheduled yet" description="Approved trips from alumni drivers will appear on this calendar." class="mt-8" />
    @else
        <div
            x-data="{
                schedulesByDate: {{ \Illuminate\Support\Js::from($schedulesByDate) }},
                viewDate: new Date(),
                get monthLabel() {
                    return this.viewDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                },
                get weeks() {
                    const year = this.viewDate.getFullYear();
                    const month = this.viewDate.getMonth();
                    const firstDay = new Date(year, month, 1);
                    const startOffset = firstDay.getDay();
                    const daysInMonth = new Date(year, month + 1, 0).getDate();
                    const cells = [];
                    for (let i = 0; i < startOffset; i++) cells.push(null);
                    for (let d = 1; d <= daysInMonth; d++) {
                        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                        cells.push({ day: d, dateStr, trips: this.schedulesByDate[dateStr] || [] });
                    }
                    while (cells.length % 7 !== 0) cells.push(null);
                    const weeks = [];
                    for (let i = 0; i < cells.length; i += 7) weeks.push(cells.slice(i, i + 7));
                    return weeks;
                },
                prevMonth() { this.viewDate = new Date(this.viewDate.getFullYear(), this.viewDate.getMonth() - 1, 1); },
                nextMonth() { this.viewDate = new Date(this.viewDate.getFullYear(), this.viewDate.getMonth() + 1, 1); },
                goToDate(dateStr) { window.location.href = '{{ route('carpooling.search') }}?date=' + dateStr; },
            }"
            class="mt-8 grid grid-cols-1 gap-4 lg:grid-cols-5"
        >
            <div class="card p-3 lg:col-span-3">
                <div class="flex items-center justify-between">
                    <button type="button" @click="prevMonth()" class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-navy-800 dark:hover:text-white">
                        <x-icon name="chevron-left" class="h-4 w-4" />
                    </button>
                    <p class="text-sm font-semibold text-slate-900 dark:text-white" x-text="monthLabel"></p>
                    <button type="button" @click="nextMonth()" class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-navy-800 dark:hover:text-white">
                        <x-icon name="chevron-right" class="h-4 w-4" />
                    </button>
                </div>

                <div class="mt-3 grid grid-cols-7 gap-1 text-center text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                    <div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div>
                </div>

                <template x-for="(week, wi) in weeks" :key="wi">
                    <div class="mt-1 grid grid-cols-7 gap-1">
                        <template x-for="(cell, ci) in week" :key="ci">
                            <button
                                type="button"
                                :disabled="!cell"
                                @click="cell && cell.trips.length && goToDate(cell.dateStr)"
                                class="mx-auto flex aspect-square w-full max-w-9 flex-col items-center justify-center rounded-lg text-xs transition-colors"
                                :class="cell ? (cell.trips.length ? 'cursor-pointer bg-navy-50 font-semibold text-navy-900 hover:bg-navy-100 dark:bg-navy-800 dark:text-white' : 'text-slate-400 dark:text-slate-500') : ''"
                            >
                                <span x-show="cell" x-text="cell?.day"></span>
                                <span x-show="cell && cell.trips.length" class="mt-0.5 flex h-3 min-w-3 items-center justify-center rounded-full bg-navy-600 px-1 text-[8px] font-bold leading-none text-white dark:bg-navy-400 dark:text-navy-950" x-text="cell?.trips.length"></span>
                            </button>
                        </template>
                    </div>
                </template>
            </div>

            <div class="card card-body lg:col-span-2">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Next Trips</h3>
                <div class="mt-3 divide-y divide-slate-100 dark:divide-navy-800">
                    @foreach ($carpoolSchedules->take(5) as $schedule)
                        <div class="flex items-start gap-2 py-2 first:pt-0 last:pb-0">
                            <span class="mt-0.5 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-navy-50 text-navy-600 dark:bg-navy-800 dark:text-navy-300">
                                <x-icon name="car" class="h-3.5 w-3.5" />
                            </span>
                            <div class="min-w-0">
                                <p class="truncate text-xs font-medium text-slate-900 dark:text-white">{{ $schedule->origin }} &rarr; {{ $schedule->destination }}</p>
                                <p class="truncate text-[10px] text-slate-500 dark:text-slate-400">{{ $schedule->departure_date->format('M j') }} at {{ \Illuminate\Support\Carbon::parse($schedule->departure_time)->format('g:i A') }} &middot; ${{ number_format($schedule->price_per_seat, 2) }}/seat</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
  </div>
</section>
