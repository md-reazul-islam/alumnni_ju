<x-layouts::admin :title="'Create Moderator'">
    <x-breadcrumb :items="[['label' => 'Moderators', 'url' => route('admin.moderators.index')], ['label' => 'Create']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Create Moderator</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Set up a new moderator account and choose which features they can access.</p>

    <form method="POST" action="{{ route('admin.moderators.store') }}" class="card card-body mt-6 space-y-5">
        @csrf

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-input label="First Name" name="first_name" :value="old('first_name')" required />
            <x-input label="Last Name" name="last_name" :value="old('last_name')" required />
        </div>

        <x-input label="Email Address" name="email" type="email" :value="old('email')" required />

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-input label="Password" name="password" type="password" hint="At least 8 characters." required />
            <x-input label="Confirm Password" name="password_confirmation" type="password" required />
        </div>

        @include('admin.moderators._permissions')

        <div class="flex justify-end gap-3">
            <x-button :href="route('admin.moderators.index')" variant="secondary">Cancel</x-button>
            <x-button type="submit">Create Moderator</x-button>
        </div>
    </form>
</x-layouts::admin>
