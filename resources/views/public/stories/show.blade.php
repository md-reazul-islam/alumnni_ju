<x-layouts::app>
    <div class="section-container max-w-3xl py-12">
        <x-breadcrumb :items="[['label' => 'Alumni Stories', 'url' => route('stories.index')], ['label' => $story->title]]" class="mb-6" />

        @if ($story->cover_image_url)
            <img src="{{ $story->cover_image_url }}" class="h-64 w-full rounded-2xl object-cover" alt="{{ $story->title }}">
        @endif

        <div class="mt-6 flex items-center gap-3">
            <x-avatar :src="$story->alumniProfile->user->avatar_url" :name="$story->alumniProfile->user->full_name" />
            <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $story->alumniProfile->user->full_name }}</p>
                <p class="text-xs text-slate-400">{{ $story->career_highlight }}</p>
            </div>
        </div>

        <h1 class="mt-6 text-3xl font-bold text-slate-900 dark:text-white">{{ $story->title }}</h1>

        <div class="prose prose-slate mt-6 max-w-none dark:prose-invert">
            <p class="whitespace-pre-line">{{ $story->story }}</p>
        </div>

        @if ($story->achievements)
            <div class="mt-8 card card-body">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Notable Achievements</h2>
                <p class="mt-2 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $story->achievements }}</p>
            </div>
        @endif
    </div>
</x-layouts::app>
