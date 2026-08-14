<x-layouts::alumni :title="'Account Settings'">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Account Settings</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage your login credentials and account security.</p>

    <div class="mt-6 space-y-6">
        <div class="card card-body">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="card card-body">
            @include('profile.partials.update-password-form')
        </div>

        <div class="card card-body border-red-200 dark:border-red-900/40">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-layouts::alumni>
