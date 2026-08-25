@php $profile ??= null; @endphp

<div>
    <h3 class="font-semibold text-slate-900 dark:text-white">Who is this profile for?</h3>
    <div class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-select
            label="Relationship to you"
            name="managed_by_relation"
            :options="['self' => 'Myself', 'parent' => 'My child', 'guardian' => 'Someone I am guardian to', 'sibling' => 'My sibling', 'relative' => 'A relative']"
            :selected="old('managed_by_relation', $profile?->managed_by_relation ?? 'self')"
            placeholder="Select"
            required
        />
        <x-select
            label="Gender"
            name="gender"
            :options="['male' => 'Male', 'female' => 'Female']"
            :selected="old('gender', $profile?->gender)"
            placeholder="Select"
            required
        />
    </div>
</div>

<div>
    <h3 class="font-semibold text-slate-900 dark:text-white">Identity</h3>
    <p class="mt-1 text-xs text-slate-400">Full name is kept private and only shown to a connection you've accepted. Display name is shown publicly.</p>
    <div class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-input label="Full Name (private)" name="full_name" :value="old('full_name', $profile?->full_name)" required />
        <x-input label="Display Name (public)" name="display_name" :value="old('display_name', $profile?->display_name)" hint="Leave blank to use your first name." />
    </div>
    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
        <x-input label="Date of Birth" name="date_of_birth" type="date" :value="old('date_of_birth', $profile?->date_of_birth?->format('Y-m-d'))" required hint="Must be 18 or older." />
        <x-input label="Height (cm)" name="height_cm" type="number" min="100" max="250" :value="old('height_cm', $profile?->height_cm)" />
        <x-select
            label="Marital Status"
            name="marital_status"
            :options="['never_married' => 'Never Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed', 'separated' => 'Separated']"
            :selected="old('marital_status', $profile?->marital_status ?? 'never_married')"
            :placeholder="null"
            required
        />
    </div>
</div>

<div>
    <h3 class="font-semibold text-slate-900 dark:text-white">Background</h3>
    <div class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-3">
        <x-input label="Religion" name="religion" :value="old('religion', $profile?->religion)" required />
        <x-input label="Sect / Denomination" name="sect" :value="old('sect', $profile?->sect)" />
        <x-input label="Mother Tongue" name="mother_tongue" :value="old('mother_tongue', $profile?->mother_tongue)" />
    </div>
</div>

<div>
    <h3 class="font-semibold text-slate-900 dark:text-white">Location &amp; Residency</h3>
    <div class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-input label="Nationality (citizenship)" name="nationality" :value="old('nationality', $profile?->nationality)" required placeholder="e.g. Bangladeshi, American" />
        <x-input label="Visa / Residency Status" name="visa_status" :value="old('visa_status', $profile?->visa_status)" placeholder="e.g. US Citizen, Green Card, Bangladeshi Citizen" />
    </div>
    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
        <x-input label="Current Country" name="country" :value="old('country', $profile?->country)" required placeholder="e.g. USA, Bangladesh" />
        <x-input label="State / Region" name="state" :value="old('state', $profile?->state)" />
        <x-input label="City" name="city" :value="old('city', $profile?->city)" placeholder="e.g. Albany, New York, Dhaka" />
    </div>
</div>

<div>
    <h3 class="font-semibold text-slate-900 dark:text-white">Education &amp; Career</h3>
    <div class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-input label="Education Level" name="education_level" :value="old('education_level', $profile?->education_level)" required placeholder="e.g. Bachelor's, Master's, PhD" />
        <x-input label="Occupation" name="occupation" :value="old('occupation', $profile?->occupation)" required />
    </div>
    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-textarea label="Education Details" name="education_details" rows="2">{{ old('education_details', $profile?->education_details) }}</x-textarea>
        <x-textarea label="Occupation Details" name="occupation_details" rows="2">{{ old('occupation_details', $profile?->occupation_details) }}</x-textarea>
    </div>
</div>

<div>
    <h3 class="font-semibold text-slate-900 dark:text-white">About Me (public)</h3>
    <x-textarea label="" name="about_me" rows="4">{{ old('about_me', $profile?->about_me) }}</x-textarea>
</div>

<div>
    <h3 class="font-semibold text-slate-900 dark:text-white">Private Details</h3>
    <p class="mt-1 text-xs text-slate-400">Only visible to you, admin, and a connection you've accepted an interest with.</p>
    <div class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-input label="Income Range" name="income_range" :value="old('income_range', $profile?->income_range)" placeholder="e.g. $60,000 - $80,000 / year" />
        <x-textarea label="Physical Description" name="physical_description" rows="2">{{ old('physical_description', $profile?->physical_description) }}</x-textarea>
    </div>
    <div class="mt-5">
        <x-textarea label="Family Details" name="family_details" rows="3">{{ old('family_details', $profile?->family_details) }}</x-textarea>
    </div>
    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
        <x-input label="Guardian Name" name="guardian_name" :value="old('guardian_name', $profile?->guardian_name)" />
        <x-input label="Guardian Phone" name="guardian_phone" :value="old('guardian_phone', $profile?->guardian_phone)" />
        <x-input label="Guardian Email" name="guardian_email" type="email" :value="old('guardian_email', $profile?->guardian_email)" />
    </div>
    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-input label="Contact Phone" name="contact_phone" :value="old('contact_phone', $profile?->contact_phone)" />
        <x-input label="Contact Email" name="contact_email" type="email" :value="old('contact_email', $profile?->contact_email)" />
    </div>
</div>

<div>
    <h3 class="font-semibold text-slate-900 dark:text-white">Partner Preferences (public)</h3>
    <div class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-3">
        <x-input label="Preferred Age (min)" name="preferred_age_min" type="number" min="18" max="90" :value="old('preferred_age_min', $profile?->preferred_age_min)" />
        <x-input label="Preferred Age (max)" name="preferred_age_max" type="number" min="18" max="90" :value="old('preferred_age_max', $profile?->preferred_age_max)" />
        <x-input label="Preferred Country" name="preferred_country" :value="old('preferred_country', $profile?->preferred_country)" />
    </div>
    <div class="mt-5">
        <x-textarea label="What I'm Looking For" name="preferred_partner_details" rows="3">{{ old('preferred_partner_details', $profile?->preferred_partner_details) }}</x-textarea>
    </div>
</div>

<div>
    <h3 class="font-semibold text-slate-900 dark:text-white">Photo Privacy</h3>
    <x-select
        label="Who can see your photo?"
        name="photo_visibility"
        :options="['private' => 'Private — only visible after an accepted interest', 'public' => 'Public — visible to anyone browsing']"
        :selected="old('photo_visibility', $profile?->photo_visibility ?? 'private')"
        :placeholder="null"
        required
    />
</div>
