@php
    $sections = [
        ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'route' => 'admin.dashboard'],
        ['label' => 'Alumni', 'icon' => 'graduation-cap', 'match' => 'admin.alumni.*', 'children' => [
            ['label' => 'All Alumni', 'route' => 'admin.alumni.index'],
            ['label' => 'Pending Verification', 'route' => 'admin.alumni.pending'],
            ['label' => 'Verified Alumni', 'route' => 'admin.alumni.verified'],
            ['label' => 'Suspended Alumni', 'route' => 'admin.alumni.suspended'],
        ]],
        ['label' => 'Events', 'icon' => 'calendar', 'match' => 'admin.events.*', 'children' => [
            ['label' => 'All Events', 'route' => 'admin.events.index'],
            ['label' => 'Create Event', 'route' => 'admin.events.create'],
            ['label' => 'Registrations', 'route' => 'admin.events.registrations'],
        ]],
        ['label' => 'Career', 'icon' => 'briefcase', 'match' => 'admin.jobs.*,admin.companies.*', 'children' => [
            ['label' => 'Jobs', 'route' => 'admin.jobs.index'],
            ['label' => 'Pending Jobs', 'route' => 'admin.jobs.pending'],
            ['label' => 'Companies', 'route' => 'admin.companies.index'],
        ]],
        ['label' => 'Content', 'icon' => 'file-text', 'match' => 'admin.news.*,admin.stories.*,admin.announcements.*', 'children' => [
            ['label' => 'News', 'route' => 'admin.news.index'],
            ['label' => 'Stories', 'route' => 'admin.stories.index'],
            ['label' => 'Announcements', 'route' => 'admin.announcements.index'],
        ]],
        ['label' => 'Community', 'icon' => 'message-square', 'match' => 'admin.community.*', 'children' => [
            ['label' => 'Posts', 'route' => 'admin.community.posts'],
            ['label' => 'Reports', 'route' => 'admin.community.reports'],
            ['label' => 'Moderation', 'route' => 'admin.community.moderation'],
        ]],
        ['label' => 'Mentorship', 'icon' => 'handshake', 'match' => 'admin.mentorship.*', 'children' => [
            ['label' => 'Mentors', 'route' => 'admin.mentorship.mentors'],
            ['label' => 'Requests', 'route' => 'admin.mentorship.requests'],
        ]],
        ['label' => 'Scholarships', 'icon' => 'award', 'route' => 'admin.scholarships.index'],
        ['label' => 'Donations', 'icon' => 'dollar-sign', 'match' => 'admin.donations.*', 'children' => [
            ['label' => 'Donations', 'route' => 'admin.donations.index'],
            ['label' => 'Campaigns', 'route' => 'admin.donations.campaigns'],
            ['label' => 'Reports', 'route' => 'admin.donations.reports'],
        ]],
        ['label' => 'Messages', 'icon' => 'message-circle', 'route' => 'admin.messages.index'],
        ['label' => 'Notifications', 'icon' => 'bell', 'route' => 'notifications.index'],
        ['label' => 'Reports', 'icon' => 'chart-bar', 'route' => 'admin.reports.index'],
        ['label' => 'Audit Logs', 'icon' => 'clipboard-list', 'route' => 'admin.audit-logs.index'],
        ['label' => 'Settings', 'icon' => 'settings', 'route' => 'admin.settings.index'],
    ];

    $isActiveSection = function ($section) {
        if (isset($section['route'])) {
            return Route::has($section['route']) && request()->routeIs($section['route']);
        }
        foreach (explode(',', $section['match'] ?? '') as $pattern) {
            if (request()->routeIs(trim($pattern))) {
                return true;
            }
        }
        return false;
    };
@endphp

<aside
    x-data="{ mobileOpen: false, collapsed: false }"
    x-on:open-admin-nav.window="mobileOpen = true"
>
    <div x-show="mobileOpen" x-cloak class="fixed inset-0 z-40 bg-black/30 lg:hidden" @click="mobileOpen = false"></div>

    <nav
        :class="[mobileOpen ? 'translate-x-0' : '-translate-x-full', collapsed ? 'lg:w-20' : 'lg:w-64']"
        class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col overflow-y-auto border-r border-navy-800 bg-navy-950 text-navy-200 transition-all duration-200 lg:static lg:z-auto lg:translate-x-0"
    >
        <div class="flex h-16 flex-shrink-0 items-center justify-between px-5">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 text-white">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gold-500 text-navy-950">
                    <x-icon name="graduation-cap" class="h-4 w-4" />
                </span>
                <span x-show="!collapsed" class="text-sm font-bold">Admin Panel</span>
            </a>
            <button @click="mobileOpen = false" class="text-navy-400 hover:text-white lg:hidden">
                <x-icon name="x" class="h-5 w-5" />
            </button>
        </div>

        <div class="flex-1 space-y-1 px-3 pb-6">
            @foreach ($sections as $section)
                @php $active = $isActiveSection($section); @endphp

                @if (isset($section['children']))
                    <div x-data="{ open: {{ $active ? 'true' : 'false' }} }">
                        <button
                            @click="open = !open"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium {{ $active ? 'bg-navy-800 text-white' : 'text-navy-300 hover:bg-navy-900 hover:text-white' }}"
                        >
                            <span class="flex items-center gap-3">
                                <x-icon :name="$section['icon']" class="h-4 w-4 flex-shrink-0" />
                                <span x-show="!collapsed">{{ $section['label'] }}</span>
                            </span>
                            <span x-show="!collapsed" :class="open ? 'rotate-180' : ''" class="transition-transform">
                                <x-icon name="chevron-down" class="h-3.5 w-3.5" />
                            </span>
                        </button>

                        <div x-show="open && !collapsed" x-transition class="mt-1 space-y-0.5 pl-9">
                            @foreach ($section['children'] as $child)
                                <a
                                    href="{{ Route::has($child['route']) ? route($child['route']) : '#' }}"
                                    class="block rounded-lg px-3 py-2 text-sm {{ Route::has($child['route']) && request()->routeIs($child['route']) ? 'font-semibold text-gold-400' : 'text-navy-300 hover:text-white' }}"
                                >
                                    {{ $child['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a
                        href="{{ Route::has($section['route']) ? route($section['route']) : '#' }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium {{ $active ? 'bg-navy-800 text-white' : 'text-navy-300 hover:bg-navy-900 hover:text-white' }}"
                    >
                        <x-icon :name="$section['icon']" class="h-4 w-4 flex-shrink-0" />
                        <span x-show="!collapsed">{{ $section['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </div>

        <button @click="collapsed = !collapsed" class="hidden flex-shrink-0 items-center justify-center gap-2 border-t border-navy-800 py-3 text-navy-400 hover:text-white lg:flex">
            <x-icon name="panel-left" class="h-4 w-4" />
        </button>
    </nav>
</aside>
