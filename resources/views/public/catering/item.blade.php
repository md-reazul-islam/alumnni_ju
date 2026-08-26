<x-layouts::app>
  <div class="overflow-hidden rounded-3xl bg-white shadow-xl dark:bg-navy-900">
    <div class="section-container max-w-4xl py-8">
        <x-breadcrumb :items="[
            ['label' => 'Catering', 'url' => route('catering.search')],
            ...($foodItem->categories->first() ? [['label' => $foodItem->categories->first()->name, 'url' => route('catering.search')]] : []),
            ['label' => $foodItem->name],
        ]" class="mb-6" />

        <div
            class="card card-body"
            x-data="{
                quantity: 1,
                added: false,
                addToCart() {
                    Alpine.store('cateringCart').add({
                        food_item_id: {{ $foodItem->id }},
                        name: {{ \Illuminate\Support\Js::from($foodItem->name) }},
                        unit_price: {{ (float) $foodItem->base_price }},
                        unit_label: {{ \Illuminate\Support\Js::from($foodItem->unit_label) }},
                        image: {{ \Illuminate\Support\Js::from($foodItem->image_url) }},
                    }, Math.max(1, parseInt(this.quantity) || 1));
                    this.added = true;
                    setTimeout(() => this.added = false, 2000);
                },
            }"
        >
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                <div class="flex h-64 items-center justify-center overflow-hidden rounded-xl bg-navy-100 text-navy-400 dark:bg-navy-800 sm:h-80">
                    @if ($foodItem->image_url)
                        <img src="{{ $foodItem->image_url }}" class="h-full w-full object-cover">
                    @else
                        <x-icon name="utensils" class="h-12 w-12" />
                    @endif
                </div>

                <div>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($foodItem->categories as $cat)
                            <x-badge variant="info">{{ $cat->name }}</x-badge>
                        @endforeach
                    </div>

                    <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">{{ $foodItem->name }}</h1>
                    <p class="mt-2 text-2xl font-bold text-navy-700 dark:text-gold-400">
                        ${{ number_format($foodItem->base_price, 2) }} <span class="text-sm font-normal text-slate-400">{{ $foodItem->unit_label }}</span>
                    </p>

                    @if ($foodItem->description)
                        <p class="mt-4 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $foodItem->description }}</p>
                    @endif

                    <div class="mt-6 flex items-end gap-3">
                        <div>
                            <label class="form-label">Quantity</label>
                            <input type="number" x-model="quantity" min="1" class="form-input w-24">
                        </div>
                        <x-button type="button" @click="addToCart()" class="flex-1">
                            <span x-show="!added">Add to Cart</span>
                            <span x-show="added" x-cloak>Added &check;</span>
                        </x-button>
                    </div>

                    <div class="mt-3" x-show="added" x-cloak x-transition>
                        <a href="{{ route('catering.orders.create') }}" class="text-sm font-semibold text-navy-700 hover:underline dark:text-gold-400">
                            View cart &amp; continue to order &rarr;
                        </a>
                    </div>

                    <p class="mt-4 text-xs text-slate-400">Adding to cart doesn't charge you — you'll review everything and submit for pricing before anything is confirmed.</p>
                </div>
            </div>
        </div>

        @if ($relatedItems->isNotEmpty())
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">You might also like</h2>
                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach ($relatedItems as $related)
                        <a href="{{ route('catering.items.show', $related) }}" class="card overflow-hidden transition hover:shadow-popover">
                            <div class="flex h-20 items-center justify-center bg-navy-100 text-navy-400 dark:bg-navy-800 sm:h-24">
                                @if ($related->image_url)
                                    <img src="{{ $related->image_url }}" class="h-full w-full object-cover">
                                @else
                                    <x-icon name="utensils" class="h-6 w-6" />
                                @endif
                            </div>
                            <div class="p-2.5">
                                <p class="truncate text-xs font-semibold text-slate-900 dark:text-white sm:text-sm">{{ $related->name }}</p>
                                <p class="mt-1 text-[10px] font-semibold text-navy-700 dark:text-gold-400 sm:text-xs">${{ number_format($related->base_price, 2) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
  </div>
</x-layouts::app>
