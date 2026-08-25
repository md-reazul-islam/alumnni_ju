<x-layouts::alumni :title="'Blocked Users'">
    <x-breadcrumb :items="[['label' => 'Blocked Users']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Blocked Users</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Blocked members cannot see your matrimony profiles, send you interest requests, or appear in your search results.</p>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    @if ($blocks->isEmpty())
        <x-empty-state icon="ban" title="You haven't blocked anyone" class="mt-8" />
    @else
        <div class="mt-6 space-y-3">
            @foreach ($blocks as $block)
                <div class="card card-body flex items-center justify-between">
                    <div>
                        <p class="font-medium text-slate-900 dark:text-white">{{ $block->blocked->full_name }}</p>
                        @if ($block->reason)
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $block->reason }}</p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('matrimony.blocks.destroy', $block->blocked) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm font-medium text-navy-700 hover:underline dark:text-navy-300">Unblock</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts::alumni>
