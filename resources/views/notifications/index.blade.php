@php $layout = auth()->user()->isAdminStaff() ? 'layouts::admin' : 'layouts::alumni'; @endphp
<x-dynamic-component :component="$layout" :title="'Notifications'">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Notifications</h1>

    <div class="mt-6 space-y-2">
        @forelse ($notifications as $notification)
            <div class="card card-body flex items-start gap-3 {{ $notification->read_at ? '' : 'ring-1 ring-navy-200 dark:ring-navy-700' }}">
                <span class="mt-0.5 flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-navy-50 text-navy-700 dark:bg-navy-800 dark:text-navy-200">
                    <x-icon :name="$notification->data['icon'] ?? 'bell'" class="h-4 w-4" />
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm text-slate-700 dark:text-slate-200">{{ $notification->data['message'] ?? 'Notification' }}</p>
                    <p class="mt-0.5 text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
                @if (!$notification->read_at)
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                        @csrf
                        <button type="submit" class="text-xs font-semibold text-navy-700 hover:text-navy-900 dark:text-navy-300">Mark read</button>
                    </form>
                @endif
            </div>
        @empty
            <x-empty-state icon="bell" title="No notifications yet" description="You'll see connection requests, event reminders, and admin announcements here." />
        @endforelse
    </div>

    <div class="mt-6">{{ $notifications->links() }}</div>
</x-dynamic-component>
