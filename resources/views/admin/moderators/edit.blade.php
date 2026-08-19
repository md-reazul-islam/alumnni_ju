<x-layouts::admin :title="'Edit Moderator'">
    <x-breadcrumb :items="[['label' => 'Moderators', 'url' => route('admin.moderators.index')], ['label' => $moderator->full_name]]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Moderator</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Update account details and adjust feature access for {{ $moderator->full_name }}.</p>

    <form method="POST" action="{{ route('admin.moderators.update', $moderator) }}" class="card card-body mt-6 space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-input label="First Name" name="first_name" :value="old('first_name', $moderator->first_name)" required />
            <x-input label="Last Name" name="last_name" :value="old('last_name', $moderator->last_name)" required />
        </div>

        <x-input label="Email Address" name="email" type="email" :value="old('email', $moderator->email)" required />

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-input label="New Password" name="password" type="password" hint="Leave blank to keep the current password." />
            <x-input label="Confirm New Password" name="password_confirmation" type="password" />
        </div>

        @include('admin.moderators._permissions')

        <div class="flex justify-end gap-3">
            <x-button :href="route('admin.moderators.index')" variant="secondary">Cancel</x-button>
            <x-button type="submit">Save Changes</x-button>
        </div>
    </form>
</x-layouts::admin>
