@php
    $cateringItemsForJs = $cateringFoodItems->map(fn ($item) => [
        'id' => $item->id,
        'name' => $item->name,
        'price' => number_format($item->base_price, 2),
        'unit_price' => (float) $item->base_price,
        'unit_label' => $item->unit_label,
        'image' => $item->image_url,
        'url' => route('catering.items.show', $item),
        'category_ids' => $item->categories->pluck('id'),
    ])->values();
@endphp

<section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
  <div class="section-container py-5 sm:py-7">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white sm:text-3xl">Catering</h2>
            <p class="mt-1.5 text-navy-200">Order catering for your next event, or browse home made foods from fellow alumni.</p>
        </div>
        <a href="{{ route('catering.search') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
            Browse catering <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
        <div>
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-navy-200">Event Catering &middot; by program category</h3>
                <a href="{{ route('catering.search') }}" class="text-xs font-medium text-gold-400 hover:text-gold-300">View all</a>
            </div>

            @if ($cateringCategories->isEmpty())
                <x-empty-state icon="utensils" title="No catering categories yet" class="mt-4" />
            @else
                <div
                    class="mt-3"
                    x-data="{
                        activeCategory: null,
                        items: {{ \Illuminate\Support\Js::from($cateringItemsForJs) }},
                        get filteredItems() {
                            return this.items.filter((item) => this.activeCategory === null || item.category_ids.includes(this.activeCategory));
                        },
                    }"
                >
                    <div class="grid grid-cols-3 gap-2 sm:grid-cols-4">
                        @foreach ($cateringCategories as $cat)
                            <button
                                type="button"
                                @click="activeCategory = activeCategory === {{ $cat->id }} ? null : {{ $cat->id }}"
                                class="card flex flex-col items-center gap-1.5 p-2.5 text-center transition hover:shadow-popover"
                                :class="activeCategory === {{ $cat->id }} ? 'ring-2 ring-gold-400' : ''"
                            >
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-navy-50 text-navy-600 dark:bg-navy-800 dark:text-navy-300">
                                    <x-icon :name="$cat->icon ?: 'utensils'" class="h-4 w-4" />
                                </span>
                                <span class="w-full truncate text-[11px] font-semibold text-slate-900 dark:text-white sm:text-xs">{{ $cat->name }}</span>
                            </button>
                        @endforeach
                    </div>

                    <template x-if="activeCategory !== null">
                        <div class="mt-4">
                            <template x-if="filteredItems.length === 0">
                                <p class="text-sm text-navy-300">No items in this category yet.</p>
                            </template>

                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                                <template x-for="item in filteredItems" :key="item.id">
                                    <div class="card overflow-hidden" x-data="{ added: false }">
                                        <a :href="item.url" class="block">
                                            <div class="flex h-20 items-center justify-center bg-navy-100 text-navy-400 dark:bg-navy-800 sm:h-24">
                                                <img x-show="item.image" :src="item.image" class="h-full w-full object-cover">
                                                <template x-if="!item.image"><x-icon name="utensils" class="h-6 w-6" /></template>
                                            </div>
                                            <div class="p-2.5">
                                                <p class="truncate text-xs font-semibold text-slate-900 dark:text-white sm:text-sm" x-text="item.name"></p>
                                                <p class="mt-0.5 text-[10px] font-semibold text-navy-700 dark:text-gold-400 sm:text-xs">$<span x-text="item.price"></span></p>
                                            </div>
                                        </a>
                                        <button
                                            type="button"
                                            @click="Alpine.store('cateringCart').add({ food_item_id: item.id, name: item.name, unit_price: item.unit_price, unit_label: item.unit_label, image: item.image }); added = true; setTimeout(() => added = false, 1500)"
                                            class="w-full border-t border-slate-100 py-1.5 text-[10px] font-semibold uppercase tracking-wide text-navy-700 transition hover:bg-navy-50 dark:border-navy-800 dark:text-gold-400 dark:hover:bg-navy-800 sm:text-xs"
                                        >
                                            <span x-show="!added">Add to Cart</span>
                                            <span x-show="added" x-cloak>Added &check;</span>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <a :href="'{{ route('catering.orders.create') }}?category=' + activeCategory" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-gold-400 hover:text-gold-300">
                                Go to cart &amp; checkout <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                            </a>
                        </div>
                    </template>
                </div>
            @endif
        </div>

        <div>
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-navy-200">Home Made Foods &middot; by alumni</h3>
                <a href="{{ route('catering.homemade.index') }}" class="text-xs font-medium text-gold-400 hover:text-gold-300">View all</a>
            </div>

            @if ($cateringHomemadeListings->isEmpty())
                <x-empty-state icon="cooking-pot" title="No home made foods yet" class="mt-4" />
            @else
                <div class="mt-3 grid grid-cols-3 gap-3">
                    @foreach ($cateringHomemadeListings as $listing)
                        <a href="{{ route('catering.homemade.show', $listing) }}" class="card overflow-hidden transition hover:shadow-popover">
                            <div class="flex h-16 items-center justify-center bg-navy-100 text-navy-400 dark:bg-navy-800 sm:h-20">
                                @if ($listing->cover_image_url)
                                    <img src="{{ $listing->cover_image_url }}" class="h-full w-full object-cover">
                                @else
                                    <x-icon name="image" class="h-5 w-5" />
                                @endif
                            </div>
                            <div class="p-2">
                                <p class="w-full truncate text-[11px] font-semibold text-slate-900 dark:text-white sm:text-xs">{{ $listing->title }}</p>
                                <p class="mt-0.5 w-full truncate text-[10px] font-semibold text-navy-700 dark:text-gold-400">${{ number_format($listing->price, 2) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
  </div>
</section>
