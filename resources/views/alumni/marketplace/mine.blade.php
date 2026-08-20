<x-layouts::alumni :title="'My Listings'">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">My Listings</h1>
        <x-button :href="route('marketplace.create')" size="sm"><x-icon name="plus" class="h-4 w-4" /> Post a Listing</x-button>
    </div>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    @if ($listings->isEmpty())
        <x-empty-state icon="shopping-bag" title="You haven't posted any listings yet" class="mt-8" />
    @else
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($listings as $listing)
                <div class="card overflow-hidden">
                    <div class="flex h-36 items-center justify-center bg-navy-100 text-navy-400 dark:bg-navy-800">
                        @if ($listing->cover_image_url)
                            <img src="{{ $listing->cover_image_url }}" class="h-full w-full object-cover">
                        @else
                            <x-icon name="image" class="h-10 w-10" />
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <x-badge variant="info">{{ $listing->category->name }}</x-badge>
                            <x-badge :variant="match($listing->status) { 'approved' => 'success', 'rejected' => 'danger', 'expired' => 'neutral', default => 'warning' }">
                                {{ ucfirst($listing->status) }}
                            </x-badge>
                        </div>
                        <p class="mt-2 font-semibold text-slate-900 dark:text-white">{{ $listing->title }}</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            ${{ number_format($listing->price, 2) }}{{ $listing->price_unit !== 'total' ? ' / ' . str_replace('per_', '', $listing->price_unit) : '' }}
                        </p>

                        @if ($listing->status === 'rejected' && $listing->rejection_reason)
                            <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $listing->rejection_reason }}</p>
                        @endif

                        <div class="mt-3 flex flex-wrap gap-2 text-xs text-slate-500 dark:text-slate-400">
                            @if ($listing->pending_orders_count)
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">{{ $listing->pending_orders_count }} new inquiry</span>
                            @endif
                            @if ($listing->ongoing_orders_count)
                                <span class="rounded-full bg-blue-100 px-2 py-0.5 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">{{ $listing->ongoing_orders_count }} ongoing</span>
                            @endif
                            @if ($listing->completed_orders_count)
                                <span class="rounded-full bg-green-100 px-2 py-0.5 text-green-700 dark:bg-green-900/40 dark:text-green-300">{{ $listing->completed_orders_count }} completed</span>
                            @endif
                        </div>

                        <div class="mt-3 flex items-center gap-3">
                            <a href="{{ route('marketplace.edit', $listing) }}" class="text-sm font-medium text-navy-700 hover:underline dark:text-navy-300">Edit</a>
                            <form method="POST" action="{{ route('marketplace.destroy', $listing) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Delete this listing?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Delete'}).then(r=>r.isConfirmed&&this.submit())">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-red-600 hover:underline">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $listings->links() }}</div>
    @endif
</x-layouts::alumni>
