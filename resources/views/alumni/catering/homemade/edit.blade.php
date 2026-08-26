<x-layouts::alumni :title="'Edit Home Made Listing'">
    <x-breadcrumb :items="[['label' => 'Catering', 'url' => route('catering.search')], ['label' => 'My Home Made Listings', 'url' => route('catering.homemade.mine')], ['label' => 'Edit Listing']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Listing</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Saving changes sends this listing back to the catering team for re-review.</p>

    <form method="POST" action="{{ route('catering.homemade.update', $listing) }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf
        @method('PUT')

        @include('alumni.catering.homemade.partials.form')

        <div class="flex justify-end">
            <x-button type="submit">Save Changes</x-button>
        </div>
    </form>
</x-layouts::alumni>
