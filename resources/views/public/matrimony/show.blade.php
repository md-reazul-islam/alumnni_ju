@php
    $viewer = auth()->user();
    $fullyVisible = $profile->isFullyVisibleTo($viewer);
    $photoVisible = $profile->photoVisibleTo($viewer);
@endphp

<x-layouts::app>
    <div>
      <div class="section-container py-8">
        <x-breadcrumb :items="[['label' => 'Matrimony', 'url' => route('matrimony.search')], ['label' => $profile->display_name]]" onDark class="mb-4" />

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="card overflow-hidden">
                    <div class="flex aspect-[16/9] items-center justify-center bg-slate-100 dark:bg-navy-800">
                        @if ($photoVisible && $profile->primary_photo)
                            <img src="{{ asset('storage/' . $profile->primary_photo->path) }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex flex-col items-center gap-2 text-slate-400">
                                <x-icon name="user" class="h-20 w-20" />
                                @unless ($photoVisible)
                                    <p class="text-sm">Photo visible after an accepted interest</p>
                                @endunless
                            </div>
                        @endif
                    </div>

                    @if ($photoVisible && $profile->photos->count() > 1)
                        <div class="grid grid-cols-4 gap-2 p-3">
                            @foreach ($profile->photos as $photo)
                                <img src="{{ asset('storage/' . $photo->path) }}" class="aspect-square w-full rounded-lg object-cover">
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="card card-body mt-6">
                    <h2 class="font-semibold text-slate-900 dark:text-white">About</h2>
                    <p class="mt-2 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $profile->about_me ?: 'No bio provided.' }}</p>
                </div>

                <div class="card card-body mt-6">
                    <h2 class="font-semibold text-slate-900 dark:text-white">Background</h2>
                    <dl class="mt-3 grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
                        <div><dt class="text-slate-400">Religion</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->religion }}{{ $profile->sect ? ' ('.$profile->sect.')' : '' }}</dd></div>
                        <div><dt class="text-slate-400">Marital Status</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ str_replace('_', ' ', ucfirst($profile->marital_status)) }}</dd></div>
                        <div><dt class="text-slate-400">Height</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->height_cm ? $profile->height_cm.' cm' : '—' }}</dd></div>
                        <div><dt class="text-slate-400">Mother Tongue</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->mother_tongue ?: '—' }}</dd></div>
                        <div><dt class="text-slate-400">Nationality</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->nationality }}</dd></div>
                        <div><dt class="text-slate-400">Visa / Residency</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->visa_status ?: '—' }}</dd></div>
                        <div><dt class="text-slate-400">Education</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->education_level }}</dd></div>
                        <div><dt class="text-slate-400">Occupation</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->occupation }}</dd></div>
                    </dl>
                    @if ($profile->education_details || $profile->occupation_details)
                        <p class="mt-3 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $profile->education_details }} {{ $profile->occupation_details }}</p>
                    @endif
                </div>

                <div class="card card-body mt-6">
                    <h2 class="font-semibold text-slate-900 dark:text-white">Partner Preferences</h2>
                    <dl class="mt-3 grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
                        <div><dt class="text-slate-400">Age Range</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->preferred_age_min ?? '—' }} - {{ $profile->preferred_age_max ?? '—' }}</dd></div>
                        <div><dt class="text-slate-400">Country</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->preferred_country ?: 'Open' }}</dd></div>
                    </dl>
                    @if ($profile->preferred_partner_details)
                        <p class="mt-3 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $profile->preferred_partner_details }}</p>
                    @endif
                </div>

                <div class="card card-body mt-6">
                    <h2 class="font-semibold text-slate-900 dark:text-white">Private Details</h2>
                    @if ($fullyVisible)
                        <dl class="mt-3 grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
                            <div><dt class="text-slate-400">Full Name</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->full_name }}</dd></div>
                            <div><dt class="text-slate-400">Contact Phone</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->contact_phone ?: '—' }}</dd></div>
                            <div><dt class="text-slate-400">Contact Email</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->contact_email ?: '—' }}</dd></div>
                            <div><dt class="text-slate-400">Income Range</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->income_range ?: '—' }}</dd></div>
                        </dl>
                        @if ($profile->family_details)
                            <p class="mt-3 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $profile->family_details }}</p>
                        @endif
                    @else
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Full name, contact details, and family background are visible once {{ $profile->display_name }} accepts your interest request.</p>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <div class="card card-body text-center">
                    <div class="flex items-center justify-center gap-2">
                        <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $profile->display_name }}, {{ $profile->age }}</p>
                        @if ($profile->is_verified)
                            <x-icon name="badge-check" class="h-5 w-5 text-navy-600 dark:text-navy-300" />
                        @endif
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $profile->city ? $profile->city . ', ' : '' }}{{ $profile->state ? $profile->state . ', ' : '' }}{{ $profile->country }}</p>

                    @auth
                        @if ($fullyVisible)
                            <x-badge variant="success" class="mt-4">Connected</x-badge>
                        @elseif (Route::has('matrimony.interests.store'))
                            <form method="POST" action="{{ route('matrimony.interests.store', $profile) }}" class="mt-4 space-y-2">
                                @csrf
                                <textarea name="message" rows="2" class="form-textarea" placeholder="Optional introduction message"></textarea>
                                <x-button type="submit" class="w-full">Send Interest</x-button>
                            </form>
                        @else
                            <x-badge variant="info" class="mt-4">Introductions opening soon</x-badge>
                        @endif
                    @else
                        <x-button :href="route('login')" variant="secondary" class="mt-4 w-full">Login to Connect</x-button>
                    @endauth

                    @auth
                        @if ($viewer->id !== $profile->created_by && Route::has('matrimony.favorites.store'))
                            <form method="POST" action="{{ route($isFavorited ? 'matrimony.favorites.destroy' : 'matrimony.favorites.store', $profile) }}" class="mt-2">
                                @csrf
                                @if ($isFavorited) @method('DELETE') @endif
                                <button type="submit" class="w-full text-sm font-medium text-navy-700 hover:underline dark:text-navy-300">
                                    {{ $isFavorited ? 'Remove from Favorites' : 'Save to Favorites' }}
                                </button>
                            </form>
                        @endif
                        @if ($viewer->id !== $profile->created_by && Route::has('matrimony.blocks.store'))
                            <form method="POST" action="{{ route('matrimony.blocks.store', $profile->creator) }}" class="mt-2" onsubmit="return confirm('Block this member? You will no longer see each other on Matrimony.')">
                                @csrf
                                <button type="submit" class="w-full text-sm font-medium text-red-600 hover:underline">Block This Member</button>
                            </form>
                        @endif
                    @endauth
                </div>

                <div class="card card-body">
                    <p class="text-sm text-slate-400">Managed By</p>
                    <p class="font-medium text-slate-900 dark:text-white">{{ str_replace('_', ' ', ucfirst($profile->managed_by_relation)) }}</p>
                </div>
            </div>
        </div>
      </div>
    </div>
</x-layouts::app>
