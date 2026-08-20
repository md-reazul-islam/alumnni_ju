<x-layouts::admin :title="'Approved Listings'">
    <x-breadcrumb :items="[['label' => 'Marketplace'], ['label' => 'Approved Listings']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Approved Listings</h1>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    @if ($listings->isEmpty())
        <x-empty-state icon="shopping-bag" title="No approved listings yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Title</th><th>Category</th><th>Seller</th><th>Price</th><th>Approved</th><th></th></tr></thead>
            <tbody>
                @foreach ($listings as $listing)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">
                            <a href="{{ route('admin.marketplace.listings.show', $listing) }}" class="hover:underline">{{ $listing->title }}</a>
                        </td>
                        <td>{{ $listing->category->name }}</td>
                        <td>{{ $listing->seller->full_name }}</td>
                        <td>${{ number_format($listing->price, 2) }}</td>
                        <td>{{ $listing->approved_at?->format('M j, Y') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.marketplace.listings.destroy', $listing) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Delete this listing?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Delete'}).then(r=>r.isConfirmed&&this.submit())">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-600"><x-icon name="trash-2" class="h-4 w-4" /></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $listings->links() }}</div>
    @endif
</x-layouts::admin>
