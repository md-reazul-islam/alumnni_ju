@php
    $user = auth()->user();
    $notifications = $user->notifications()->latest()->limit(8)->get();
    $unreadCount = $user->unreadNotifications()->count();
@endphp

<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" @click.outside="open = false" class="relative flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-navy-800">
        <x-icon name="bell" class="h-5 w-5" />
        @if ($unreadCount > 0)
            <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white dark:ring-navy-900"></span>
        @endif
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
        class="absolute right-0 z-40 mt-2 w-80 origin-top-right rounded-xl border border-slate-200 bg-white shadow-popover dark:border-navy-800 dark:bg-navy-900"
    >
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 dark:border-navy-800">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Notifications</p>
            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-xs font-medium text-navy-700 hover:text-navy-900 dark:text-navy-300">Mark all read</button>
                </form>
            @endif
        </div>

        <div class="max-h-96 divide-y divide-slate-100 overflow-y-auto dark:divide-navy-800">
            @forelse ($notifications as $notification)
                <a href="{{ $notification->data['url'] ?? '#' }}" class="flex gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-navy-800 {{ $notification->read_at ? '' : 'bg-navy-50/60 dark:bg-navy-800/60' }}">
                    <span class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-navy-50 text-navy-700 dark:bg-navy-800 dark:text-navy-200">
                        <x-icon :name="$notification->data['icon'] ?? 'bell'" class="h-4 w-4" />
                    </span>
                    <span class="min-w-0">
                        <span class="block truncate text-sm text-slate-700 dark:text-slate-200">{{ $notification->data['message'] ?? 'Notification' }}</span>
                        <span class="text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</span>
                    </span>
                </a>
            @empty
                <div class="px-4 py-10 text-center">
                    <x-icon name="bell" class="mx-auto h-8 w-8 text-slate-300 dark:text-navy-700" />
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">No notifications yet.</p>
                </div>
            @endforelse
        </div>

        <a href="{{ route('notifications.index') }}" class="block border-t border-slate-100 px-4 py-2.5 text-center text-xs font-semibold text-navy-700 hover:bg-slate-50 dark:border-navy-800 dark:text-navy-300 dark:hover:bg-navy-800">
            View all notifications
        </a>
    </div>
</div>
