<x-layouts::app>
    <div class="section-container py-12">
        <x-breadcrumb :items="[['label' => 'Alumni Stories']]" class="mb-4" />

        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Alumni Stories</h1>
                <p class="mt-1.5 text-slate-500 dark:text-slate-400">Inspiring journeys from graduates making an impact.</p>
            </div>
            @auth
                @if (Route::has('stories.create'))
                    <x-button :href="route('stories.create')" size="sm">Share Your Story</x-button>
                @endif
            @endauth
        </div>

        @if ($stories->isEmpty())
            <x-empty-state icon="book-open" title="No alumni stories published yet" class="mt-8" />
        @else
            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-3">
                @foreach ($stories as $story)
                    <a href="{{ route('stories.show', $story) }}" class="card overflow-hidden transition hover:shadow-popover">
                        <div class="flex h-40 items-center justify-center bg-gold-50 text-gold-500 dark:bg-navy-800">
                            @if ($story->cover_image_url)
                                <img src="{{ $story->cover_image_url }}" class="h-full w-full object-cover" alt="{{ $story->title }}">
                            @else
                                <x-icon name="book-open" class="h-10 w-10" />
                            @endif
                        </div>
                        <div class="card-body">
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $story->title }}</p>
                            <p class="mt-1 line-clamp-2 text-sm text-slate-500 dark:text-slate-400">{{ Str::limit(strip_tags($story->story), 110) }}</p>
                            <p class="mt-3 text-xs font-medium text-navy-600 dark:text-navy-300">{{ $story->alumniProfile->user->full_name }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">{{ $stories->links() }}</div>
        @endif
    </div>
</x-layouts::app>
