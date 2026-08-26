<x-layouts::app>
  <div class="overflow-hidden rounded-3xl bg-white shadow-xl dark:bg-navy-900">
    <div class="section-container max-w-4xl py-8">
        <x-breadcrumb :items="[['label' => 'Catering', 'url' => route('catering.search')], ['label' => 'Home Made Foods', 'url' => route('catering.homemade.index')], ['label' => $listing->title]]" class="mb-6" />

        <div class="card card-body">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <x-badge variant="info">{{ $listing->category->name }}</x-badge>
                    <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $listing->title }}</h1>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">By {{ $listing->seller->full_name }}</p>
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
                        next() { this.index = (this.index + 1) % this.images.length; },
                        prev() { this.index = (this.index - 1 + this.images.length) % this.images.length; },
                        open(i) { this.index = i; this.lightbox = true; },
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
                            <div class="max-h-full max-w-full overflow-auto">
                                <img :src="images[index]" class="max-h-[85vh] max-w-full object-contain">
                            </div>
                        </div>
                    </template>
                </div>
            @endif

            <div class="prose prose-slate mt-8 max-w-none dark:prose-invert">
                <h2>Description</h2>
                <p class="whitespace-pre-line">{{ $listing->description }}</p>
            </div>

            @if ($listing->tags)
                <div class="mt-6 flex flex-wrap gap-1.5">
                    @foreach (explode(',', $listing->tags) as $tag)
                        <x-badge variant="neutral">{{ trim($tag) }}</x-badge>
                    @endforeach
                </div>
            @endif

            <div class="mt-8 border-t border-slate-100 pt-6 dark:border-navy-800">
                @auth
                    @if ($hasActiveInquiry)
                        <x-badge variant="success" class="text-sm">You've already ordered this — check your messages.</x-badge>
                    @else
                        <form method="POST" action="{{ route('catering.homemade.inquire', $listing) }}" class="flex flex-wrap items-end gap-3">
                            @csrf
                            <div>
                                <label class="form-label">Quantity</label>
                                <input type="number" name="quantity" min="1" max="1000" value="1" class="form-input w-24">
                            </div>
                            <x-button type="submit">I'm Interested</x-button>
                        </form>
                        <p class="mt-2 text-xs text-slate-400">Interested buyers are connected through our office — we'll pass your order along and follow up with you directly.</p>
                    @endif
                @else
                    <x-button :href="route('login')">Log In to Order This</x-button>
                @endauth
            </div>
        </div>
    </div>
  </div>
</x-layouts::app>
