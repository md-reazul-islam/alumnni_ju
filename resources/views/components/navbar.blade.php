@php
    $primaryNavItems = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard'],
        ['label' => 'Directory', 'route' => 'alumni.directory', 'icon' => 'users'],
        ['label' => 'Events', 'route' => 'events.index', 'icon' => 'calendar', 'section' => 'show_events'],
        ['label' => 'Careers', 'route' => 'jobs.index', 'icon' => 'briefcase', 'section' => 'show_jobs'],
        ['label' => 'Community', 'route' => 'community.index', 'icon' => 'message-square'],
    ];

    $moreNavItems = [
        ['label' => 'Mentorship', 'route' => 'mentorship.index', 'icon' => 'handshake'],
        ['label' => 'Carpooling panel', 'route' => 'carpooling.driver.become', 'icon' => 'car', 'section' => 'show_carpooling'],
        ['label' => 'Catering', 'route' => 'catering.search', 'icon' => 'utensils', 'section' => 'show_catering'],
        ['label' => 'Matrimony', 'route' => 'matrimony.search', 'icon' => 'heart', 'section' => 'show_matrimony'],
        ['label' => 'Media Advocacy', 'route' => 'media-advocacy.index', 'icon' => 'megaphone', 'section' => 'show_media_advocacy'],
    ];

    $filterVisible = fn ($item) => ! isset($item['section']) || \App\Models\Setting::get('homepage', $item['section'], true) !== '0';

    $primaryNavItems = array_values(array_filter($primaryNavItems, $filterVisible));
    $moreNavItems = array_values(array_filter($moreNavItems, $filterVisible));

    $navItems = array_merge($primaryNavItems, $moreNavItems);
@endphp

<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur dark:border-navy-800 dark:bg-navy-950/90">
    <div class="section-container flex h-16 items-center justify-between gap-4">
        <div class="flex items-center gap-8">
            <a href="{{ route('home') }}" class="flex flex-shrink-0 items-center gap-2 text-base font-bold text-navy-900 dark:text-white">
                <x-brand-icon />
                <span class="hidden sm:inline">{{ \App\Models\Setting::get('general', 'site_text', config('app.name')) }}</span>
            </a>

            <nav class="hidden items-center gap-1 lg:flex">
                @foreach ($primaryNavItems as $item)
                    @if (Route::has($item['route']))
                        <a
                            href="{{ route($item['route']) }}"
                            class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs(explode('.', $item['route'])[0] . '.*') ? 'bg-navy-50 text-navy-800 dark:bg-navy-800 dark:text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-navy-800 dark:text-slate-300 dark:hover:bg-navy-800 dark:hover:text-white' }}"
                        >
                            {{ $item['label'] }}
                        </a>
                    @endif
                @endforeach

                @if (!empty($moreNavItems))
                    @php
                        $moreIsActive = collect($moreNavItems)->contains(fn ($item) => request()->routeIs(explode('.', $item['route'])[0] . '.*'));
                    @endphp
                    <div x-data="{ moreOpen: false }" class="relative">
                        <button
                            type="button"
                            @click="moreOpen = !moreOpen"
                            @click.outside="moreOpen = false"
                            class="flex items-center gap-1 rounded-lg px-3 py-2 text-sm font-medium {{ $moreIsActive ? 'bg-navy-50 text-navy-800 dark:bg-navy-800 dark:text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-navy-800 dark:text-slate-300 dark:hover:bg-navy-800 dark:hover:text-white' }}"
                        >
                            More
                            <x-icon name="chevron-down" class="h-3.5 w-3.5" />
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
                            class="absolute left-0 z-40 mt-2 w-52 origin-top-left rounded-xl border border-slate-200 bg-white p-2 shadow-popover dark:border-navy-800 dark:bg-navy-900"
                        >
                            @foreach ($moreNavItems as $item)
                                @if (Route::has($item['route']))
                                    <a
                                        href="{{ route($item['route']) }}"
                                        @click="moreOpen = false"
                                        class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs(explode('.', $item['route'])[0] . '.*') ? 'bg-navy-50 text-navy-800 dark:bg-navy-800 dark:text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-navy-800 dark:text-slate-300 dark:hover:bg-navy-800 dark:hover:text-white' }}"
                                    >
                                        <x-icon :name="$item['icon']" class="h-4 w-4 text-slate-400" />
                                        {{ $item['label'] }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
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
