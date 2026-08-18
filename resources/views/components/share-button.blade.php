@props(['url', 'title' => ''])

<div class="relative inline-block" x-data="{ open: false, copied: false }" @click.outside="open = false" @keydown.escape.window="open = false">
    <button
        type="button"
        @click="open = !open"
        {{ $attributes->class(['flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-slate-600 shadow-sm hover:bg-white dark:bg-navy-900/90 dark:text-slate-300']) }}
        aria-label="Share this event"
    >
        <x-icon name="share-2" class="h-4 w-4" />
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition
        class="absolute right-0 z-30 mt-2 w-72 rounded-xl border border-slate-200 bg-white p-4 text-left shadow-popover dark:border-navy-700 dark:bg-navy-900"
    >
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Share this event</p>

        <div class="mt-2 flex items-center gap-2">
            <input
                type="text"
                readonly
                value="{{ $url }}"
                x-ref="shareLink"
                @click="$refs.shareLink.select()"
                class="form-input flex-1 text-xs"
            >
            <button
                type="button"
                @click="navigator.clipboard.writeText(@js($url)); copied = true; setTimeout(() => copied = false, 2000)"
                class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 dark:bg-navy-800 dark:text-slate-300"
                :title="copied ? 'Copied!' : 'Copy link'"
            >
                <x-icon name="copy" x-show="!copied" class="h-4 w-4" />
                <x-icon name="check" x-show="copied" x-cloak class="h-4 w-4 text-emerald-500" />
            </button>
        </div>
        <p class="mt-1 text-xs text-emerald-600" x-show="copied" x-cloak>Link copied to clipboard!</p>

        <div class="mt-3 flex items-center justify-between">
            <a
                href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url) }}"
                target="_blank"
                rel="noopener"
                class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-[#1877F2] hover:bg-slate-200 dark:bg-navy-800"
                title="Share on Facebook"
            >
                <x-icon name="facebook" class="h-4 w-4" />
            </a>
            <a
                href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($url) }}"
                target="_blank"
                rel="noopener"
                class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-[#0A66C2] hover:bg-slate-200 dark:bg-navy-800"
                title="Share on LinkedIn"
            >
                <x-icon name="linkedin" class="h-4 w-4" />
            </a>
            <a
                href="https://wa.me/?text={{ urlencode(($title ? $title . ' - ' : '') . $url) }}"
                target="_blank"
                rel="noopener"
                class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-[#25D366] hover:bg-slate-200 dark:bg-navy-800"
                title="Share on WhatsApp"
            >
                <x-icon name="whatsapp" class="h-4 w-4" />
            </a>
            <button
                type="button"
                @click="navigator.clipboard.writeText(@js($url)); copied = true; setTimeout(() => copied = false, 2500); window.open('https://www.instagram.com/', '_blank')"
                class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-[#E4405F] hover:bg-slate-200 dark:bg-navy-800"
                title="Instagram doesn't support direct links — copies the link to paste into your bio, story, or DM"
            >
                <x-icon name="instagram" class="h-4 w-4" />
            </button>
        </div>
    </div>
</div>
