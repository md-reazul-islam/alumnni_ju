<section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
  <div class="section-container py-5 sm:py-7">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white sm:text-3xl">Matrimony</h2>
            <p class="mt-1.5 text-navy-200">Admin-reviewed profiles for alumni, family, and friends looking to get married — in the US, Bangladesh, and beyond.</p>
        </div>
        <a href="{{ route('matrimony.search') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
            Browse profiles <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>

    @if ($matrimonyProfiles->isEmpty())
        <x-empty-state icon="heart" title="No profiles yet" description="Approved matrimony profiles will appear here." class="mt-8" />
    @else
        <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-4">
            @foreach ($matrimonyProfiles as $profile)
                <a href="{{ route('matrimony.show', $profile) }}" class="card overflow-hidden transition hover:shadow-popover">
                    <div class="flex aspect-square items-center justify-center bg-slate-100 dark:bg-navy-800">
                        @if ($profile->photo_visibility === 'public' && $profile->primary_photo)
                            <img src="{{ asset('storage/' . $profile->primary_photo->path) }}" class="h-full w-full object-cover">
                        @else
                            <x-icon name="user" class="h-10 w-10 text-slate-300 dark:text-navy-600" />
                        @endif
                    </div>
                    <div class="p-3">
                        <div class="flex items-center gap-1">
                            <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $profile->display_name }}, {{ $profile->age }}</p>
                            @if ($profile->is_verified)
                                <x-icon name="badge-check" class="h-3.5 w-3.5 flex-shrink-0 text-navy-600 dark:text-navy-300" />
                            @endif
                        </div>
                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $profile->city ? $profile->city . ', ' : '' }}{{ $profile->country }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
  </div>
</section>
