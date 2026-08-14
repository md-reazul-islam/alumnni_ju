<x-layouts::alumni :title="'Edit Profile'">
    <div x-data="{ tab: 'about' }">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Profile</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Keep your alumni profile up to date so classmates can find and recognize you.</p>
            </div>
            <a href="{{ route('alumni.profile.show', auth()->user()) }}" class="text-sm font-medium text-navy-700 hover:text-navy-900 dark:text-navy-300">Preview profile &rarr;</a>
        </div>

        @if (session('status'))
            <x-alert variant="success" class="mt-6">{{ session('status') }}</x-alert>
        @endif

        {{-- Tabs --}}
        <div class="mt-6 flex gap-1 overflow-x-auto border-b border-slate-200 dark:border-navy-800">
            @foreach (['about' => 'About', 'career' => 'Career', 'education' => 'Education', 'credentials' => 'Credentials', 'skills' => 'Skills & Interests', 'privacy' => 'Privacy'] as $key => $label)
                <button
                    @click="tab = '{{ $key }}'"
                    :class="tab === '{{ $key }}' ? 'border-navy-700 text-navy-800 dark:text-white' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400'"
                    class="flex-shrink-0 border-b-2 px-4 py-2.5 text-sm font-medium"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- About --}}
        <div x-show="tab === 'about'" x-cloak class="mt-6">
            <form method="POST" action="{{ route('alumni.profile.update') }}" enctype="multipart/form-data" class="card card-body space-y-5">
                @csrf @method('PATCH')

                <div class="flex flex-wrap items-center gap-6">
                    <div>
                        <p class="form-label">Profile photo</p>
                        <div class="flex items-center gap-3">
                            <x-avatar :src="auth()->user()->avatar_url" :name="auth()->user()->full_name" size="lg" />
                            <label class="btn-secondary btn-sm cursor-pointer">
                                Change
                                <input type="file" name="avatar" accept="image/*" class="hidden" onchange="this.form.requestSubmit ? null : null">
                            </label>
                        </div>
                    </div>
                    <div>
                        <p class="form-label">Cover image</p>
                        <label class="btn-secondary btn-sm cursor-pointer">
                            Upload cover
                            <input type="file" name="cover_image" accept="image/*" class="hidden">
                        </label>
                    </div>
                </div>

                <x-textarea label="Bio" name="bio" rows="4" placeholder="Tell fellow alumni about yourself...">{{ old('bio', $profile->bio) }}</x-textarea>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-input label="Country" name="country" :value="old('country', $profile->country)" />
                    <x-input label="City" name="city" :value="old('city', $profile->city)" />
                </div>
                <x-input label="Address" name="address" :value="old('address', $profile->address)" />

                <div class="flex justify-end">
                    <x-button type="submit">Save Changes</x-button>
                </div>
            </form>
        </div>

        {{-- Career --}}
        <div x-show="tab === 'career'" x-cloak class="mt-6 space-y-6">
            <form method="POST" action="{{ route('alumni.profile.update') }}" class="card card-body space-y-5">
                @csrf @method('PATCH')
                <input type="hidden" name="profile_visibility" value="{{ $profile->profile_visibility }}">

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-input label="Current job title" name="job_title" :value="old('job_title', $profile->job_title)" />
                    <x-input label="Organization" name="organization" :value="old('organization', $profile->organization)" />
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-input label="Industry" name="industry" :value="old('industry', $profile->industry)" />
                    <x-select label="Employment type" name="employment_type" :selected="old('employment_type', $profile->employment_type)" :options="['full_time' => 'Full Time', 'part_time' => 'Part Time', 'self_employed' => 'Self-Employed', 'internship' => 'Internship', 'unemployed' => 'Unemployed', 'student' => 'Student']" placeholder="Select type" />
                </div>
                <x-input label="Work location" name="work_location" :value="old('work_location', $profile->work_location)" />
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-input label="LinkedIn URL" name="linkedin_url" type="url" :value="old('linkedin_url', $profile->linkedin_url)" />
                    <x-input label="Personal website" name="website_url" type="url" :value="old('website_url', $profile->website_url)" />
                </div>

                <div class="flex justify-end">
                    <x-button type="submit">Save Changes</x-button>
                </div>
            </form>

            <x-profile-item-manager
                type="employment"
                title="Employment History"
                :items="$profile->employments"
                :fields="[
                    ['name' => 'job_title', 'label' => 'Job Title', 'required' => true],
                    ['name' => 'company_name', 'label' => 'Company'],
                    ['name' => 'employment_type', 'label' => 'Type', 'type' => 'select', 'options' => ['full_time' => 'Full Time', 'part_time' => 'Part Time', 'internship' => 'Internship', 'contract' => 'Contract', 'freelance' => 'Freelance'], 'required' => true],
                    ['name' => 'location', 'label' => 'Location'],
                    ['name' => 'start_date', 'label' => 'Start Date', 'type' => 'date'],
                    ['name' => 'end_date', 'label' => 'End Date', 'type' => 'date'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                ]"
                :primary-field="'job_title'"
                :secondary-field="'company_name'"
            />
        </div>

        {{-- Education --}}
        <div x-show="tab === 'education'" x-cloak class="mt-6">
            <x-profile-item-manager
                type="education"
                title="Education"
                :items="$profile->educations"
                :fields="[
                    ['name' => 'institution', 'label' => 'Institution', 'required' => true],
                    ['name' => 'degree', 'label' => 'Degree'],
                    ['name' => 'field_of_study', 'label' => 'Field of Study'],
                    ['name' => 'start_year', 'label' => 'Start Year', 'type' => 'number'],
                    ['name' => 'end_year', 'label' => 'End Year', 'type' => 'number'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                ]"
                :primary-field="'institution'"
                :secondary-field="'degree'"
            />
        </div>

        {{-- Credentials: Achievements, Certifications, Publications, Projects --}}
        <div x-show="tab === 'credentials'" x-cloak class="mt-6 space-y-6">
            <x-profile-item-manager
                type="achievement" title="Achievements" :items="$profile->achievements"
                :fields="[['name' => 'title', 'label' => 'Title', 'required' => true], ['name' => 'achieved_on', 'label' => 'Date', 'type' => 'date'], ['name' => 'description', 'label' => 'Description', 'type' => 'textarea']]"
                primary-field="title" secondary-field="description"
            />
            <x-profile-item-manager
                type="certification" title="Certifications" :items="$profile->certifications"
                :fields="[['name' => 'name', 'label' => 'Name', 'required' => true], ['name' => 'issuing_organization', 'label' => 'Issuing Organization'], ['name' => 'issue_date', 'label' => 'Issue Date', 'type' => 'date'], ['name' => 'expiry_date', 'label' => 'Expiry Date', 'type' => 'date'], ['name' => 'credential_url', 'label' => 'Credential URL', 'type' => 'url']]"
                primary-field="name" secondary-field="issuing_organization"
            />
            <x-profile-item-manager
                type="publication" title="Publications" :items="$profile->publications"
                :fields="[['name' => 'title', 'label' => 'Title', 'required' => true], ['name' => 'publisher', 'label' => 'Publisher'], ['name' => 'published_on', 'label' => 'Published On', 'type' => 'date'], ['name' => 'url', 'label' => 'URL', 'type' => 'url']]"
                primary-field="title" secondary-field="publisher"
            />
            <x-profile-item-manager
                type="project" title="Projects" :items="$profile->projects"
                :fields="[['name' => 'title', 'label' => 'Title', 'required' => true], ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'], ['name' => 'url', 'label' => 'URL', 'type' => 'url']]"
                primary-field="title" secondary-field="description"
            />
        </div>

        {{-- Skills & Interests --}}
        <div x-show="tab === 'skills'" x-cloak class="mt-6">
            <form method="POST" action="{{ route('alumni.profile.update') }}" class="card card-body space-y-5">
                @csrf @method('PATCH')
                <input type="hidden" name="profile_visibility" value="{{ $profile->profile_visibility }}">

                <div>
                    <label class="form-label">Skills</label>
                    <input type="text" name="skills" value="{{ $profile->skills->pluck('name')->implode(', ') }}" class="form-input" placeholder="e.g. Laravel, Project Management, Data Analysis">
                    <p class="form-hint">Separate skills with commas.</p>
                </div>

                <div>
                    <p class="form-label">Interests</p>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @php $myInterestIds = $profile->interests->pluck('id')->all(); @endphp
                        @foreach ($interests as $interest)
                            <label class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-navy-700">
                                <input type="checkbox" name="interests[]" value="{{ $interest->id }}" @checked(in_array($interest->id, $myInterestIds)) class="rounded border-slate-300 text-navy-700 focus:ring-navy-500">
                                {{ $interest->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end">
                    <x-button type="submit">Save Changes</x-button>
                </div>
            </form>
        </div>

        {{-- Privacy --}}
        <div x-show="tab === 'privacy'" x-cloak class="mt-6">
            <form method="POST" action="{{ route('alumni.profile.update') }}" class="card card-body space-y-5">
                @csrf @method('PATCH')

                <div>
                    <p class="form-label">Profile Visibility</p>
                    <div class="space-y-2">
                        @foreach (['public' => ['Public', 'Visible to anyone, including guests browsing the public directory.'], 'alumni' => ['Alumni Only', 'Visible only to verified, logged-in alumni.'], 'private' => ['Private', 'Visible only to your accepted connections.']] as $value => [$label, $description])
                            <label class="flex items-start gap-3 rounded-lg border border-slate-200 p-3 dark:border-navy-700">
                                <input type="radio" name="profile_visibility" value="{{ $value }}" @checked(old('profile_visibility', $profile->profile_visibility) === $value) class="mt-1 border-slate-300 text-navy-700 focus:ring-navy-500" required>
                                <span>
                                    <span class="block text-sm font-medium text-slate-800 dark:text-slate-100">{{ $label }}</span>
                                    <span class="block text-xs text-slate-500 dark:text-slate-400">{{ $description }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end">
                    <x-button type="submit">Save Changes</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::alumni>
