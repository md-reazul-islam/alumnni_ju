<x-layouts::admin :title="'Catering Food Items'">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Food Items</h1>
        <x-button :href="route('admin.catering.items.create')" size="sm"><x-icon name="plus" class="h-4 w-4" /> Add Item</x-button>
    </div>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">The catalog customers pick from when building a catering order.</p>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    <form method="GET" class="mt-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search items" class="form-input max-w-xs">
    </form>

    @if ($items->isEmpty())
        <x-empty-state icon="utensils" title="No food items yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Item</th><th>Categories</th><th>Base Price</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $item->name }}</td>
                        <td class="text-sm text-slate-500 dark:text-slate-400">{{ $item->categories->pluck('name')->join(', ') }}</td>
                        <td>${{ number_format($item->base_price, 2) }} <span class="text-xs text-slate-400">{{ $item->unit_label }}</span></td>
                        <td><x-badge :variant="$item->is_active ? 'success' : 'neutral'">{{ $item->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                        <td class="flex items-center gap-3">
                            <a href="{{ route('admin.catering.items.edit', $item) }}" class="text-slate-400 hover:text-navy-700"><x-icon name="pencil" class="h-4 w-4" /></a>
                            <form method="POST" action="{{ route('admin.catering.items.destroy', $item) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Remove this item?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Remove'}).then(r=>r.isConfirmed&&this.submit())">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-600"><x-icon name="trash-2" class="h-4 w-4" /></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $items->links() }}</div>
    @endif
</x-layouts::admin>
