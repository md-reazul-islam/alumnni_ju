<section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
    <div class="section-container py-5 sm:py-7">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white sm:text-3xl">{{ \App\Models\Setting::get('general', 'site_text', config('app.name')) }} Stories</h2>
                <p class="mt-1.5 text-navy-200">Inspiring journeys from our graduates.</p>
            </div>
            <a href="{{ route('stories.index') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
                Read all stories <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>

        @if ($stories->isEmpty())
            <x-empty-state icon="book-open" title="No alumni stories published yet" class="mt-8" />
        @else
            <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-6">
                @foreach ($stories as $story)
                    <a href="{{ route('stories.show', $story) }}" class="card overflow-hidden transition hover:shadow-popover">
                        <div class="flex h-20 items-center justify-center bg-gold-50 text-gold-500 dark:bg-navy-800 sm:h-24">
                            @if ($story->cover_image_url)
                                <img src="{{ $story->cover_image_url }}" class="h-full w-full object-cover" alt="{{ $story->title }}">
                            @else
                                <x-icon name="book-open" class="h-6 w-6" />
                            @endif
                        </div>
                        <div class="p-2.5">
                            <p class="w-full truncate text-xs font-semibold text-slate-900 dark:text-white sm:text-sm">{{ $story->title }}</p>
                            <p class="mt-1 w-full truncate text-[10px] font-medium text-navy-600 dark:text-navy-300 sm:text-xs">{{ $story->alumniProfile->user->full_name }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>
