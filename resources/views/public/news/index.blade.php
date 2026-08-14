<x-layouts::app>
    <div class="section-container py-12">
        <x-breadcrumb :items="[['label' => 'News']]" class="mb-4" />

        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">News &amp; Announcements</h1>
        <p class="mt-1.5 text-slate-500 dark:text-slate-400">The latest updates from the university and alumni association.</p>

        @if ($articles->isEmpty())
            <x-empty-state icon="newspaper" title="No news published yet" class="mt-8" />
        @else
            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-3">
                @foreach ($articles as $article)
                    <a href="{{ route('news.show', $article) }}" class="card overflow-hidden transition hover:shadow-popover">
                        <div class="flex h-36 items-center justify-center bg-navy-100 text-navy-400 dark:bg-navy-800">
                            @if ($article->featured_image_url)
                                <img src="{{ $article->featured_image_url }}" class="h-full w-full object-cover" alt="{{ $article->title }}">
                            @else
                                <x-icon name="newspaper" class="h-10 w-10" />
                            @endif
                        </div>
                        <div class="card-body">
                            @if ($article->category)<x-badge variant="info">{{ $article->category->name }}</x-badge>@endif
                            <p class="mt-2 text-xs font-medium text-navy-600 dark:text-navy-300">{{ $article->published_at?->format('M d, Y') }}</p>
                            <p class="mt-1 font-semibold text-slate-900 dark:text-white">{{ $article->title }}</p>
                            @if ($article->excerpt)
                                <p class="mt-1 line-clamp-2 text-sm text-slate-500 dark:text-slate-400">{{ $article->excerpt }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">{{ $articles->links() }}</div>
        @endif
    </div>
</x-layouts::app>
