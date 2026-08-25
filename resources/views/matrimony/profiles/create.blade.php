<x-layouts::alumni :title="'Create Matrimony Profile'">
    <x-breadcrumb :items="[['label' => 'My Matrimony Profiles', 'url' => route('matrimony.profiles.mine')], ['label' => 'Create Profile']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Create a Matrimony Profile</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Save as a draft, add a photo, then submit for admin review when ready.</p>

    <form method="POST" action="{{ route('matrimony.profiles.store') }}" class="card card-body mt-6 space-y-8">
        @csrf
        @include('matrimony.profiles.partials.form')
        <div class="flex justify-end"><x-button type="submit">Save Draft</x-button></div>
    </form>
</x-layouts::alumni>
