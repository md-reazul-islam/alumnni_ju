@php $user = auth()->user(); @endphp

<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-2 rounded-full focus:outline-none focus-visible:ring-2 focus-visible:ring-navy-500">
        <x-avatar :src="$user->avatar_url" :name="$user->full_name" size="sm" />
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 z-40 mt-2 w-64 origin-top-right rounded-xl border border-slate-200 bg-white p-2 shadow-popover dark:border-navy-800 dark:bg-navy-900"
    >
        <div class="px-3 py-2.5">
            <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $user->full_name }}</p>
            <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
        </div>
        <div class="my-1 border-t border-slate-100 dark:border-navy-800"></div>

        @if ($user->isAlumni() && Route::has('alumni.profile.show'))
            <a href="{{ route('alumni.profile.show', $user) }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-navy-800">
                <x-icon name="user" class="h-4 w-4 text-slate-400" /> My Profile
            </a>
            <a href="{{ route('alumni.profile.edit') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-navy-800">
                <x-icon name="square-pen" class="h-4 w-4 text-slate-400" /> Edit Profile
            </a>
            @if (Route::has('connections.index'))
                <a href="{{ route('connections.index') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-navy-800">
                    <x-icon name="users" class="h-4 w-4 text-slate-400" /> My Network
                </a>
            @endif
        @endif

        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-navy-800">
            <x-icon name="settings" class="h-4 w-4 text-slate-400" /> Account Settings
        </a>

        @if ($user->isAdminStaff())
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-navy-800">
                <x-icon name="shield" class="h-4 w-4 text-slate-400" /> Admin Panel
            </a>
        @endif

        <button
            type="button"
            @click="document.documentElement.classList.toggle('dark'); localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light'"
            class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-navy-800"
        >
            <x-icon name="moon" class="h-4 w-4 text-slate-400" /> Toggle Dark Mode
        </button>

        <div class="my-1 border-t border-slate-100 dark:border-navy-800"></div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                <x-icon name="log-out" class="h-4 w-4" /> Log Out
            </button>
        </form>
    </div>
</div>
