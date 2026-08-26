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
                    <div class="mx-auto flex aspect-square w-full max-w-xs items-center justify-center bg-slate-100 dark:bg-navy-800 sm:max-w-sm">
                        @if ($photoVisible && $profile->primary_photo)
                            <img src="{{ asset('storage/' . $profile->primary_photo->path) }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex flex-col items-center gap-2 px-4 text-center text-slate-400">
                                <x-icon name="user" class="h-14 w-14" />
                                @unless ($photoVisible)
                                    <p class="text-xs">Photo visible after an accepted interest</p>
                                @endunless
                            </div>
                        @endif
                    </div>

                    @if ($photoVisible && $profile->photos->count() > 1)
                        <div class="grid grid-cols-6 gap-1.5 p-2.5">
                            @foreach ($profile->photos as $photo)
                                <img src="{{ asset('storage/' . $photo->path) }}" class="aspect-square w-full rounded-md object-cover">
                            @endforeach
                        </div>
                    @endif
                </div>

                @php
                    $sections = [
                        ['key' => 'about', 'icon' => 'file-text', 'label' => 'About'],
                        ['key' => 'background', 'icon' => 'info', 'label' => 'Background'],
                        ['key' => 'preferences', 'icon' => 'heart', 'label' => 'Partner Preferences'],
                        ['key' => 'private', 'icon' => 'lock', 'label' => 'Private Details'],
                    ];
                @endphp

                @foreach ($sections as $section)
                    <div class="card mt-4" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="flex w-full items-center justify-between p-4 text-left">
                            <span class="flex items-center gap-2 font-semibold text-slate-900 dark:text-white">
                                <x-icon :name="$section['icon']" class="h-4 w-4 text-navy-600 dark:text-navy-300" />
                                {{ $section['label'] }}
                            </span>
                            <span :class="open ? 'rotate-180' : ''" class="flex-shrink-0 text-slate-400 transition-transform">
                                <x-icon name="chevron-down" class="h-4 w-4" />
                            </span>
                        </button>

                        <div x-show="open" x-transition x-cloak class="border-t border-slate-100 p-4 dark:border-navy-800">
                            @if ($section['key'] === 'about')
                                <p class="whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $profile->about_me ?: 'No bio provided.' }}</p>
                            @elseif ($section['key'] === 'background')
                                <dl class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
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
                            @elseif ($section['key'] === 'preferences')
                                <dl class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
                                    <div><dt class="text-slate-400">Age Range</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->preferred_age_min ?? '—' }} - {{ $profile->preferred_age_max ?? '—' }}</dd></div>
                                    <div><dt class="text-slate-400">Country</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->preferred_country ?: 'Open' }}</dd></div>
                                </dl>
                                @if ($profile->preferred_partner_details)
                                    <p class="mt-3 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $profile->preferred_partner_details }}</p>
                                @endif
                            @elseif ($section['key'] === 'private')
                                @if ($fullyVisible)
                                    <dl class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
                                        <div><dt class="text-slate-400">Full Name</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->full_name }}</dd></div>
                                        <div><dt class="text-slate-400">Contact Phone</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->contact_phone ?: '—' }}</dd></div>
                                        <div><dt class="text-slate-400">Contact Email</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->contact_email ?: '—' }}</dd></div>
                                        <div><dt class="text-slate-400">Income Range</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->income_range ?: '—' }}</dd></div>
                                    </dl>
                                    @if ($profile->family_details)
                                        <p class="mt-3 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $profile->family_details }}</p>
                                    @endif
                                @else
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Full name, contact details, and family background are visible once {{ $profile->display_name }} accepts your interest request.</p>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
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
                        @if ($viewer->id !== $profile->created_by)
                            <form method="POST" action="{{ route('reports.store', ['matrimony_profile', $profile->id]) }}" class="mt-2" onsubmit="event.preventDefault(); Swal.fire({title:'Report this profile',input:'text',inputLabel:'Reason',inputPlaceholder:'Why are you reporting this profile?',showCancelButton:true,confirmButtonText:'Submit'}).then(r=>{ if(r.isConfirmed && r.value){ this.querySelector('[name=reason]').value = r.value; this.submit(); } })">
                                @csrf
                                <input type="hidden" name="reason" value="">
                                <button type="submit" class="w-full text-sm font-medium text-slate-400 hover:text-red-600">Report This Profile</button>
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
