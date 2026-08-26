<section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
  <div class="section-container py-5 sm:py-7">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white sm:text-3xl">News &amp; Announcements</h2>
            <p class="mt-1.5 text-navy-200">The latest from our institution and alumni association.</p>
        </div>
        <a href="{{ route('news.index') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
            View all news <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>

    @if ($news->isEmpty())
        <x-empty-state icon="newspaper" title="No news published yet" class="mt-8" />
    @else
        <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-6">
            @foreach ($news as $article)
                <a href="{{ route('news.show', $article) }}" class="card overflow-hidden transition hover:shadow-popover">
                    <div class="flex h-20 items-center justify-center bg-navy-100 text-navy-400 dark:bg-navy-800 sm:h-24">
                        @if ($article->featured_image_url)
                            <img src="{{ $article->featured_image_url }}" class="h-full w-full object-cover" alt="{{ $article->title }}">
                        @else
                            <x-icon name="newspaper" class="h-6 w-6" />
                        @endif
                    </div>
                    <div class="p-2.5">
                        <p class="w-full truncate text-[10px] font-medium text-navy-600 dark:text-navy-300 sm:text-xs">{{ $article->published_at?->format('M d, Y') }}</p>
                        <p class="mt-1 w-full truncate text-xs font-semibold text-slate-900 dark:text-white sm:text-sm">{{ $article->title }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
  </div>
</section>
