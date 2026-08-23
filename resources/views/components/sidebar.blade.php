@php
    $sections = [
        ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'route' => 'admin.dashboard'],
        ['label' => 'Alumni', 'icon' => 'graduation-cap', 'match' => 'admin.alumni.*', 'permission' => 'manage-alumni', 'children' => [
            ['label' => 'All Alumni', 'route' => 'admin.alumni.index'],
            ['label' => 'Pending Verification', 'route' => 'admin.alumni.pending'],
            ['label' => 'Verified Alumni', 'route' => 'admin.alumni.verified'],
            ['label' => 'Suspended Alumni', 'route' => 'admin.alumni.suspended'],
        ]],
        ['label' => 'Moderators', 'icon' => 'shield', 'match' => 'admin.moderators.*', 'permission' => 'manage-administrators', 'children' => [
            ['label' => 'All Moderators', 'route' => 'admin.moderators.index'],
            ['label' => 'Create Moderator', 'route' => 'admin.moderators.create'],
        ]],
        ['label' => 'Events', 'icon' => 'calendar', 'match' => 'admin.events.*', 'permission' => 'manage-events', 'children' => [
            ['label' => 'All Events', 'route' => 'admin.events.index'],
            ['label' => 'Create Event', 'route' => 'admin.events.create'],
            ['label' => 'Registrations', 'route' => 'admin.events.registrations'],
        ]],
        ['label' => 'Career', 'icon' => 'briefcase', 'match' => 'admin.jobs.*,admin.companies.*', 'permission' => 'manage-jobs', 'children' => [
            ['label' => 'Jobs', 'route' => 'admin.jobs.index'],
            ['label' => 'Create Job', 'route' => 'admin.jobs.create'],
            ['label' => 'Pending Jobs', 'route' => 'admin.jobs.pending'],
            ['label' => 'Companies', 'route' => 'admin.companies.index'],
        ]],
        ['label' => 'Marketplace', 'icon' => 'shopping-bag', 'match' => 'admin.marketplace.*', 'permission' => 'manage-marketplace', 'children' => [
            ['label' => 'Categories', 'route' => 'admin.marketplace.categories.index'],
            ['label' => 'Pending Listings', 'route' => 'admin.marketplace.listings.pending'],
            ['label' => 'Approved Listings', 'route' => 'admin.marketplace.listings.approved'],
            ['label' => 'Rejected Listings', 'route' => 'admin.marketplace.listings.rejected'],
            ['label' => 'Orders', 'route' => 'admin.marketplace.orders.index'],
            ['label' => 'Reports', 'route' => 'admin.marketplace.reports.index'],
        ]],
        ['label' => 'Carpooling', 'icon' => 'car', 'match' => 'admin.carpooling.*', 'permission' => 'manage-carpooling', 'children' => [
            ['label' => 'Pending Drivers', 'route' => 'admin.carpooling.drivers.pending'],
            ['label' => 'Approved Drivers', 'route' => 'admin.carpooling.drivers.approved'],
            ['label' => 'Rejected Drivers', 'route' => 'admin.carpooling.drivers.rejected'],
            ['label' => 'Pending Trips', 'route' => 'admin.carpooling.schedules.pending'],
            ['label' => 'Approved Trips', 'route' => 'admin.carpooling.schedules.approved'],
            ['label' => 'Rejected Trips', 'route' => 'admin.carpooling.schedules.rejected'],
        ]],
        ['label' => 'Library', 'icon' => 'book-open', 'match' => 'admin.library.*', 'permission' => 'manage-library', 'children' => [
            ['label' => 'All Books', 'route' => 'admin.library.index'],
            ['label' => 'Add Book', 'route' => 'admin.library.create'],
            ['label' => 'Available Books', 'route' => 'admin.library.available'],
            ['label' => 'Pending Requests', 'route' => 'admin.library.requests.pending'],
            ['label' => 'Accepted Requests', 'route' => 'admin.library.requests.accepted'],
            ['label' => 'Rejected Requests', 'route' => 'admin.library.requests.rejected'],
            ['label' => 'Borrowed Books', 'route' => 'admin.library.requests.borrowed'],
            ['label' => 'Borrow History', 'route' => 'admin.library.requests.history'],
        ]],
        ['label' => 'Content', 'icon' => 'file-text', 'match' => 'admin.news.*,admin.stories.*,admin.announcements.*,admin.gallery.*', 'children' => [
            ['label' => 'News', 'route' => 'admin.news.index', 'permission' => 'manage-news'],
            ['label' => 'Stories', 'route' => 'admin.stories.index', 'permission' => 'manage-stories'],
            ['label' => 'Announcements', 'route' => 'admin.announcements.index', 'permission' => 'manage-announcements'],
            ['label' => 'Gallery', 'route' => 'admin.gallery.index', 'permission' => 'manage-gallery'],
        ]],
        ['label' => 'Homepage Slider', 'icon' => 'image', 'match' => 'admin.sliders.*', 'permission' => 'manage-sliders', 'children' => [
            ['label' => 'All Slides', 'route' => 'admin.sliders.index'],
            ['label' => 'Add Slide', 'route' => 'admin.sliders.create'],
        ]],
        ['label' => 'Community', 'icon' => 'message-square', 'match' => 'admin.community.*', 'permission' => 'moderate-community', 'children' => [
            ['label' => 'Posts', 'route' => 'admin.community.posts'],
            ['label' => 'Reports', 'route' => 'admin.community.reports'],
            ['label' => 'Moderation', 'route' => 'admin.community.moderation'],
        ]],
        ['label' => 'Mentorship', 'icon' => 'handshake', 'match' => 'admin.mentorship.*', 'permission' => 'manage-mentorship', 'children' => [
            ['label' => 'Mentors', 'route' => 'admin.mentorship.mentors'],
            ['label' => 'Requests', 'route' => 'admin.mentorship.requests'],
        ]],
        ['label' => 'Scholarships', 'icon' => 'award', 'route' => 'admin.scholarships.index', 'permission' => 'manage-scholarships'],
        ['label' => 'Donations', 'icon' => 'dollar-sign', 'match' => 'admin.donations.*', 'permission' => 'manage-donations', 'children' => [
            ['label' => 'Donations', 'route' => 'admin.donations.index'],
            ['label' => 'Campaigns', 'route' => 'admin.donations.campaigns'],
            ['label' => 'Reports', 'route' => 'admin.donations.reports'],
        ]],
        ['label' => 'Messages', 'icon' => 'message-circle', 'route' => 'admin.messages.index'],
        ['label' => 'Notifications', 'icon' => 'bell', 'route' => 'notifications.index'],
        ['label' => 'Reports', 'icon' => 'chart-bar', 'route' => 'admin.reports.index', 'permission' => ['manage-reports', 'manage-alumni']],
        ['label' => 'Audit Logs', 'icon' => 'clipboard-list', 'route' => 'admin.audit-logs.index', 'permission' => 'view-audit-logs'],
        ['label' => 'Settings', 'icon' => 'settings', 'route' => 'admin.settings.index', 'permission' => 'manage-settings'],
    ];

    $hasAccess = function ($permission) {
        if (! $permission) {
            return true;
        }

        foreach ((array) $permission as $slug) {
            if (auth()->user()->hasPermission($slug)) {
                return true;
            }
        }

        return false;
    };

    $sections = collect($sections)
        ->map(function ($section) use ($hasAccess) {
            if (isset($section['children'])) {
                $section['children'] = array_values(array_filter(
                    $section['children'],
                    fn ($child) => $hasAccess($child['permission'] ?? ($section['permission'] ?? null))
                ));
            }

            return $section;
        })
        ->filter(function ($section) use ($hasAccess) {
            if (isset($section['children'])) {
                return count($section['children']) > 0;
            }

            return $hasAccess($section['permission'] ?? null);
        })
        ->values()
        ->all();

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
                <x-brand-icon box-class="bg-gold-500 text-navy-950" />
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
