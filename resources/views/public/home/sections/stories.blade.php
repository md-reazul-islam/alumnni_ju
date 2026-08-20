<section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
    <div class="section-container py-5 sm:py-7">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white sm:text-3xl">Alumni Stories</h2>
                <p class="mt-1.5 text-navy-200">Inspiring journeys from our graduates.</p>
            </div>
            <a href="{{ route('stories.index') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
                Read all stories <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>

        @if ($stories->isEmpty())
            <x-empty-state icon="book-open" title="No alumni stories published yet" class="mt-8" />
        @else
            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-3">
                @foreach ($stories as $story)
                    <div class="card overflow-hidden transition hover:shadow-popover">
                        <a href="{{ route('stories.show', $story) }}" class="block">
                            <div class="flex h-40 items-center justify-center bg-gold-50 text-gold-500 dark:bg-navy-800">
                                @if ($story->cover_image_url)
                                    <img src="{{ $story->cover_image_url }}" class="h-full w-full object-cover" alt="{{ $story->title }}">
                                @else
                                    <x-icon name="book-open" class="h-10 w-10" />
                                @endif
                            </div>
                            <div class="card-body pb-0">
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $story->title }}</p>
                                <p class="mt-1 line-clamp-2 text-sm text-slate-500 dark:text-slate-400">{{ Str::limit(strip_tags($story->story), 100) }}</p>
                            </div>
                        </a>
                        <div class="card-body pt-3">
                            @can('view', $story->alumniProfile)
                                <a href="{{ route('alumni.profile.show', $story->alumniProfile->user) }}" class="text-xs font-medium text-navy-600 hover:underline dark:text-navy-300">
                                    {{ $story->alumniProfile->user->full_name }}
                                </a>
                            @else
                                <p class="text-xs font-medium text-navy-600 dark:text-navy-300">{{ $story->alumniProfile->user->full_name }}</p>
                            @endcan
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
