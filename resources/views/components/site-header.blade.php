@php
    $navItems = [
        ['label' => 'About', 'route' => 'about'],
        ['label' => 'Alumni', 'route' => 'alumni.directory'],
        ['label' => 'Events', 'route' => 'events.index'],
        ['label' => 'Careers', 'route' => 'jobs.index'],
        ['label' => 'Stories', 'route' => 'stories.index'],
        ['label' => 'News', 'route' => 'news.index'],
        ['label' => 'Donate', 'route' => 'donations.index'],
        ['label' => 'Contact', 'route' => 'contact'],
    ];
@endphp

<header x-data="{ mobileOpen: false }" class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur dark:border-navy-800 dark:bg-navy-950/90">
    <div class="section-container flex h-16 items-center justify-between gap-4">
        <a href="{{ route('home') }}" class="flex flex-shrink-0 items-center gap-2 text-base font-bold text-navy-900 dark:text-white">
            <x-brand-icon />
            {{ \App\Models\Setting::get('general', 'site_text', config('app.name')) }}
        </a>

        <nav class="hidden items-center gap-1 xl:flex">
            @foreach ($navItems as $item)
                @if (Route::has($item['route']))
                    <a
                        href="{{ route($item['route']) }}"
                        class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs($item['route']) ? 'bg-navy-50 text-navy-800 dark:bg-navy-800 dark:text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-navy-800 dark:text-slate-300 dark:hover:bg-navy-800 dark:hover:text-white' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            <button
                type="button"
                x-data
                @click="$dispatch('open-command-palette')"
                class="hidden h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-navy-800 sm:flex"
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
                <a href="{{ route('login') }}" class="hidden rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:text-navy-800 dark:text-slate-300 dark:hover:text-white sm:inline-block">
                    Log In
                </a>
                <x-button :href="route('register')" size="sm" class="hidden sm:inline-flex">Join the Alumni Network</x-button>

                <button
                    type="button"
                    x-data="{ dark: document.documentElement.classList.contains('dark') }"
                    @click="document.documentElement.classList.toggle('dark'); dark = document.documentElement.classList.contains('dark'); localStorage.theme = dark ? 'dark' : 'light'"
                    class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-navy-800"
                    aria-label="Toggle dark mode"
                >
                    <x-icon name="moon" x-show="!dark" class="h-5 w-5" />
                    <x-icon name="sun" x-show="dark" x-cloak class="h-5 w-5" />
                </button>
            @endauth

            <button
                type="button"
                @click="mobileOpen = !mobileOpen"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-navy-800 xl:hidden"
            >
                <x-icon name="menu" class="h-5 w-5" />
            </button>
        </div>
    </div>

    <nav x-show="mobileOpen" x-cloak x-transition class="space-y-1 border-t border-slate-100 p-4 dark:border-navy-800 xl:hidden">
        @foreach ($navItems as $item)
            @if (Route::has($item['route']))
                <a href="{{ route($item['route']) }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-navy-800">
                    {{ $item['label'] }}
                </a>
            @endif
        @endforeach

        @guest
            <div class="mt-3 space-y-2 border-t border-slate-100 pt-3 dark:border-navy-800">
                <x-button :href="route('login')" variant="secondary" class="w-full">Log In</x-button>
                <x-button :href="route('register')" class="w-full">Join the Alumni Network</x-button>

                <button
                    type="button"
                    x-data="{ dark: document.documentElement.classList.contains('dark') }"
                    @click="document.documentElement.classList.toggle('dark'); dark = document.documentElement.classList.contains('dark'); localStorage.theme = dark ? 'dark' : 'light'"
                    class="flex w-full items-center justify-center gap-2 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-navy-800"
                >
                    <x-icon name="moon" x-show="!dark" class="h-4 w-4" />
                    <x-icon name="sun" x-show="dark" x-cloak class="h-4 w-4" />
                    <span x-text="dark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"></span>
                </button>
            </div>
        @endguest
    </nav>
</header>
