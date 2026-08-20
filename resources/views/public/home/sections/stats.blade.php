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
