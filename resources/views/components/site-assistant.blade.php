<div x-data="siteAssistant()" class="fixed bottom-5 right-5 z-40">
    {{-- Launcher button --}}
    <button
        type="button"
        @click="toggle()"
        aria-label="Open site assistant"
        class="flex h-14 w-14 items-center justify-center rounded-full bg-navy-800 text-white shadow-popover transition hover:bg-navy-700"
    >
        <x-icon name="sparkles" x-show="!open" class="h-6 w-6" />
        <x-icon name="x" x-show="open" x-cloak class="h-6 w-6" />
    </button>

    {{-- Chat panel --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        @click.outside="open = false"
        class="absolute bottom-[calc(100%+0.75rem)] right-0 flex h-[28rem] w-80 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-popover dark:border-navy-800 dark:bg-navy-900 sm:w-96"
    >
        <div class="flex items-center gap-2.5 border-b border-slate-100 px-4 py-3 dark:border-navy-800">
            <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-navy-50 text-navy-700 dark:bg-navy-800 dark:text-navy-200">
                <x-icon name="sparkles" class="h-4 w-4" />
            </span>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-900 dark:text-white">Site Assistant</p>
                <p class="text-xs text-slate-400">Ask me anything about the site</p>
            </div>
        </div>

        <div x-ref="scrollBox" class="flex-1 space-y-3 overflow-y-auto p-4">
            <template x-for="(message, index) in messages" :key="index">
                <div :class="message.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <p
                        x-text="message.content"
                        class="max-w-[85%] whitespace-pre-line rounded-2xl px-3 py-2 text-sm"
                        :class="message.role === 'user'
                            ? 'bg-navy-800 text-white'
                            : 'bg-slate-100 text-slate-700 dark:bg-navy-800 dark:text-slate-200'"
                    ></p>
                </div>
            </template>

            <div x-show="sending" x-cloak class="flex items-center gap-1.5 px-3 text-slate-400">
                <x-icon name="loader-circle" class="h-4 w-4 animate-spin" />
                <span class="text-xs">Thinking…</span>
            </div>
        </div>

        <form @submit.prevent="send()" class="flex items-center gap-2 border-t border-slate-100 p-3 dark:border-navy-800">
            <input
                type="text"
                x-ref="chatInput"
                x-model="input"
                placeholder="Type your question…"
                autocomplete="off"
                :disabled="sending"
                class="form-input flex-1 !py-2 text-sm"
            >
            <button
                type="submit"
                :disabled="sending || !input.trim()"
                class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-navy-800 text-white transition hover:bg-navy-700 disabled:opacity-40"
                aria-label="Send message"
            >
                <x-icon name="send" class="h-4 w-4" />
            </button>
        </form>
    </div>
</div>
