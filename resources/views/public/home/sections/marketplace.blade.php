<section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
  <div class="section-container py-5 sm:py-7">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white sm:text-3xl">Marketplace</h2>
            <p class="mt-1.5 text-navy-200">{{ \App\Http\Controllers\Admin\SettingsController::resolveSectionDescription('marketplace') }}</p>
        </div>
        <a href="{{ route('marketplace.index') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
            View all <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>

    @if ($marketplaceListings->isEmpty())
        <x-empty-state icon="shopping-bag" title="No listings available" description="Approved listings from alumni will appear here." class="mt-8" />
    @else
        <div class="mt-8">
            @include('public.marketplace.partials.browser', ['listings' => $marketplaceListings, 'categories' => $marketplaceCategories, 'compact' => true])
        </div>
    @endif
  </div>
</section>
