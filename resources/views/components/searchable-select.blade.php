@props(['name', 'options' => [], 'selected' => null, 'placeholder' => 'Search…', 'label' => null, 'required' => false])

<div
    x-data="{
        open: false,
        query: '',
        selectedValue: {{ \Illuminate\Support\Js::from(old($name, $selected)) }},
        options: {{ \Illuminate\Support\Js::from(collect($options)->values()) }},
        get selectedLabel() {
            const found = this.options.find(o => String(o.value) === String(this.selectedValue));
            return found ? found.label : '';
        },
        get filtered() {
            if (!this.query) return this.options;
            const q = this.query.toLowerCase();
            return this.options.filter(o => o.label.toLowerCase().includes(q));
        },
        select(option) {
            this.selectedValue = option.value;
            this.query = '';
            this.open = false;
        },
    }"
    @click.outside="open = false"
    @keydown.escape="open = false"
    class="relative"
>
    @if ($label)
        <label class="form-label">{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</label>
    @endif

    <input type="hidden" name="{{ $name }}" :value="selectedValue">

    <button
        type="button"
        @click="open = !open; open && $nextTick(() => $refs.searchInput.focus())"
        {{ $attributes->class(['form-select flex w-full items-center justify-between text-left', 'border-red-400 focus:border-red-500 focus:ring-red-500' => $errors->has($name)]) }}
    >
        <span x-text="selectedLabel || @js($placeholder)" :class="selectedLabel ? '' : 'text-slate-400'"></span>
        <x-icon name="chevron-down" class="h-4 w-4 flex-shrink-0 text-slate-400" />
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition
        class="absolute z-20 mt-1 w-full overflow-hidden rounded-lg border border-slate-200 bg-white shadow-popover dark:border-navy-700 dark:bg-navy-900"
    >
        <div class="border-b border-slate-100 p-2 dark:border-navy-800">
            <input
                type="text"
                x-ref="searchInput"
                x-model="query"
                placeholder="{{ $placeholder }}"
                class="form-input"
                autocomplete="off"
            >
        </div>
        <div class="max-h-56 overflow-y-auto p-1">
            <template x-if="filtered.length === 0">
                <p class="px-3 py-2 text-sm text-slate-400">No matches found</p>
            </template>
            <template x-for="option in filtered" :key="option.value">
                <button
                    type="button"
                    @click="select(option)"
                    class="flex w-full items-center rounded-lg px-3 py-2 text-left text-sm hover:bg-slate-50 dark:hover:bg-navy-800"
                    :class="String(selectedValue) === String(option.value) ? 'bg-navy-50 font-medium dark:bg-navy-800' : 'text-slate-700 dark:text-slate-200'"
                    x-text="option.label"
                ></button>
            </template>
        </div>
    </div>

    @error($name)
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>
