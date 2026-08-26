<x-layouts::alumni :title="'List a Home Made Food'">
    <x-breadcrumb :items="[['label' => 'Catering', 'url' => route('catering.search')], ['label' => 'My Home Made Listings', 'url' => route('catering.homemade.mine')], ['label' => 'New Listing']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">List a Home Made Food</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Your listing will be reviewed by the catering team before it goes live. Buyers contact the office, not you directly.</p>

    <form method="POST" action="{{ route('catering.homemade.store') }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf

        @include('alumni.catering.homemade.partials.form')

        <div class="flex justify-end">
            <x-button type="submit">Submit for Review</x-button>
        </div>
    </form>
</x-layouts::alumni>
