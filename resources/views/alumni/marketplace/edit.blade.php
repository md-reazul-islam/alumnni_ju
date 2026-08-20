<x-layouts::alumni :title="'Edit Listing'">
    <x-breadcrumb :items="[['label' => 'Marketplace', 'url' => route('marketplace.index')], ['label' => 'My Listings', 'url' => route('marketplace.mine')], ['label' => 'Edit Listing']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Listing</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Saving changes sends this listing back to the alumni office for re-review.</p>

    <form method="POST" action="{{ route('marketplace.update', $listing) }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf
        @method('PUT')

        @include('alumni.marketplace.partials.form')

        <div class="flex justify-end">
            <x-button type="submit">Save Changes</x-button>
        </div>
    </form>
</x-layouts::alumni>
