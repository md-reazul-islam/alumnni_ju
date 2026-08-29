<x-layouts::app>
    <div>
      <div class="section-container py-8">
        <x-breadcrumb :items="[['label' => 'Media Advocacy', 'url' => route('media-advocacy.index')], ['label' => 'Published Media']]" onDark class="mb-4" />

        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">Published Media</h1>
                <p class="mt-1.5 text-navy-200">Promotions, posters, banners, videos, news, and blogs we've published.</p>
            </div>
            <x-button :href="route('media-advocacy.index')" variant="secondary" size="sm">Request a Service</x-button>
        </div>

        @if ($media->isEmpty())
            <x-empty-state icon="image" title="No published media yet" class="mt-8" />
        @else
            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($media as $item)
                    <div x-data="{ open: false }">
                        <button
                            type="button"
                            @click="open = true"
                            class="card block w-full overflow-hidden text-left transition hover:shadow-popover"
                        >
                            <div class="relative flex h-40 items-center justify-center bg-navy-100 text-navy-400 dark:bg-navy-800">
                                @if ($item->type === 'image' && $item->image_url)
                                    <img src="{{ $item->image_url }}" class="h-full w-full object-cover">
                                @elseif ($item->type === 'video' && $item->video_thumbnail_url)
                                    <img src="{{ $item->video_thumbnail_url }}" class="h-full w-full object-cover">
                                @else
                                    <x-icon name="video" class="h-8 w-8" />
                                @endif

                                @if ($item->type === 'video')
                                    <span class="absolute inset-0 flex items-center justify-center bg-black/20">
                                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white/90 text-navy-900">
                                            <x-icon name="play" class="h-5 w-5" />
                                        </span>
                                    </span>
                                @endif
                            </div>
                            <div class="card-body">
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $item->title }}</p>
                                @if ($item->tag)
                                    <span class="mt-1 inline-block rounded-full bg-navy-50 px-2 py-0.5 text-xs font-medium text-navy-600 dark:bg-navy-800 dark:text-navy-300">{{ $item->tag }}</span>
                                @endif
                                @if ($item->description)
                                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $item->description }}</p>
                                @endif
                            </div>
                        </button>

                        <template x-if="open">
                            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4" @click.self="open = false" @keydown.window.escape="open = false">
                                <button type="button" @click="open = false" class="absolute right-4 top-4 text-white hover:text-gold-400">
                                    <x-icon name="x" class="h-7 w-7" />
                                </button>

                                @if ($item->type === 'video' && $item->video_embed_url)
                                    <div class="aspect-video w-full max-w-3xl overflow-hidden rounded-lg">
                                        <iframe :src="open ? '{{ $item->video_embed_url }}' : ''" class="h-full w-full" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                                    </div>
                                @elseif ($item->image_url)
                                    <div class="max-h-full max-w-full overflow-auto">
                                        <img src="{{ $item->image_url }}" class="max-h-[85vh] max-w-full object-contain">
                                    </div>
                                @endif
                            </div>
                        </template>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">{{ $media->links() }}</div>
        @endif
      </div>
    </div>
</x-layouts::app>
