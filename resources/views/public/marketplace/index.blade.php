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

        @if ($listings->isEmpty())
            <x-empty-state icon="shopping-bag" title="No listings available" description="Approved listings from alumni will appear here." class="mt-8" />
        @else
            <div class="mt-6">
                @include('public.marketplace.partials.browser')
            </div>
        @endif
      </div>
    </div>
</x-layouts::app>
