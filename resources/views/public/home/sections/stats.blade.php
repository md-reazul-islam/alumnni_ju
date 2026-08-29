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
    x-intersect.once="$refs.alumni && animate($refs.alumni, {{ $stats['total_alumni'] }}); $refs.careers && animate($refs.careers, {{ $stats['career_opportunities'] }}); $refs.carpooling && animate($refs.carpooling, {{ $stats['carpooling_services'] }}); $refs.events && animate($refs.events, {{ $stats['active_events'] }}); $refs.matrimony && animate($refs.matrimony, {{ $stats['brides_grooms'] }}); $refs.books && animate($refs.books, {{ $stats['available_books'] }})"
>
    <div class="section-container py-8 sm:py-10">
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-6">
            <div class="group relative flex aspect-square flex-col items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-white p-3 text-center shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:border-navy-200 hover:shadow-lg dark:border-white/10 dark:bg-white/5 dark:hover:border-navy-400/30">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-navy-50 text-navy-700 transition-transform duration-300 group-hover:scale-110 dark:bg-navy-400/10 dark:text-navy-300">
                    <x-icon name="graduation-cap" class="h-5 w-5" />
                </div>
                <p x-ref="alumni" class="mt-2.5 text-2xl font-bold text-navy-900 dark:text-white sm:text-3xl">0</p>
                <p class="mt-1 text-xs font-medium text-slate-500 dark:text-slate-400">Total {{ \App\Models\Setting::get('general', 'site_text', config('app.name')) }}</p>
            </div>

            <div class="group relative flex aspect-square flex-col items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-white p-3 text-center shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:border-emerald-200 hover:shadow-lg dark:border-white/10 dark:bg-white/5 dark:hover:border-emerald-400/30">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 transition-transform duration-300 group-hover:scale-110 dark:bg-emerald-400/10 dark:text-emerald-300">
                    <x-icon name="briefcase" class="h-5 w-5" />
                </div>
                <p x-ref="careers" class="mt-2.5 text-2xl font-bold text-navy-900 dark:text-white sm:text-3xl">0</p>
                <p class="mt-1 text-xs font-medium text-slate-500 dark:text-slate-400">Career Opportunities</p>
            </div>

            <div class="group relative flex aspect-square flex-col items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-white p-3 text-center shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:border-sky-200 hover:shadow-lg dark:border-white/10 dark:bg-white/5 dark:hover:border-sky-400/30">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-sky-50 text-sky-700 transition-transform duration-300 group-hover:scale-110 dark:bg-sky-400/10 dark:text-sky-300">
                    <x-icon name="car" class="h-5 w-5" />
                </div>
                <p x-ref="carpooling" class="mt-2.5 text-2xl font-bold text-navy-900 dark:text-white sm:text-3xl">0</p>
                <p class="mt-1 text-xs font-medium text-slate-500 dark:text-slate-400">Carpooling Services</p>
            </div>

            <div class="group relative flex aspect-square flex-col items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-white p-3 text-center shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:border-gold-200 hover:shadow-lg dark:border-white/10 dark:bg-white/5 dark:hover:border-gold-400/30">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gold-50 text-gold-700 transition-transform duration-300 group-hover:scale-110 dark:bg-gold-400/10 dark:text-gold-300">
                    <x-icon name="calendar" class="h-5 w-5" />
                </div>
                <p x-ref="events" class="mt-2.5 text-2xl font-bold text-navy-900 dark:text-white sm:text-3xl">0</p>
                <p class="mt-1 text-xs font-medium text-slate-500 dark:text-slate-400">Upcoming Events</p>
            </div>

            <div class="group relative flex aspect-square flex-col items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-white p-3 text-center shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:border-rose-200 hover:shadow-lg dark:border-white/10 dark:bg-white/5 dark:hover:border-rose-400/30">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50 text-rose-700 transition-transform duration-300 group-hover:scale-110 dark:bg-rose-400/10 dark:text-rose-300">
                    <x-icon name="heart" class="h-5 w-5" />
                </div>
                <p x-ref="matrimony" class="mt-2.5 text-2xl font-bold text-navy-900 dark:text-white sm:text-3xl">0</p>
                <p class="mt-1 text-xs font-medium text-slate-500 dark:text-slate-400">Brides &amp; Grooms</p>
            </div>

            <div class="group relative flex aspect-square flex-col items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-white p-3 text-center shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:border-purple-200 hover:shadow-lg dark:border-white/10 dark:bg-white/5 dark:hover:border-purple-400/30">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-50 text-purple-700 transition-transform duration-300 group-hover:scale-110 dark:bg-purple-400/10 dark:text-purple-300">
                    <x-icon name="book-open" class="h-5 w-5" />
                </div>
                <p x-ref="books" class="mt-2.5 text-2xl font-bold text-navy-900 dark:text-white sm:text-3xl">0</p>
                <p class="mt-1 text-xs font-medium text-slate-500 dark:text-slate-400">Available Books</p>
            </div>
        </div>
    </div>
</section>
