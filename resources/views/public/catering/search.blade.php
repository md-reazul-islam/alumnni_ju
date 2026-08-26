<x-layouts::app>
    @php
        $categoriesForJs = $categories->map(fn ($cat) => [
            'id' => $cat->id,
            'name' => $cat->name,
            'icon' => $cat->icon,
            'description' => $cat->description,
            'item_count' => $cat->foodItems->count(),
        ])->values();
        $itemsForJs = $foodItems->map(fn ($item) => [
            'id' => $item->id,
            'name' => $item->name,
            'description' => $item->description,
            'price' => number_format($item->base_price, 2),
            'unit_label' => $item->unit_label,
            'image' => $item->image_url,
            'category_ids' => $item->categories->pluck('id'),
        ])->values();
    @endphp

    <div>
      <div class="section-container py-8">
        <x-breadcrumb :items="[['label' => 'Catering']]" onDark class="mb-4" />

        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">Event Catering</h1>
                <p class="mt-1.5 text-navy-200">Pick a program category, build your order from our catalog, and get a priced invoice back from our team.</p>
            </div>
            @auth
                <x-button :href="route('catering.orders.create')" size="sm">Start an Order</x-button>
            @endauth
        </div>

        @if ($categories->isEmpty())
            <x-empty-state icon="utensils" title="No catering categories yet" description="Program categories and food items will appear here once the admin team adds them." class="mt-8" />
        @else
            <div
                class="mt-8"
                x-data="{
                    query: '',
                    activeCategory: null,
                    categories: {{ \Illuminate\Support\Js::from($categoriesForJs) }},
                    items: {{ \Illuminate\Support\Js::from($itemsForJs) }},
                    get filteredItems() {
                        return this.items.filter((item) => {
                            const matchesCategory = this.activeCategory === null || item.category_ids.includes(this.activeCategory);
                            const matchesQuery = !this.query || item.name.toLowerCase().includes(this.query.toLowerCase());
                            return matchesCategory && matchesQuery;
                        });
                    },
                    activeCategoryName() {
                        const cat = this.categories.find((c) => c.id === this.activeCategory);
                        return cat ? cat.name : '';
                    },
                }"
            >
                <div class="relative max-w-sm">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-navy-400" />
                    <input type="text" x-model="query" placeholder="Search food items" class="form-input pl-9">
                </div>

                <template x-if="!activeCategory && !query">
                    <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                        <template x-for="cat in categories" :key="cat.id">
                            <button
                                type="button"
                                @click="activeCategory = cat.id"
                                class="card flex flex-col items-center gap-2 p-4 text-center transition hover:shadow-popover"
                            >
                                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-navy-50 text-navy-600 dark:bg-navy-800 dark:text-navy-300">
                                    <x-icon name="utensils" class="h-6 w-6" />
                                </span>
                                <span class="text-sm font-semibold text-slate-900 dark:text-white" x-text="cat.name"></span>
                                <span class="text-xs text-slate-400" x-text="cat.item_count + ' item' + (cat.item_count === 1 ? '' : 's')"></span>
                            </button>
                        </template>
                    </div>
                </template>

                <template x-if="activeCategory || query">
                    <div class="mt-6">
                        <div class="flex items-center gap-2" x-show="activeCategory && !query">
                            <button type="button" @click="activeCategory = null" class="flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white">
                                <x-icon name="chevron-left" class="h-4 w-4" /> All categories
                            </button>
                            <span class="text-sm font-semibold text-slate-900 dark:text-white" x-text="'&middot; ' + activeCategoryName()"></span>
                        </div>

                        <template x-if="filteredItems.length === 0">
                            <p class="mt-4 text-sm text-slate-400">No items match your search.</p>
                        </template>

                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <template x-for="item in filteredItems" :key="item.id">
                                <div class="card overflow-hidden">
                                    <div class="flex h-32 items-center justify-center bg-navy-100 text-navy-400 dark:bg-navy-800">
                                        <img x-show="item.image" :src="item.image" class="h-full w-full object-cover">
                                        <template x-if="!item.image"><x-icon name="image" class="h-7 w-7" /></template>
                                    </div>
                                    <div class="card-body">
                                        <p class="font-semibold text-slate-900 dark:text-white" x-text="item.name"></p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400" x-text="item.description" x-show="item.description"></p>
                                        <p class="mt-2 font-semibold text-navy-700 dark:text-gold-400">
                                            $<span x-text="item.price"></span> <span class="text-xs font-normal text-slate-400" x-text="item.unit_label"></span>
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        @endif
      </div>
    </div>
</x-layouts::app>
