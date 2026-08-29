<section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
  <div class="section-container py-5 sm:py-7">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white sm:text-3xl">Media Advocacy</h2>
            <p class="mt-1.5 text-navy-200">Request a media service, or browse work we've already published.</p>
        </div>
        <a href="{{ route('media-advocacy.index') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
            Browse services <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
        <div>
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-navy-200">Media Advocacy &middot; request a service</h3>
                <a href="{{ route('media-advocacy.index') }}" class="text-xs font-medium text-gold-400 hover:text-gold-300">View all</a>
            </div>

            @if ($mediaAdvocacyCategories->isEmpty())
                <x-empty-state icon="megaphone" title="No services available yet" class="mt-4" />
            @else
                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @foreach ($mediaAdvocacyCategories as $cat)
                        <div class="card overflow-hidden">
                            <div class="flex h-20 items-center justify-center bg-navy-100 text-navy-400 dark:bg-navy-800">
                                @if ($cat->image_url)
                                    <img src="{{ $cat->image_url }}" class="h-full w-full object-cover">
                                @else
                                    <x-icon :name="$cat->icon ?: 'megaphone'" class="h-6 w-6" />
                                @endif
                            </div>
                            <div class="p-2.5">
                                <div class="flex items-center gap-1.5">
                                    <span class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-navy-50 text-navy-600 dark:bg-navy-800 dark:text-navy-300">
                                        <x-icon :name="$cat->icon ?: 'megaphone'" class="h-3 w-3" />
                                    </span>
                                    <p class="truncate text-xs font-semibold text-slate-900 dark:text-white sm:text-sm">{{ $cat->name }}</p>
                                </div>
                                <p class="mt-1 line-clamp-2 text-[10px] text-slate-500 dark:text-slate-400 sm:text-xs">{{ $cat->description }}</p>

                                @if (Route::has('media-advocacy.inquire') && auth()->check())
                                    <form method="POST" action="{{ route('media-advocacy.inquire', $cat) }}" class="mt-2">
                                        @csrf
                                        <button type="submit" class="w-full rounded-lg bg-navy-800 py-1.5 text-[10px] font-semibold uppercase tracking-wide text-white transition hover:bg-navy-700 sm:text-xs">
                                            I'm Interested
                                        </button>
                                    </form>
                                @elseif (Route::has('media-advocacy.inquire'))
                                    <a href="{{ route('login') }}" class="mt-2 block w-full rounded-lg bg-navy-800 py-1.5 text-center text-[10px] font-semibold uppercase tracking-wide text-white transition hover:bg-navy-700 sm:text-xs">
                                        I'm Interested
                                    </a>
                                @else
                                    <span class="mt-2 block w-full rounded-lg bg-slate-100 py-1.5 text-center text-[10px] font-semibold uppercase tracking-wide text-slate-400 dark:bg-navy-800 sm:text-xs">
                                        Coming soon
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-navy-200">Published Media &middot; our recent work</h3>
                <a href="{{ route('media-advocacy.published') }}" class="text-xs font-medium text-gold-400 hover:text-gold-300">View all</a>
            </div>

            @if ($publishedMedia->isEmpty())
                <x-empty-state icon="image" title="No published media yet" class="mt-4" />
            @else
                <div class="mt-3 grid grid-cols-3 gap-3">
                    @foreach ($publishedMedia as $item)
                        <div x-data="{ open: false }">
                            <button
                                type="button"
                                @click="open = true"
                                class="card block w-full overflow-hidden text-left transition hover:shadow-popover"
                            >
                                <div class="relative flex h-16 items-center justify-center bg-navy-100 text-navy-400 dark:bg-navy-800 sm:h-20">
                                    @if ($item->type === 'image' && $item->image_url)
                                        <img src="{{ $item->image_url }}" class="h-full w-full object-cover">
                                    @elseif ($item->type === 'video' && $item->video_thumbnail_url)
                                        <img src="{{ $item->video_thumbnail_url }}" class="h-full w-full object-cover">
                                    @else
                                        <x-icon name="video" class="h-6 w-6" />
                                    @endif

                                    @if ($item->type === 'video')
                                        <span class="absolute inset-0 flex items-center justify-center bg-black/20">
                                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white/90 text-navy-900">
                                                <x-icon name="play" class="h-3 w-3" />
                                            </span>
                                        </span>
                                    @endif
                                </div>
                                <p class="w-full truncate p-2 text-[10px] font-semibold text-slate-900 dark:text-white sm:text-xs">{{ $item->title }}</p>
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
            @endif
        </div>
    </div>
  </div>
</section>
