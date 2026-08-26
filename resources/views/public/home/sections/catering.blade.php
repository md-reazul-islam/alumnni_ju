@php
    $cateringItemsForJs = $cateringFoodItems->map(fn ($item) => [
        'id' => $item->id,
        'name' => $item->name,
        'price' => number_format($item->base_price, 2),
        'unit_label' => $item->unit_label,
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
                                    <x-icon name="utensils" class="h-4.5 w-4.5" />
                                </span>
                                <span class="w-full truncate text-[11px] font-semibold text-slate-900 dark:text-white sm:text-xs">{{ $cat->name }}</span>
                            </button>
                        @endforeach
                    </div>

                    <template x-if="activeCategory !== null">
                        <div class="mt-4 space-y-2">
                            <template x-if="filteredItems.length === 0">
                                <p class="text-sm text-navy-300">No items in this category yet.</p>
                            </template>
                            <template x-for="item in filteredItems" :key="item.id">
                                <div class="flex items-center justify-between rounded-lg bg-navy-800/60 px-3 py-2">
                                    <span class="text-sm text-white" x-text="item.name"></span>
                                    <span class="text-xs font-semibold text-gold-400">$<span x-text="item.price"></span></span>
                                </div>
                            </template>
                            <a :href="'{{ route('catering.orders.create') }}?category=' + activeCategory" class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-gold-400 hover:text-gold-300">
                                Order from this category <x-icon name="arrow-right" class="h-3.5 w-3.5" />
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
