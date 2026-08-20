@props(['name', 'checked' => false, 'label' => null, 'hint' => null])

<div class="flex items-center justify-between gap-4 py-2">
    @if ($label)
        <div class="min-w-0">
            <p class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $label }}</p>
            @if ($hint)
                <p class="text-xs text-slate-400">{{ $hint }}</p>
            @endif
        </div>
    @endif

    <label class="relative inline-flex flex-shrink-0 cursor-pointer items-center">
        <input type="hidden" name="{{ $name }}" value="0">
        <input type="checkbox" name="{{ $name }}" value="1" @checked($checked) class="peer sr-only">
        <div class="h-6 w-11 rounded-full bg-slate-300 transition-colors peer-checked:bg-navy-700 dark:bg-navy-700 dark:peer-checked:bg-gold-500"></div>
        <div class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-5"></div>
    </label>
</div>
