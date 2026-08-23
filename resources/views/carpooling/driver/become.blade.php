<x-layouts::alumni :title="'Become a Driver'">
    <x-breadcrumb :items="[['label' => 'Become a Driver']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Become a Driver</h1>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    @if ($profile)
        <div class="mt-6 card card-body">
            <div class="flex items-center gap-2">
                <x-badge :variant="match($profile->status) { 'approved' => 'success', 'rejected' => 'danger', 'suspended' => 'danger', default => 'warning' }">
                    {{ ucfirst($profile->status) }}
                </x-badge>
                @if ($profile->status === 'approved')
                    <x-badge :variant="$profile->is_active ? 'success' : 'neutral'">{{ $profile->is_active ? 'Active' : 'Paused' }}</x-badge>
                @endif
            </div>

            @if ($profile->status === 'rejected' && $profile->rejection_reason)
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $profile->rejection_reason }}</p>
            @endif

            @if ($profile->status === 'approved')
                <div class="mt-4 flex gap-3">
                    <x-button :href="route('carpooling.driver.dashboard')" size="sm">Go to Driver Dashboard</x-button>
                    <form method="POST" action="{{ route('carpooling.driver.toggle-active') }}">
                        @csrf
                        <x-button type="submit" variant="secondary" size="sm">{{ $profile->is_active ? 'Pause Listing' : 'Reactivate' }}</x-button>
                    </form>
                </div>
            @endif
        </div>
    @endif

    @if (!$profile || $profile->status === 'rejected')
        <form method="POST" action="{{ route('carpooling.driver.store') }}" class="card card-body mt-6 space-y-5">
            @csrf
            <p class="text-sm text-slate-500 dark:text-slate-400">Your application will be reviewed by the alumni office before you can post trips.</p>

            <x-input label="Driver's License Number" name="license_number" :value="old('license_number', $profile?->license_number)" required />
            <x-input label="License Expiry" name="license_expiry" type="date" :value="old('license_expiry', $profile?->license_expiry?->format('Y-m-d'))" />
            <x-textarea label="Bio" name="bio" rows="3" placeholder="Tell riders a bit about yourself...">{{ old('bio', $profile?->bio) }}</x-textarea>

            <div class="flex justify-end"><x-button type="submit">Submit Application</x-button></div>
        </form>
    @elseif ($profile->status === 'pending')
        <div class="mt-6 card card-body">
            <p class="text-sm text-slate-500 dark:text-slate-400">Your application is pending review. You can update your details below while you wait.</p>
        </div>
        <form method="POST" action="{{ route('carpooling.driver.store') }}" class="card card-body mt-4 space-y-5">
            @csrf
            <x-input label="Driver's License Number" name="license_number" :value="old('license_number', $profile->license_number)" required />
            <x-input label="License Expiry" name="license_expiry" type="date" :value="old('license_expiry', $profile->license_expiry?->format('Y-m-d'))" />
            <x-textarea label="Bio" name="bio" rows="3">{{ old('bio', $profile->bio) }}</x-textarea>
            <div class="flex justify-end"><x-button type="submit">Save Changes</x-button></div>
        </form>
    @endif
</x-layouts::alumni>
