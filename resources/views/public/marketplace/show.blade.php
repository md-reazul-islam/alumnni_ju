<x-layouts::app>
  <div class="overflow-hidden rounded-3xl bg-white shadow-xl dark:bg-navy-900">
    <div class="section-container max-w-4xl py-8">
        <x-breadcrumb :items="[['label' => 'Marketplace', 'url' => route('marketplace.index')], ['label' => $listing->title]]" class="mb-6" />

        @if (session('status'))
            <x-alert variant="success" class="mb-4">{{ session('status') }}</x-alert>
        @endif

        @if (session('error'))
            <x-alert variant="warning" class="mb-4">{{ session('error') }}</x-alert>
        @endif

        <div class="card card-body">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <x-badge variant="info">{{ $listing->category->name }}</x-badge>
                    <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $listing->title }}</h1>
                    <p class="mt-1 flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                        <x-icon name="map-pin" class="h-4 w-4" /> {{ $listing->address }}{{ $listing->city ? ', ' . $listing->city : '' }}
                    </p>
                </div>
                <p class="text-2xl font-bold text-navy-700 dark:text-gold-400">
                    ${{ number_format($listing->price, 2) }}{{ $listing->price_unit !== 'total' ? ' / ' . str_replace('per_', '', $listing->price_unit) : '' }}
                </p>
            </div>

            @if ($listing->images->isNotEmpty())
                @php
                    $imageUrls = $listing->images->map(fn ($image) => asset('storage/' . $image->path))->values();
                @endphp
                <div
                    x-data="{
                        images: {{ \Illuminate\Support\Js::from($imageUrls) }},
                        index: 0,
                        lightbox: false,
                        zoomed: false,
                        next() { this.index = (this.index + 1) % this.images.length; this.zoomed = false; },
                        prev() { this.index = (this.index - 1 + this.images.length) % this.images.length; this.zoomed = false; },
                        open(i) { this.index = i; this.lightbox = true; this.zoomed = false; },
                    }"
                    class="mt-6"
                    @keydown.window.escape="lightbox = false"
                >
                    <div class="relative overflow-hidden rounded-lg bg-navy-100 dark:bg-navy-800">
                        <img :src="images[index]" @click="open(index)" class="h-64 w-full cursor-zoom-in object-cover sm:h-96">

                        <template x-if="images.length > 1">
                            <div>
                                <button type="button" @click="prev()" class="absolute left-2 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-slate-700 shadow hover:bg-white dark:bg-navy-900/80 dark:text-white">
                                    <x-icon name="chevron-left" class="h-5 w-5" />
                                </button>
                                <button type="button" @click="next()" class="absolute right-2 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-slate-700 shadow hover:bg-white dark:bg-navy-900/80 dark:text-white">
                                    <x-icon name="chevron-right" class="h-5 w-5" />
                                </button>
                                <div class="absolute bottom-2 left-1/2 flex -translate-x-1/2 gap-1.5">
                                    <template x-for="(img, i) in images" :key="i">
                                        <button type="button" @click="index = i" :class="i === index ? 'bg-white' : 'bg-white/50'" class="h-1.5 w-1.5 rounded-full"></button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <template x-if="images.length > 1">
                        <div class="mt-3 grid grid-cols-4 gap-2 sm:grid-cols-6">
                            <template x-for="(img, i) in images" :key="i">
                                <button type="button" @click="index = i" class="overflow-hidden rounded-lg border-2" :class="i === index ? 'border-navy-700 dark:border-gold-400' : 'border-transparent'">
                                    <img :src="img" class="h-16 w-full object-cover">
                                </button>
                            </template>
                        </div>
                    </template>

                    <template x-if="lightbox">
                        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4" @click.self="lightbox = false">
                            <button type="button" @click="lightbox = false" class="absolute right-4 top-4 text-white hover:text-gold-400">
                                <x-icon name="x" class="h-7 w-7" />
                            </button>

                            <button type="button" @click.stop="zoomed = !zoomed" class="absolute left-4 top-4 flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-sm text-white hover:bg-white/20">
                                <x-icon name="maximize-2" class="h-4 w-4" x-show="!zoomed" />
                                <x-icon name="minimize-2" class="h-4 w-4" x-show="zoomed" x-cloak />
                                <span x-text="zoomed ? 'Zoom Out' : 'Zoom In'"></span>
                            </button>

                            <template x-if="images.length > 1">
                                <button type="button" @click.stop="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 text-white hover:text-gold-400">
                                    <x-icon name="chevron-left" class="h-9 w-9" />
                                </button>
                            </template>
                            <template x-if="images.length > 1">
                                <button type="button" @click.stop="next()" class="absolute right-4 top-1/2 -translate-y-1/2 text-white hover:text-gold-400">
                                    <x-icon name="chevron-right" class="h-9 w-9" />
                                </button>
                            </template>

                            <div class="max-h-full max-w-full overflow-auto">
                                <img :src="images[index]" @click.stop="zoomed = !zoomed" :class="zoomed ? 'scale-150 cursor-zoom-out' : 'scale-100 cursor-zoom-in'" class="max-h-[85vh] max-w-full object-contain transition-transform duration-300">
                            </div>
                        </div>
                    </template>
                </div>
            @endif

            <div class="prose prose-slate mt-8 max-w-none dark:prose-invert">
                <h2>Description</h2>
                <p class="whitespace-pre-line">{{ $listing->description }}</p>
            </div>

            @if (!empty($listing->details))
                <div class="mt-6">
                    <h2 class="font-semibold text-slate-900 dark:text-white">Details</h2>
                    <dl class="mt-3 grid grid-cols-2 gap-3 text-sm sm:grid-cols-3">
                        @foreach ($listing->details as $detail)
                            <div>
                                <dt class="text-slate-400">{{ $detail['label'] }}</dt>
                                <dd class="font-medium text-slate-700 dark:text-slate-200">{{ $detail['value'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endif

            @if ($listing->video_embed_url)
                <div class="mt-6">
                    <h2 class="font-semibold text-slate-900 dark:text-white">Video</h2>
                    <div class="mt-3 aspect-video overflow-hidden rounded-lg">
                        <iframe src="{{ $listing->video_embed_url }}" class="h-full w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                    </div>
                </div>
            @endif

            <div class="mt-6">
                <h2 class="font-semibold text-slate-900 dark:text-white">Location</h2>
                <div class="mt-3 overflow-hidden rounded-lg">
                    <iframe src="{{ $listing->map_embed_url }}" class="h-64 w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>

            <div class="mt-8 border-t border-slate-100 pt-6 dark:border-navy-800">
                @if ($isOwnListing)
                    <x-badge variant="info" class="text-sm">This is your own listing — you can't inquire about it yourself.</x-badge>
                @elseif (auth()->check())
                    @if ($hasActiveInquiry)
                        <x-badge variant="success" class="text-sm">You've already contacted our team about this listing — check your messages.</x-badge>
                    @else
                        <form method="POST" action="{{ route('marketplace.inquire', $listing) }}">
                            @csrf
                            <x-button type="submit">I'm Interested</x-button>
                        </form>
                        <p class="mt-2 text-xs text-slate-400">Interested buyers are connected through our office — we'll pass your inquiry along and follow up with you directly.</p>
                    @endif
                @else
                    <x-button :href="route('login')">Log In to Contact Us About This Listing</x-button>
                @endif
            </div>
        </div>
    </div>
  </div>
</x-layouts::app>
