<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="bg-slate-50 font-sans antialiased dark:bg-navy-950">
    <div class="flex min-h-screen">
        <x-sidebar />

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-20 flex h-16 flex-shrink-0 items-center justify-between border-b border-slate-200 bg-white px-4 dark:border-navy-800 dark:bg-navy-900 sm:px-6">
                <button
                    type="button"
                    x-data
                    @click="$dispatch('open-admin-nav')"
                    class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-navy-800 lg:hidden"
                >
                    <x-icon name="menu" class="h-5 w-5" />
                </button>

                <div class="hidden flex-1 items-center gap-2 text-sm text-slate-400 sm:flex" x-data @click="document.dispatchEvent(new KeyboardEvent('keydown', {key: 'k', ctrlKey: true}))">
                    <button type="button" class="flex w-full max-w-sm items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-left hover:border-slate-300 dark:border-navy-700 dark:hover:border-navy-600">
                        <x-icon name="search" class="h-4 w-4" />
                        <span>Quick search…</span>
                        <kbd class="ml-auto rounded border border-slate-200 px-1.5 py-0.5 text-[10px] font-semibold dark:border-navy-700">Ctrl K</kbd>
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <x-notification-dropdown />
                    <x-profile-menu />
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @isset($header)
                    <div class="mb-6">{{ $header }}</div>
                @endisset

                {{ $slot }}
            </main>
        </div>
    </div>

    <x-command-palette />

    @stack('scripts')
</body>
</html>
