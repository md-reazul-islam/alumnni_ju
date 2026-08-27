@php
    $primaryNavItems = [];
    if (auth()->check()) {
        $primaryNavItems[] = ['label' => 'Dashboard', 'route' => 'dashboard'];
    }
    $primaryNavItems = array_merge($primaryNavItems, [
        ['label' => 'About', 'route' => 'about'],
        ['label' => 'Alumni', 'route' => 'alumni.directory'],
        ['label' => 'Events', 'route' => 'events.index', 'section' => 'show_events'],
        ['label' => 'Careers', 'route' => 'jobs.index', 'section' => 'show_jobs'],
    ]);

    $moreNavGroups = [
        'Services' => [
            ['label' => 'Marketplace', 'route' => 'marketplace.index', 'section' => 'show_marketplace'],
            ['label' => 'Carpooling', 'route' => 'carpooling.search', 'section' => 'show_carpooling'],
            ['label' => 'Catering', 'route' => 'catering.search', 'section' => 'show_catering'],
            ['label' => 'Matrimony', 'route' => 'matrimony.search', 'section' => 'show_matrimony'],
        ],
        'Community' => [
            ['label' => 'Stories', 'route' => 'stories.index', 'section' => 'show_stories'],
            ['label' => 'News', 'route' => 'news.index', 'section' => 'show_news'],
            ['label' => 'Gallery', 'route' => 'gallery.index', 'section' => 'show_gallery'],
            ['label' => 'Library', 'route' => 'library.index', 'section' => 'show_library'],
        ],
        'Info' => [
            ['label' => 'Donate', 'route' => 'donations.index'],
            ['label' => 'Contact', 'route' => 'contact'],
        ],
    ];

    $filterVisible = fn ($item) => ! isset($item['section']) || \App\Models\Setting::get('homepage', $item['section'], true) !== '0';

    $primaryNavItems = array_values(array_filter($primaryNavItems, $filterVisible));

    $moreNavGroups = array_filter(array_map(
        fn ($items) => array_values(array_filter($items, $filterVisible)),
        $moreNavGroups
    ));

    $navItems = array_merge($primaryNavItems, ...array_values($moreNavGroups));
@endphp

<div x-data="{ mobileOpen: false }">
<header class="sticky top-3 z-30 mx-3 sm:mx-6 lg:mx-10">
    <div class="flex h-16 items-center justify-between gap-4 rounded-full border border-slate-200/70 bg-white/90 px-4 shadow-lg backdrop-blur dark:border-navy-800 dark:bg-navy-950/90 sm:px-6">
        <a href="{{ route('home') }}" class="flex flex-shrink-0 items-center gap-2 text-base font-bold text-navy-900 dark:text-white">
            <x-brand-icon />
            {{ \App\Models\Setting::get('general', 'site_text', config('app.name')) }}
        </a>

        <nav class="hidden items-center gap-0.5 rounded-full bg-slate-100 p-1 dark:bg-navy-900 xl:flex">
            @foreach ($primaryNavItems as $item)
                @if (Route::has($item['route']))
                    <a
                        href="{{ route($item['route']) }}"
                        class="whitespace-nowrap rounded-full px-2.5 py-1.5 text-xs font-medium {{ request()->routeIs($item['route']) ? 'bg-white text-navy-800 shadow-sm dark:bg-navy-700 dark:text-white' : 'text-slate-600 hover:text-navy-800 dark:text-slate-300 dark:hover:text-white' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach

            @if (!empty($moreNavGroups))
                @php
                    $moreIsActive = collect($moreNavGroups)->flatten(1)->contains(fn ($item) => request()->routeIs($item['route']));
                @endphp
                <div x-data="{ moreOpen: false }" class="relative">
                    <button
                        type="button"
                        @click="moreOpen = !moreOpen"
                        @click.outside="moreOpen = false"
                        class="flex items-center gap-1 whitespace-nowrap rounded-full px-2.5 py-1.5 text-xs font-medium {{ $moreIsActive ? 'bg-white text-navy-800 shadow-sm dark:bg-navy-700 dark:text-white' : 'text-slate-600 hover:text-navy-800 dark:text-slate-300 dark:hover:text-white' }}"
                    >
                        More
                        <x-icon name="chevron-down" class="h-3 w-3" />
                    </button>

                    <div
                        x-show="moreOpen"
                        x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute left-0 z-40 mt-2 w-56 origin-top-left rounded-xl border border-slate-200 bg-white p-2 shadow-popover dark:border-navy-800 dark:bg-navy-900"
                    >
                        @foreach ($moreNavGroups as $groupLabel => $groupItems)
                            @if (!$loop->first)
                                <div class="my-1 border-t border-slate-100 dark:border-navy-800"></div>
                            @endif
                            <p class="px-3 pt-1.5 pb-0.5 text-[10px] font-semibold uppercase tracking-wide text-slate-400">{{ $groupLabel }}</p>
                            @foreach ($groupItems as $item)
                                @if (Route::has($item['route']))
                                    <a
                                        href="{{ route($item['route']) }}"
                                        @click="moreOpen = false"
                                        class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs($item['route']) ? 'bg-slate-100 text-navy-800 dark:bg-navy-800 dark:text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-navy-800 dark:text-slate-300 dark:hover:bg-navy-800 dark:hover:text-white' }}"
                                    >
                                        {{ $item['label'] }}
                                    </a>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                </div>
            @endif
        </nav>

        <div class="flex items-center gap-2">
            <button
                type="button"
                x-data
                @click="$dispatch('open-command-palette')"
                class="hidden h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 dark:bg-navy-900 dark:text-slate-400 dark:hover:bg-navy-800 sm:flex"
                aria-label="Search"
            >
                <x-icon name="search" class="h-5 w-5" />
            </button>

            @auth
                @if (Route::has('notifications.index'))
                    <x-notification-dropdown />
                @endif
                <x-profile-menu />
            @else
                <a href="{{ route('login') }}" class="hidden rounded-full px-3 py-2 text-sm font-medium text-slate-600 hover:text-navy-800 dark:text-slate-300 dark:hover:text-white sm:inline-block">
                    Log In
                </a>
                <x-button :href="route('register')" size="sm" class="hidden !rounded-full sm:inline-flex">Join The Network</x-button>

                <button
                    type="button"
                    x-data="{ dark: document.documentElement.classList.contains('dark') }"
                    @click="document.documentElement.classList.toggle('dark'); dark = document.documentElement.classList.contains('dark'); localStorage.theme = dark ? 'dark' : 'light'"
                    class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 dark:bg-navy-900 dark:text-slate-400 dark:hover:bg-navy-800"
                    aria-label="Toggle dark mode"
                >
                    <x-icon name="moon" x-show="!dark" class="h-5 w-5" />
                    <x-icon name="sun" x-show="dark" x-cloak class="h-5 w-5" />
                </button>
            @endauth

            <button
                type="button"
                @click="mobileOpen = !mobileOpen; document.body.classList.toggle('overflow-hidden', mobileOpen)"
                class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 dark:bg-navy-900 dark:text-slate-400 dark:hover:bg-navy-800 xl:hidden"
            >
                <x-icon name="menu" class="h-5 w-5" />
            </button>
        </div>
    </div>
</header>

<div
        x-show="mobileOpen"
        x-cloak
        x-transition.opacity
        @keydown.escape.window="mobileOpen = false; document.body.classList.remove('overflow-hidden')"
        class="fixed inset-0 z-40 flex flex-col bg-navy-950 text-white xl:hidden"
    >
        <div class="relative flex-1 overflow-hidden">
            <div class="absolute inset-0 opacity-60" x-data="networkBackground()" x-effect="mobileOpen ? start() : stop()">
                <canvas x-ref="networkCanvas" class="h-full w-full"></canvas>
            </div>
            <div class="absolute inset-0 bg-gradient-to-b from-navy-950/40 via-navy-950/70 to-navy-950"></div>

            <div class="relative z-10 flex h-full flex-col overflow-y-auto p-6">
                <div class="flex items-center justify-between">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 text-base font-bold text-white">
                        <x-brand-icon box-class="bg-gold-500 text-navy-950" />
                        {{ \App\Models\Setting::get('general', 'site_text', config('app.name')) }}
                    </a>
                    <button
                        type="button"
                        @click="mobileOpen = false; document.body.classList.remove('overflow-hidden')"
                        class="flex h-9 w-9 items-center justify-center rounded-lg text-white/70 hover:bg-white/10"
                        aria-label="Close menu"
                    >
                        <x-icon name="x" class="h-5 w-5" />
                    </button>
                </div>

                <nav class="mt-8 space-y-1">
                    @foreach ($navItems as $item)
                        @if (Route::has($item['route']))
                            <a href="{{ route($item['route']) }}" class="block rounded-lg px-3 py-3 text-base font-medium text-white/90 hover:bg-white/10">
                                {{ $item['label'] }}
                            </a>
                        @endif
                    @endforeach
                </nav>

                @guest
                    <div class="mt-6 space-y-2 border-t border-white/10 pt-6">
                        <x-button :href="route('login')" variant="secondary" class="w-full !border-white/20 !bg-white/5 !text-white hover:!bg-white/10">Log In</x-button>
                        <x-button :href="route('register')" variant="gold" class="w-full">Join The Network</x-button>

                        <button
                            type="button"
                            x-data="{ dark: document.documentElement.classList.contains('dark') }"
                            @click="document.documentElement.classList.toggle('dark'); dark = document.documentElement.classList.contains('dark'); localStorage.theme = dark ? 'dark' : 'light'"
                            class="flex w-full items-center justify-center gap-2 rounded-lg px-3 py-2.5 text-sm font-medium text-white/70 hover:bg-white/10"
                        >
                            <x-icon name="moon" x-show="!dark" class="h-4 w-4" />
                            <x-icon name="sun" x-show="dark" x-cloak class="h-4 w-4" />
                            <span x-text="dark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"></span>
                        </button>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</div>
