<x-layouts::admin :title="'Matrimony Conversations'">
    <x-breadcrumb :items="[['label' => 'Matrimony'], ['label' => 'Conversations']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Matrimony Conversations</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Read-only oversight of every conversation started after a mutual interest match, for member safety.</p>

    @if ($conversations->isEmpty())
        <x-empty-state icon="message-circle" title="No matrimony conversations yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Participants</th><th>Last Message</th><th>Started</th><th></th></tr></thead>
            <tbody>
                @foreach ($conversations as $conversation)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $conversation->participants->pluck('full_name')->join(' & ') }}</td>
                        <td class="max-w-xs truncate">{{ $conversation->latestMessage?->body ?? '—' }}</td>
                        <td>{{ $conversation->created_at->format('M j, Y') }}</td>
                        <td><a href="{{ route('admin.matrimony.conversations.show', $conversation) }}" class="text-navy-700 hover:underline dark:text-navy-300">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $conversations->links() }}</div>
    @endif
</x-layouts::admin>
