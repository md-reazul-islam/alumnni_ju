<x-layouts::alumni :title="'Edit Matrimony Profile'">
    <x-breadcrumb :items="[['label' => 'My Matrimony Profiles', 'url' => route('matrimony.profiles.mine')], ['label' => 'Edit Profile']]" class="mb-4" />

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <x-badge :variant="match($profile->status) { 'approved' => 'success', 'rejected', 'suspended' => 'danger', default => 'warning' }">
                    {{ ucfirst($profile->status) }}
                </x-badge>
                @if ($profile->is_verified)
                    <x-badge variant="info">Verified</x-badge>
                @endif
            </div>
            <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $profile->display_name ?: $profile->full_name }}</h1>
        </div>
        <div class="text-right">
            <p class="text-sm text-slate-500 dark:text-slate-400">Profile completion</p>
            <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $profile->profile_completion }}%</p>
        </div>
    </div>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    @if (Route::has('matrimony.profiles.viewers'))
        <div class="mt-4">
            <a href="{{ route('matrimony.profiles.viewers', $profile) }}" class="text-sm font-medium text-navy-700 hover:underline dark:text-navy-300">
                {{ $profile->views_count }} profile view(s) &middot; See who viewed you &rarr;
            </a>
        </div>
    @endif

    @if ($profile->status === 'rejected' && $profile->rejection_reason)
        <x-alert variant="danger" class="mt-4">{{ $profile->rejection_reason }}</x-alert>
    @endif

    @if (Route::has('matrimony.photos.index'))
        <div class="mt-4">
            <a href="{{ route('matrimony.photos.index', $profile) }}" class="text-sm font-medium text-navy-700 hover:underline dark:text-navy-300">Manage Photos &rarr;</a>
        </div>
    @endif

    <form method="POST" action="{{ route('matrimony.profiles.update', $profile) }}" class="card card-body mt-6 space-y-8">
        @csrf
        @method('PUT')
        @include('matrimony.profiles.partials.form')
        <div class="flex justify-end"><x-button type="submit">Save Changes</x-button></div>
    </form>

    @if (in_array($profile->status, ['draft', 'rejected']))
        <form method="POST" action="{{ route('matrimony.profiles.submit', $profile) }}" class="card card-body mt-6 space-y-3">
            @csrf
            <h3 class="font-semibold text-slate-900 dark:text-white">Submit for Admin Review</h3>
            @if ($profile->profile_completion < 80)
                <x-alert variant="warning">Complete at least 80% of the profile before you can submit it (currently {{ $profile->profile_completion }}%).</x-alert>
            @endif
            <label class="flex items-start gap-2 text-sm text-slate-600 dark:text-slate-300">
                <input type="checkbox" name="terms_accepted" value="1" class="mt-1 form-checkbox" required>
                <span>I confirm the information provided is accurate, I have the right to create this profile, and I agree to the matrimony section's terms of use.</span>
            </label>
            <div class="flex justify-end">
                <x-button type="submit" :disabled="$profile->profile_completion < 80">Submit for Review</x-button>
            </div>
        </form>
    @endif

    <div class="mt-6 flex items-center justify-between">
        <form method="POST" action="{{ route('matrimony.profiles.toggle-active', $profile) }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-navy-700 hover:underline dark:text-navy-300">
                {{ $profile->is_active ? 'Pause this profile' : 'Reactivate this profile' }}
            </button>
        </form>
        <form method="POST" action="{{ route('matrimony.profiles.destroy', $profile) }}" onsubmit="return confirm('Delete this profile permanently?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-sm font-medium text-red-600 hover:underline">Delete Profile</button>
        </form>
    </div>
</x-layouts::alumni>
