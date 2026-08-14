<x-guest-layout :title="'Join the Alumni Network'">
    <div
        x-data="{
            step: 1,
            totalSteps: 5,
            selectedInterests: {{ json_encode(old('interests', [])) }},
            photoPreview: null,

            stepFields() {
                return this.$refs['step' + this.step].querySelectorAll('input, select, textarea');
            },

            next() {
                const fields = this.stepFields();
                for (const field of fields) {
                    if (!field.checkValidity()) {
                        field.reportValidity();
                        return;
                    }
                }
                this.step = Math.min(this.step + 1, this.totalSteps);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            prev() {
                this.step = Math.max(this.step - 1, 1);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            previewPhoto(event) {
                const file = event.target.files[0];
                if (file) this.photoPreview = URL.createObjectURL(file);
            },
        }"
        class="w-full max-w-none lg:max-w-2xl"
    >
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Join the Alumni Network</h1>
        <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">
            Already a member? <a href="{{ route('login') }}" class="font-semibold text-navy-700 hover:text-navy-900 dark:text-navy-300">Log in instead</a>
        </p>

        {{-- Step progress --}}
        <ol class="mt-8 grid grid-cols-5 gap-2">
            @foreach (['Personal', 'Academic', 'Professional', 'Interests', 'Review'] as $index => $label)
                <li class="text-center">
                    <div
                        class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-xs font-semibold"
                        :class="step > {{ $index + 1 }} ? 'bg-navy-800 text-white' : (step === {{ $index + 1 }} ? 'bg-gold-500 text-navy-950' : 'bg-slate-100 text-slate-400 dark:bg-navy-800 dark:text-slate-500')"
                    >
                        <template x-if="step > {{ $index + 1 }}"><x-icon name="check" class="h-4 w-4" /></template>
                        <template x-if="step <= {{ $index + 1 }}"><span>{{ $index + 1 }}</span></template>
                    </div>
                    <p class="mt-1.5 hidden text-[11px] font-medium text-slate-500 dark:text-slate-400 sm:block">{{ $label }}</p>
                </li>
            @endforeach
        </ol>

        @if ($errors->any())
            <x-alert variant="danger" class="mt-6">
                Please correct the highlighted errors before continuing.
            </x-alert>
        @endif

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="mt-8">
            @csrf

            {{-- Step 1: Personal Information --}}
            <div x-ref="step1" x-show="step === 1" x-cloak class="space-y-5">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Personal Information</h2>

                <div class="flex items-center gap-4">
                    <template x-if="photoPreview">
                        <img :src="photoPreview" class="h-16 w-16 rounded-full object-cover ring-2 ring-slate-200">
                    </template>
                    <template x-if="!photoPreview">
                        <span class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400 dark:bg-navy-800"><x-icon name="user" class="h-7 w-7" /></span>
                    </template>
                    <div>
                        <label class="btn-secondary btn-sm cursor-pointer">
                            Upload Photo
                            <input type="file" name="photo" accept="image/*" class="hidden" @change="previewPhoto($event)">
                        </label>
                        <p class="form-hint">JPG, PNG or WebP. Max 2MB.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-input label="First name" name="first_name" :value="old('first_name')" required />
                    <x-input label="Last name" name="last_name" :value="old('last_name')" required />
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-input label="Date of birth" name="date_of_birth" type="date" :value="old('date_of_birth')" />
                    <x-select label="Gender" name="gender" :selected="old('gender')" :options="['male' => 'Male', 'female' => 'Female', 'other' => 'Other', 'prefer_not_to_say' => 'Prefer not to say']" placeholder="Select gender" />
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-input label="Email address" name="email" type="email" :value="old('email')" required />
                    <x-input label="Phone number" name="phone" :value="old('phone')" />
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-input label="Country" name="country" :value="old('country')" required />
                    <x-input label="City" name="city" :value="old('city')" />
                </div>

                <x-input label="Address" name="address" :value="old('address')" />

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-input label="Password" name="password" type="password" required />
                    <x-input label="Confirm password" name="password_confirmation" type="password" required />
                </div>

                <div class="flex justify-end pt-2">
                    <x-button type="button" @click="next()">Continue</x-button>
                </div>
            </div>

            {{-- Step 2: Academic Information --}}
            <div x-ref="step2" x-show="step === 2" x-cloak class="space-y-5">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Academic Information</h2>

                <x-input label="Student ID" name="student_id" :value="old('student_id')" required />

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-select label="Department" name="department_id" :selected="old('department_id')" :options="$departments->pluck('name', 'id')" placeholder="Select department" required />
                    <x-select label="Program" name="program_id" :selected="old('program_id')" :options="$departments->flatMap->programs->pluck('name', 'id')" placeholder="Select program" />
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-select label="Degree" name="degree_id" :selected="old('degree_id')" :options="$degrees->pluck('abbreviation', 'id')" placeholder="Select degree" required />
                    <x-input label="Major" name="major" :value="old('major')" />
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-input label="Admission year" name="admission_year" type="number" min="1950" :max="now()->year" :value="old('admission_year')" required />
                    <x-input label="Graduation year" name="graduation_year" type="number" min="1950" :max="now()->year + 10" :value="old('graduation_year')" required />
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-select label="Campus" name="campus_id" :selected="old('campus_id')" :options="$campuses->pluck('name', 'id')" placeholder="Select campus" />
                    <x-input label="Batch" name="batch" :value="old('batch')" placeholder="e.g. 2016-2020" />
                </div>

                <div class="flex justify-between pt-2">
                    <x-button type="button" variant="secondary" @click="prev()">Back</x-button>
                    <x-button type="button" @click="next()">Continue</x-button>
                </div>
            </div>

            {{-- Step 3: Professional Information --}}
            <div x-ref="step3" x-show="step === 3" x-cloak class="space-y-5">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Professional Information</h2>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-input label="Job title" name="job_title" :value="old('job_title')" />
                    <x-input label="Organization" name="organization" :value="old('organization')" />
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-input label="Industry" name="industry" :value="old('industry')" />
                    <x-select label="Employment type" name="employment_type" :selected="old('employment_type')" :options="['full_time' => 'Full Time', 'part_time' => 'Part Time', 'self_employed' => 'Self-Employed', 'internship' => 'Internship', 'unemployed' => 'Unemployed', 'student' => 'Student']" placeholder="Select type" />
                </div>

                <x-input label="Work location" name="work_location" :value="old('work_location')" />

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-input label="LinkedIn URL" name="linkedin_url" type="url" :value="old('linkedin_url')" />
                    <x-input label="Personal website" name="website_url" type="url" :value="old('website_url')" />
                </div>

                <div class="flex justify-between pt-2">
                    <x-button type="button" variant="secondary" @click="prev()">Back</x-button>
                    <x-button type="button" @click="next()">Continue</x-button>
                </div>
            </div>

            {{-- Step 4: Interests --}}
            <div x-ref="step4" x-show="step === 4" x-cloak class="space-y-5">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Areas of Interest</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Select the topics you'd like to engage with in the alumni community.</p>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($interests as $interest)
                        <label
                            class="flex cursor-pointer items-center gap-2 rounded-lg border px-3 py-2.5 text-sm transition"
                            :class="selectedInterests.includes({{ $interest->id }}) ? 'border-navy-600 bg-navy-50 text-navy-800 dark:bg-navy-800 dark:text-white' : 'border-slate-200 text-slate-600 hover:border-slate-300 dark:border-navy-700 dark:text-slate-300'"
                        >
                            <input
                                type="checkbox"
                                name="interests[]"
                                value="{{ $interest->id }}"
                                class="sr-only"
                                x-model.number="selectedInterests"
                            >
                            {{ $interest->name }}
                        </label>
                    @endforeach
                </div>

                <div class="flex justify-between pt-2">
                    <x-button type="button" variant="secondary" @click="prev()">Back</x-button>
                    <x-button type="button" @click="next()">Continue</x-button>
                </div>
            </div>

            {{-- Step 5: Review & Confirm --}}
            <div x-ref="step5" x-show="step === 5" x-cloak class="space-y-5">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Review &amp; Verification</h2>

                <x-alert variant="info">
                    After you submit, we'll send a verification email to confirm your address. Our alumni office will
                    then review your academic details — this typically takes 1&ndash;2 business days.
                </x-alert>

                <label class="flex items-start gap-2.5 text-sm text-slate-600 dark:text-slate-300">
                    <input type="checkbox" name="terms" value="1" required class="mt-0.5 rounded border-slate-300 text-navy-700 focus:ring-navy-500">
                    <span>
                        I agree to the <a href="{{ route('terms') }}" target="_blank" class="font-medium text-navy-700 hover:underline dark:text-navy-300">Terms &amp; Conditions</a>
                        and <a href="{{ route('privacy') }}" target="_blank" class="font-medium text-navy-700 hover:underline dark:text-navy-300">Privacy Policy</a>.
                    </span>
                </label>
                @error('terms')
                    <p class="form-error">{{ $message }}</p>
                @enderror

                <div class="flex justify-between pt-2">
                    <x-button type="button" variant="secondary" @click="prev()">Back</x-button>
                    <x-button type="submit">Submit Registration</x-button>
                </div>
            </div>
        </form>
    </div>
</x-guest-layout>
