<x-layouts::app>
  <div class="overflow-hidden rounded-3xl bg-white shadow-xl dark:bg-navy-900">
    <div class="section-container max-w-3xl py-8">
        <x-breadcrumb :items="[['label' => 'Alumni Stories', 'url' => route('stories.index')], ['label' => $story->title]]" class="mb-6" />

        @if ($story->cover_image_url)
            <img src="{{ $story->cover_image_url }}" class="h-64 w-full rounded-2xl object-cover" alt="{{ $story->title }}">
        @endif

        <div class="mt-6 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <x-avatar :src="$story->alumniProfile->user->avatar_url" :name="$story->alumniProfile->user->full_name" />
                <div>
                    @can('view', $story->alumniProfile)
                        <a href="{{ route('alumni.profile.show', $story->alumniProfile->user) }}" class="text-sm font-semibold text-slate-900 hover:underline dark:text-white">
                            {{ $story->alumniProfile->user->full_name }}
                        </a>
                    @else
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $story->alumniProfile->user->full_name }}</p>
                    @endcan
                    <p class="text-xs text-slate-400">{{ $story->career_highlight }}</p>
                </div>
            </div>
            <x-share-button :url="route('stories.show', $story)" :title="$story->title" label="Share this story" class="flex-shrink-0 !bg-slate-100 dark:!bg-navy-800" />
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
  </div>
</x-layouts::app>
