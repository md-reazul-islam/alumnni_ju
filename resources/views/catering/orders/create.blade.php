@php
    $categoriesForJs = $categories->map(fn ($category) => [
        'id' => $category->id,
        'name' => $category->name,
        'icon' => $category->icon,
        'items' => $category->foodItems->map(fn ($item) => [
            'id' => $item->id,
            'name' => $item->name,
            'base_price' => (float) $item->base_price,
            'unit_label' => $item->unit_label,
        ])->values(),
    ])->values();
@endphp

<x-layouts::alumni :title="'Order Catering'">
    <x-breadcrumb :items="[['label' => 'Catering', 'url' => Route::has('catering.search') ? route('catering.search') : null], ['label' => 'Build an Order']]" class="mb-4" />

    <div
        x-data="{
            categories: {{ \Illuminate\Support\Js::from($categoriesForJs) }},
            selectedCategoryId: {{ $selectedCategoryId }},
            cart: [],
            customName: '',
            customQty: 1,
            get selectedCategory() {
                return this.categories.find(c => c.id === this.selectedCategoryId) ?? null;
            },
            addItem(item) {
                const existing = this.cart.find(c => c.food_item_id === item.id);
                if (existing) { existing.quantity++; }
                else { this.cart.push({ food_item_id: item.id, name: item.name, unit_price: item.base_price, unit_label: item.unit_label, quantity: 1 }); }
            },
            addCustom() {
                if (!this.customName.trim()) return;
                this.cart.push({ custom_name: this.customName.trim(), name: this.customName.trim(), quantity: Math.max(1, parseInt(this.customQty) || 1) });
                this.customName = '';
                this.customQty = 1;
            },
            removeAt(index) { this.cart.splice(index, 1); },
            get cartPayload() {
                return this.cart.map(c => ({ food_item_id: c.food_item_id ?? null, custom_name: c.custom_name ?? null, quantity: c.quantity }));
            },
        }"
    >
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Build Your Catering Order</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pick a category, add items (or type one we don't carry), and submit — we'll price it and send you an invoice to approve.</p>

        @if ($errors->any())
            <x-alert variant="danger" class="mt-4">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <form method="POST" action="{{ route('catering.orders.store') }}" @submit="document.getElementById('items_json').value = JSON.stringify(cartPayload)" class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            @csrf
            <input type="hidden" id="items_json" name="items_json" value="[]">
            <input type="hidden" name="catering_program_category_id" :value="selectedCategoryId">

            <div class="lg:col-span-2">
                <div class="flex flex-wrap gap-2">
                    <template x-for="category in categories" :key="category.id">
                        <button
                            type="button"
                            @click="selectedCategoryId = category.id"
                            :class="selectedCategoryId === category.id ? 'bg-navy-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-navy-800 dark:text-slate-300'"
                            class="flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-sm font-medium transition"
                        >
                            <x-icon name="utensils" class="h-3.5 w-3.5" />
                            <span x-text="category.name"></span>
                        </button>
                    </template>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <template x-for="item in (selectedCategory?.items ?? [])" :key="item.id">
                        <div class="card card-body flex items-center justify-between gap-3">
                            <div>
                                <p class="font-medium text-slate-900 dark:text-white" x-text="item.name"></p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">$<span x-text="item.base_price.toFixed(2)"></span> <span x-text="item.unit_label"></span></p>
                            </div>
                            <button type="button" @click="addItem(item)" class="btn-secondary btn-sm flex-shrink-0">Add</button>
                        </div>
                    </template>
                    <template x-if="(selectedCategory?.items ?? []).length === 0">
                        <p class="text-sm text-slate-500 dark:text-slate-400 sm:col-span-2">No catalog items in this category yet — add a custom item below.</p>
                    </template>
                </div>

                <div class="card card-body mt-4">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Don't see what you want?</h3>
                    <div class="mt-2 flex flex-wrap items-end gap-3">
                        <div class="flex-1">
                            <label class="form-label">Item name</label>
                            <input type="text" x-model="customName" placeholder="e.g. Special dessert platter" class="form-input">
                        </div>
                        <div class="w-24">
                            <label class="form-label">Qty</label>
                            <input type="number" x-model="customQty" min="1" class="form-input">
                        </div>
                        <button type="button" @click="addCustom()" class="btn-secondary btn-sm">Add Custom Item</button>
                    </div>
                    <p class="mt-2 text-xs text-slate-400">Custom items don't have a price yet — our team will price them when reviewing your order.</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="card card-body">
                    <h3 class="font-semibold text-slate-900 dark:text-white">Your Cart</h3>
                    <template x-if="cart.length === 0">
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">No items added yet.</p>
                    </template>
                    <div class="mt-3 space-y-2">
                        <template x-for="(line, index) in cart" :key="index">
                            <div class="flex items-center justify-between gap-2 text-sm">
                                <span x-text="line.name + ' x' + line.quantity"></span>
                                <button type="button" @click="removeAt(index)" class="text-red-500 hover:text-red-700">
                                    <x-icon name="x" class="h-4 w-4" />
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="card card-body space-y-4">
                    <x-input label="Event Date" name="event_date" type="date" :value="old('event_date')" required />
                    <x-input label="Guest Count" name="guest_count" type="number" min="1" :value="old('guest_count')" />
                    <x-input label="Contact Phone" name="contact_phone" :value="old('contact_phone')" />
                    <x-textarea label="Delivery Address" name="delivery_address" rows="2">{{ old('delivery_address') }}</x-textarea>
                    <x-textarea label="Notes / Special Requests" name="notes" rows="2">{{ old('notes') }}</x-textarea>
                    <x-button type="submit" class="w-full disabled:cursor-not-allowed disabled:opacity-50" x-bind:disabled="cart.length === 0">Submit Order for Pricing</x-button>
                </div>
            </div>
        </form>
    </div>
</x-layouts::alumni>
