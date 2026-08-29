<section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
  <div class="section-container py-5 sm:py-7">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white sm:text-3xl">Matrimony</h2>
            <p class="mt-1.5 text-navy-200">{{ \App\Http\Controllers\Admin\SettingsController::resolveSectionDescription('matrimony') }}</p>
        </div>
        <a href="{{ route('matrimony.search') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
            Browse profiles <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>

    @if ($matrimonyProfiles->isEmpty())
        <x-empty-state icon="heart" title="No profiles yet" description="Approved matrimony profiles will appear here." class="mt-8" />
    @else
        <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-6">
            @foreach ($matrimonyProfiles as $profile)
                <a href="{{ route('matrimony.show', $profile) }}" class="group relative flex aspect-square flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:border-navy-200 hover:shadow-lg dark:border-white/10 dark:bg-white/5 dark:hover:border-navy-400/30">
                    <div class="flex flex-1 items-center justify-center overflow-hidden bg-slate-100 dark:bg-navy-800">
                        @if ($profile->photo_visibility === 'public' && $profile->primary_photo)
                            <img src="{{ asset('storage/' . $profile->primary_photo->path) }}" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110">
                        @else
                            <x-icon name="user" class="h-8 w-8 text-slate-300 dark:text-navy-600" />
                        @endif
                    </div>
                    <div class="p-2 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <p class="w-full truncate text-xs font-semibold text-slate-900 dark:text-white sm:text-sm">{{ $profile->display_name }}, {{ $profile->age }}</p>
                            @if ($profile->is_verified)
                                <x-icon name="badge-check" class="h-3 w-3 flex-shrink-0 text-navy-600 dark:text-navy-300" />
                            @endif
                        </div>
                        <p class="w-full truncate text-[10px] text-slate-500 dark:text-slate-400 sm:text-xs">{{ $profile->city ? $profile->city . ', ' : '' }}{{ $profile->country }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
  </div>
</section>
