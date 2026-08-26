<x-layouts::app>
    <div>
      <div class="section-container py-8">
        <x-breadcrumb :items="[['label' => 'Catering', 'url' => route('catering.search')], ['label' => 'Home Made Foods']]" onDark class="mb-4" />

        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">Home Made Foods</h1>
                <p class="mt-1.5 text-navy-200">Home-cooked food made and sold by fellow alumni.</p>
            </div>
            @auth
                <x-button :href="route('catering.homemade.create')" size="sm">List a Food</x-button>
            @endauth
        </div>

        @if ($listings->isEmpty())
            <x-empty-state icon="cooking-pot" title="No home made foods available" description="Approved listings from alumni will appear here." class="mt-8" />
        @else
            <div class="mt-6">
                @include('public.catering.homemade.partials.browser')
            </div>
        @endif
      </div>
    </div>
</x-layouts::app>
