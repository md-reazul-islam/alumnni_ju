<div
    x-data="commandPalette()"
    x-show="open"
    x-cloak
    @keydown.escape.window="close()"
    class="fixed inset-0 z-50 overflow-y-auto px-4 pb-6 pt-[12vh]"
    style="display: none;"
>
    <div
        class="fixed inset-0 bg-navy-950/50 backdrop-blur-sm"
        @click="close()"
        x-show="open"
        x-transition.opacity
    ></div>

    <div
        class="relative mx-auto max-w-xl overflow-hidden rounded-2xl bg-white shadow-popover dark:bg-navy-900"
        x-show="open"
        x-transition:enter="ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
    >
        <div class="flex items-center gap-3 border-b border-slate-100 px-4 py-3 dark:border-navy-800">
            <x-icon name="search" class="h-5 w-5 flex-shrink-0 text-slate-400" />
            <input
                type="text"
                x-ref="paletteInput"
                x-model="query"
                @input="onInput()"
                @keydown.down.prevent="moveDown()"
                @keydown.up.prevent="moveUp()"
                @keydown.enter.prevent="selectActive()"
                placeholder="Search alumni, events, jobs, news, stories…"
                autocomplete="off"
                class="min-w-0 flex-1 border-0 bg-transparent p-0 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-0 dark:text-white"
            >
            <kbd class="flex-shrink-0 rounded border border-slate-200 px-1.5 py-0.5 text-[10px] font-semibold text-slate-400 dark:border-navy-700">Esc</kbd>
        </div>

        <div class="max-h-96 overflow-y-auto p-2">
            <template x-if="loading">
                <p class="px-3 py-6 text-center text-sm text-slate-400">Searching…</p>
            </template>

            <template x-if="!loading && query.trim().length < 2">
                <p class="px-3 py-6 text-center text-sm text-slate-400">Type at least 2 characters to search…</p>
            </template>

            <template x-if="!loading && query.trim().length >= 2 && groups.length === 0">
                <p class="px-3 py-6 text-center text-sm text-slate-400">No results for "<span x-text="query"></span>"</p>
            </template>

            <template x-for="group in groups" :key="group.label">
                <div class="mb-2">
                    <p class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wide text-slate-400" x-text="group.label"></p>
                    <template x-for="item in group.items" :key="item.url">
                        <a
                            :href="item.url"
                            class="flex items-center justify-between gap-3 rounded-lg px-3 py-2 text-sm"
                            :class="isActive(item.url) ? 'bg-navy-50 dark:bg-navy-800' : 'hover:bg-slate-50 dark:hover:bg-navy-800'"
                        >
                            <span class="truncate font-medium text-slate-900 dark:text-white" x-text="item.title"></span>
                            <span class="flex-shrink-0 truncate text-xs text-slate-400" x-text="item.subtitle"></span>
                        </a>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>
