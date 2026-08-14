@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard'],
        ['label' => 'Directory', 'route' => 'alumni.directory', 'icon' => 'users'],
        ['label' => 'Events', 'route' => 'events.index', 'icon' => 'calendar'],
        ['label' => 'Careers', 'route' => 'jobs.index', 'icon' => 'briefcase'],
        ['label' => 'Community', 'route' => 'community.index', 'icon' => 'message-square'],
        ['label' => 'Mentorship', 'route' => 'mentorship.index', 'icon' => 'handshake'],
    ];
@endphp

<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur dark:border-navy-800 dark:bg-navy-950/90">
    <div class="section-container flex h-16 items-center justify-between gap-4">
        <div class="flex items-center gap-8">
            <a href="{{ route('home') }}" class="flex flex-shrink-0 items-center gap-2 text-base font-bold text-navy-900 dark:text-white">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-navy-800 text-gold-400">
                    <x-icon name="graduation-cap" class="h-4 w-4" />
                </span>
                <span class="hidden sm:inline">{{ config('app.name') }}</span>
            </a>

            <nav class="hidden items-center gap-1 lg:flex">
                @foreach ($navItems as $item)
                    @if (Route::has($item['route']))
                        <a
                            href="{{ route($item['route']) }}"
                            class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs(explode('.', $item['route'])[0] . '.*') ? 'bg-navy-50 text-navy-800 dark:bg-navy-800 dark:text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-navy-800 dark:text-slate-300 dark:hover:bg-navy-800 dark:hover:text-white' }}"
                        >
                            {{ $item['label'] }}
                        </a>
                    @endif
                @endforeach
            </nav>
        </div>

        <div class="flex items-center gap-2">
            @if (Route::has('messages.index'))
                <a href="{{ route('messages.index') }}" class="hidden h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-navy-800 sm:flex">
                    <x-icon name="message-circle" class="h-5 w-5" />
                </a>
            @endif

            <x-notification-dropdown />
            <x-profile-menu />

            <button
                type="button"
                x-data
                @click="$dispatch('open-mobile-nav')"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-navy-800 lg:hidden"
            >
                <x-icon name="menu" class="h-5 w-5" />
            </button>
        </div>
    </div>

    <div
        x-data="{ open: false }"
        x-on:open-mobile-nav.window="open = true"
        x-show="open"
        x-cloak
        class="lg:hidden"
    >
        <div class="fixed inset-0 z-40 bg-black/30" @click="open = false"></div>
        <nav
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 z-50 w-72 space-y-1 overflow-y-auto bg-white p-4 shadow-popover dark:bg-navy-900"
        >
            @foreach ($navItems as $item)
                @if (Route::has($item['route']))
                    <a href="{{ route($item['route']) }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-navy-800">
                        <x-icon :name="$item['icon']" class="h-4 w-4 text-slate-400" />
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>
    </div>
</header>
