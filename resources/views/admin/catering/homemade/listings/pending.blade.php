<x-layouts::admin :title="'Pending Home Made Listings'">
    <x-breadcrumb :items="[['label' => 'Catering'], ['label' => 'Pending Home Made Listings']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Pending Home Made Listings</h1>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    @if ($listings->isEmpty())
        <x-empty-state icon="clipboard-check" title="No listings awaiting review" class="mt-8" />
    @else
        <div class="mt-6 space-y-4">
            @foreach ($listings as $listing)
                <div class="card card-body">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <x-badge variant="info">{{ $listing->category->name }}</x-badge>
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $listing->title }}</p>
                            </div>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">${{ number_format($listing->price, 2) }}{{ $listing->price_unit !== 'total' ? ' / ' . str_replace('per_', '', $listing->price_unit) : '' }}</p>
                            <p class="mt-1 text-xs text-slate-400">Submitted by {{ $listing->seller->full_name }} &middot; {{ $listing->created_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('admin.catering.homemade-listings.show', $listing) }}" class="btn-primary btn-sm flex-shrink-0">Review</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $listings->links() }}</div>
    @endif
</x-layouts::admin>
