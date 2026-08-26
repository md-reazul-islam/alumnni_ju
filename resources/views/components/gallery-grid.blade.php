@props(['photos', 'compact' => false])

<div
    x-data="photoGallery(@js($photos->map(fn ($p) => ['url' => $p->image_url, 'description' => $p->description, 'name' => $p->user->full_name ?? null])->values()))"
>
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 {{ $compact ? 'lg:grid-cols-6' : 'lg:grid-cols-4' }}">
        @foreach ($photos as $index => $photo)
            <button
                type="button"
                @click="show({{ $index }})"
                class="group relative aspect-square overflow-hidden rounded-2xl bg-slate-100 shadow-sm dark:bg-navy-900"
            >
                <img
                    src="{{ $photo->image_url }}"
                    alt="{{ $photo->description ?: 'Gallery photo' }}"
                    loading="lazy"
                    class="h-full w-full object-cover transition duration-500 group-hover:scale-110"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/0 to-black/0 opacity-0 transition duration-300 group-hover:opacity-100"></div>
                <div class="absolute inset-0 flex items-center justify-center opacity-0 transition duration-300 group-hover:opacity-100">
                    <x-icon name="maximize-2" class="h-6 w-6 text-white" />
                </div>
                @if ($photo->description)
                    <p class="absolute inset-x-0 bottom-0 line-clamp-2 p-3 text-left text-xs font-medium text-white opacity-0 transition duration-300 group-hover:opacity-100">
                        {{ $photo->description }}
                    </p>
                @endif
            </button>
        @endforeach
    </div>

    {{-- Lightbox --}}
    <div
        x-show="open"
        x-cloak
        x-transition.opacity
        @keydown.escape.window="close()"
        @keydown.arrow-right.window="next()"
        @keydown.arrow-left.window="prev()"
        @click.self="close()"
        class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-black/90 p-4 sm:p-8"
    >
        <button
            type="button"
            @click="close()"
            aria-label="Close"
            class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20"
        >
            <x-icon name="x" class="h-5 w-5" />
        </button>

        <button
            type="button"
            @click="prev()"
            aria-label="Previous photo"
            class="absolute left-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 sm:left-6"
        >
            <x-icon name="chevron-left" class="h-6 w-6" />
        </button>

        <button
            type="button"
            @click="next()"
            aria-label="Next photo"
            class="absolute right-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 sm:right-6"
        >
            <x-icon name="chevron-right" class="h-6 w-6" />
        </button>

        <div class="flex max-h-full max-w-4xl flex-col items-center">
            <img :src="active.url" class="max-h-[75vh] w-auto rounded-2xl object-contain shadow-2xl">
            <div class="mt-4 max-w-xl text-center">
                <p x-show="active.description" x-text="active.description" class="text-sm text-white"></p>
                <p x-show="active.name" x-text="active.name" class="mt-1 text-xs text-navy-300"></p>
            </div>
        </div>
    </div>
</div>
