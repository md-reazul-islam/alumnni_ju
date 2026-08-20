<x-layouts::app>
    <div>
      <div class="section-container py-8">
        <x-breadcrumb :items="[['label' => 'Marketplace']]" onDark class="mb-4" />

        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">Marketplace</h1>
                <p class="mt-1.5 text-navy-200">House rentals, property, and used items posted by alumni.</p>
            </div>
            @auth
                <x-button :href="route('marketplace.create')" size="sm">Post a Listing</x-button>
            @endauth
        </div>

        <form method="GET" class="mt-6 flex flex-col gap-3 sm:flex-row">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title or address"
                   class="form-input sm:max-w-xs" />
            <select name="category" class="form-select sm:max-w-xs">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <x-button type="submit" variant="secondary">Filter</x-button>
        </form>

        @if ($listings->isEmpty())
            <x-empty-state icon="shopping-bag" title="No listings available" description="Approved listings from alumni will appear here." class="mt-8" />
        @else
            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($listings as $listing)
                    <a href="{{ route('marketplace.show', $listing) }}" class="card overflow-hidden transition hover:shadow-popover">
                        <div class="flex h-40 items-center justify-center bg-navy-100 text-navy-400 dark:bg-navy-800">
                            @if ($listing->cover_image_url)
                                <img src="{{ $listing->cover_image_url }}" class="h-full w-full object-cover">
                            @else
                                <x-icon name="image" class="h-10 w-10" />
                            @endif
                        </div>
                        <div class="card-body">
                            <x-badge variant="info">{{ $listing->category->name }}</x-badge>
                            <p class="mt-2 font-semibold text-slate-900 dark:text-white">{{ $listing->title }}</p>
                            <p class="mt-1 flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400">
                                <x-icon name="map-pin" class="h-4 w-4" /> {{ $listing->city ?: $listing->address }}
                            </p>
                            <p class="mt-2 font-semibold text-navy-700 dark:text-gold-400">
                                ${{ number_format($listing->price, 2) }}{{ $listing->price_unit !== 'total' ? ' / ' . str_replace('per_', '', $listing->price_unit) : '' }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">{{ $listings->links('vendor.pagination.tailwind-dark') }}</div>
        @endif
      </div>
    </div>
</x-layouts::app>
