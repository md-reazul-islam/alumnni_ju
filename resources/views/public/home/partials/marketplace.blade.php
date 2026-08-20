@php
    $marketplaceItems = $marketplaceListings->map(fn ($listing) => [
        'id' => $listing->id,
        'title' => $listing->title,
        'url' => route('marketplace.show', $listing),
        'category_id' => $listing->marketplace_category_id,
        'category' => $listing->category->name,
        'price' => number_format($listing->price, 2),
        'price_unit' => $listing->price_unit,
        'location' => $listing->city ?: $listing->address,
        'image' => $listing->cover_image_url,
    ]);
@endphp

<div
    x-data="{
        query: '',
        category: null,
        items: {{ \Illuminate\Support\Js::from($marketplaceItems) }},
        get filtered() {
            return this.items.filter((item) => {
                const matchesCategory = this.category === null || item.category_id === this.category;
                const matchesQuery = !this.query || item.title.toLowerCase().includes(this.query.toLowerCase()) || item.location.toLowerCase().includes(this.query.toLowerCase());
                return matchesCategory && matchesQuery;
            });
        },
    }"
>
    <div class="flex flex-wrap items-center gap-2">
        <button type="button" @click="category = null" :class="category === null ? 'bg-gold-500 text-navy-950' : 'bg-navy-800 text-navy-200 hover:bg-navy-700'" class="rounded-full px-3.5 py-1.5 text-sm font-medium transition">All</button>
        @foreach ($marketplaceCategories as $cat)
            <button type="button" @click="category = {{ $cat->id }}" :class="category === {{ $cat->id }} ? 'bg-gold-500 text-navy-950' : 'bg-navy-800 text-navy-200 hover:bg-navy-700'" class="rounded-full px-3.5 py-1.5 text-sm font-medium transition">{{ $cat->name }}</button>
        @endforeach
    </div>

    <div class="relative mt-4 max-w-sm">
        <x-icon name="search" class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-navy-400" />
        <input type="text" x-model="query" placeholder="Search by title or location" class="form-input pl-9">
    </div>

    <template x-if="filtered.length === 0">
        <p class="mt-8 text-sm text-navy-300">No listings match your search.</p>
    </template>

    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <template x-for="item in filtered" :key="item.id">
            <a :href="item.url" class="card overflow-hidden transition hover:shadow-popover">
                <div class="flex h-36 items-center justify-center bg-navy-100 text-navy-400 dark:bg-navy-800">
                    <img x-show="item.image" :src="item.image" class="h-full w-full object-cover">
                    <template x-if="!item.image"><x-icon name="image" class="h-8 w-8" /></template>
                </div>
                <div class="card-body">
                    <x-badge variant="info" x-text="item.category"></x-badge>
                    <p class="mt-2 font-semibold text-slate-900 dark:text-white" x-text="item.title"></p>
                    <p class="mt-1 flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400">
                        <x-icon name="map-pin" class="h-4 w-4" /> <span x-text="item.location"></span>
                    </p>
                    <p class="mt-2 font-semibold text-navy-700 dark:text-gold-400">
                        $<span x-text="item.price"></span><template x-if="item.price_unit !== 'total'"><span x-text="' / ' + item.price_unit.replace('per_', '')"></span></template>
                    </p>
                </div>
            </a>
        </template>
    </div>
</div>
