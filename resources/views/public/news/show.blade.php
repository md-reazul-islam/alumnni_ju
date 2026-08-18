<x-layouts::app>
  <div class="overflow-hidden rounded-3xl bg-white shadow-xl dark:bg-navy-900">
    <div class="section-container max-w-3xl py-8">
        <x-breadcrumb :items="[['label' => 'News', 'url' => route('news.index')], ['label' => $article->title]]" class="mb-6" />

        @if ($article->featured_image_url)
            <img src="{{ $article->featured_image_url }}" class="h-64 w-full rounded-2xl object-cover" alt="{{ $article->title }}">
        @endif

        <div class="mt-6 flex flex-wrap items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
            @if ($article->category)<x-badge variant="info">{{ $article->category->name }}</x-badge>@endif
            <span>{{ $article->published_at?->format('F j, Y') }}</span>
            <span>&middot;</span>
            <span>By {{ $article->author->full_name }}</span>
        </div>

        <h1 class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">{{ $article->title }}</h1>

        <div class="prose prose-slate mt-6 max-w-none dark:prose-invert">
            {!! nl2br(e($article->body)) !!}
        </div>

        @if ($article->tags->isNotEmpty())
            <div class="mt-8 flex flex-wrap gap-2 border-t border-slate-100 pt-6 dark:border-navy-800">
                @foreach ($article->tags as $tag)
                    <x-badge variant="neutral">#{{ $tag->name }}</x-badge>
                @endforeach
            </div>
        @endif
    </div>
  </div>
</x-layouts::app>
