<x-layouts::admin :title="$profile->display_name">
    <x-breadcrumb :items="[['label' => 'Matrimony'], ['label' => 'Pending Profiles', 'url' => route('admin.matrimony.profiles.pending')], ['label' => $profile->display_name]]" class="mb-4" />

    <div x-data="{ rejecting: false, suspending: false }">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <x-badge :variant="match($profile->status) { 'approved' => 'success', 'rejected', 'suspended' => 'danger', default => 'warning' }">
                        {{ ucfirst($profile->status) }}
                    </x-badge>
                    @if ($profile->is_verified)
                        <x-badge variant="info">Verified</x-badge>
                    @endif
                </div>
                <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $profile->full_name }} ({{ $profile->display_name }})</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    {{ $profile->age }} yrs &middot; {{ ucfirst($profile->gender) }} &middot; Managed by {{ $profile->creator->full_name }} ({{ $profile->creator->email }}) as {{ str_replace('_', ' ', $profile->managed_by_relation) }}
                </p>
            </div>

            <div class="flex flex-shrink-0 flex-wrap gap-2">
                @if ($profile->status === 'pending')
                    <form method="POST" action="{{ route('admin.matrimony.profiles.approve', $profile) }}">
                        @csrf
                        <button type="submit" class="btn-primary btn-sm">Approve</button>
                    </form>
                    <button type="button" @click="rejecting = !rejecting" class="btn-secondary btn-sm">Reject</button>
                @endif
                @if ($profile->status === 'approved')
                    <button type="button" @click="suspending = !suspending" class="btn-secondary btn-sm">Suspend</button>
                @endif
                <form method="POST" action="{{ route('admin.matrimony.profiles.verify', $profile) }}">
                    @csrf
                    <button type="submit" class="btn-secondary btn-sm">{{ $profile->is_verified ? 'Remove Verification' : 'Mark Verified' }}</button>
                </form>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.matrimony.profiles.reject', $profile) }}" x-show="rejecting" x-cloak class="card card-body mt-4 space-y-3">
            @csrf
            <x-textarea label="Reason for rejection" name="rejection_reason" rows="3" required />
            <div class="flex justify-end"><x-button type="submit" variant="danger" size="sm">Confirm Rejection</x-button></div>
        </form>

        <form method="POST" action="{{ route('admin.matrimony.profiles.suspend', $profile) }}" x-show="suspending" x-cloak class="card card-body mt-4 space-y-3">
            @csrf
            <x-textarea label="Reason for suspension" name="rejection_reason" rows="3" required />
            <div class="flex justify-end"><x-button type="submit" variant="danger" size="sm">Confirm Suspension</x-button></div>
        </form>

        @if ($profile->rejection_reason)
            <x-alert variant="danger" class="mt-4">{{ $profile->rejection_reason }}</x-alert>
        @endif

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                @if ($profile->photos->isNotEmpty())
                    <div class="grid grid-cols-3 gap-3 sm:grid-cols-4">
                        @foreach ($profile->photos as $photo)
                            <img src="{{ asset('storage/' . $photo->path) }}" class="aspect-square w-full rounded-lg object-cover">
                        @endforeach
                    </div>
                @endif

                <div class="card card-body">
                    <h2 class="font-semibold text-slate-900 dark:text-white">About</h2>
                    <p class="mt-2 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $profile->about_me ?: 'Not provided.' }}</p>
                </div>

                <div class="card card-body">
                    <h2 class="font-semibold text-slate-900 dark:text-white">Background</h2>
                    <dl class="mt-2 grid grid-cols-2 gap-3 text-sm sm:grid-cols-3">
                        <div><dt class="text-slate-400">Religion</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->religion }}{{ $profile->sect ? ' ('.$profile->sect.')' : '' }}</dd></div>
                        <div><dt class="text-slate-400">Mother Tongue</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->mother_tongue ?: '—' }}</dd></div>
                        <div><dt class="text-slate-400">Marital Status</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ str_replace('_', ' ', $profile->marital_status) }}</dd></div>
                        <div><dt class="text-slate-400">Height</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->height_cm ? $profile->height_cm.' cm' : '—' }}</dd></div>
                        <div><dt class="text-slate-400">Nationality</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->nationality }}</dd></div>
                        <div><dt class="text-slate-400">Visa Status</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->visa_status ?: '—' }}</dd></div>
                        <div><dt class="text-slate-400">Location</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->city ? $profile->city.', ' : '' }}{{ $profile->state ? $profile->state.', ' : '' }}{{ $profile->country }}</dd></div>
                        <div><dt class="text-slate-400">Education</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->education_level }}</dd></div>
                        <div><dt class="text-slate-400">Occupation</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->occupation }}</dd></div>
                    </dl>
                </div>

                <div class="card card-body">
                    <h2 class="font-semibold text-slate-900 dark:text-white">Private / Contact Details</h2>
                    <dl class="mt-2 grid grid-cols-2 gap-3 text-sm sm:grid-cols-3">
                        <div><dt class="text-slate-400">Contact Phone</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->contact_phone ?: '—' }}</dd></div>
                        <div><dt class="text-slate-400">Contact Email</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->contact_email ?: '—' }}</dd></div>
                        <div><dt class="text-slate-400">Income Range</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->income_range ?: '—' }}</dd></div>
                        <div><dt class="text-slate-400">Guardian</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->guardian_name ?: '—' }} {{ $profile->guardian_phone ? '('.$profile->guardian_phone.')' : '' }}</dd></div>
                    </dl>
                    @if ($profile->family_details)
                        <p class="mt-3 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $profile->family_details }}</p>
                    @endif
                </div>
            </div>

            <div class="space-y-4">
                <div class="card card-body">
                    <p class="text-sm text-slate-400">Profile Completion</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $profile->profile_completion }}%</p>
                </div>
                <div class="card card-body">
                    <p class="text-sm text-slate-400">Profile Views</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $profile->views_count }}</p>
                </div>
                <div class="card card-body">
                    <p class="text-sm text-slate-400">Photo Visibility</p>
                    <p class="font-medium text-slate-900 dark:text-white">{{ ucfirst($profile->photo_visibility) }}</p>
                </div>
                @if ($profile->reviewer)
                    <div class="card card-body">
                        <p class="text-sm text-slate-400">Reviewed By</p>
                        <p class="font-medium text-slate-900 dark:text-white">{{ $profile->reviewer->full_name }}</p>
                        <p class="text-xs text-slate-400">{{ $profile->reviewed_at?->format('M j, Y') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts::admin>
