<x-layouts::alumni :title="'Post a Listing'">
    <x-breadcrumb :items="[['label' => 'Marketplace', 'url' => route('marketplace.index')], ['label' => 'My Listings', 'url' => route('marketplace.mine')], ['label' => 'Post a Listing']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Post a Listing</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Your listing will be reviewed by the alumni office before it goes live. Buyers contact the office, not you directly.</p>

    <form method="POST" action="{{ route('marketplace.store') }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf

        @include('alumni.marketplace.partials.form')

        <div class="flex justify-end">
            <x-button type="submit">Submit for Review</x-button>
        </div>
    </form>
</x-layouts::alumni>
