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
        <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-6">
            @foreach ($featuredAlumni as $profile)
                <div class="group relative flex aspect-square flex-col items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-white p-2.5 text-center shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:border-navy-200 hover:shadow-lg dark:border-white/10 dark:bg-white/5 dark:hover:border-navy-400/30">
                    <x-avatar :src="$profile->user->avatar_url" :name="$profile->user->full_name" size="lg" class="mx-auto flex-shrink-0 transition-transform duration-300 group-hover:scale-110" />
                    <p class="mt-2 w-full truncate text-xs font-semibold text-slate-900 dark:text-white sm:text-sm">{{ $profile->user->full_name }}</p>
                    <p class="w-full truncate text-[10px] text-slate-500 dark:text-slate-400 sm:text-xs">{{ $profile->degree?->abbreviation }} {{ $profile->graduation_year }}</p>
                    <p class="mt-0.5 w-full truncate text-[9px] text-slate-400 sm:text-[11px]">{{ $profile->job_title }} @if($profile->organization) at {{ $profile->organization }} @endif</p>
                </div>
            @endforeach
        </div>
    @endif
  </div>
</section>
