<x-layouts::alumni :title="'Post a Job'">
    <x-breadcrumb :items="[['label' => 'Career Center', 'url' => route('jobs.index')], ['label' => 'Post a Job']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Post a Job</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Your listing will be reviewed by the alumni office before it goes live.</p>

    <form method="POST" action="{{ route('jobs.store') }}" class="card card-body mt-6 space-y-5">
        @csrf

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-input label="Job Title" name="title" :value="old('title')" required />
            <x-input label="Company Name" name="company_name" :value="old('company_name')" required />
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-input label="Location" name="location" :value="old('location')" />
            <x-select label="Employment Type" name="employment_type" :selected="old('employment_type')" required :options="['full_time' => 'Full Time', 'part_time' => 'Part Time', 'internship' => 'Internship', 'remote' => 'Remote', 'contract' => 'Contract', 'freelance' => 'Freelance']" />
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <x-input label="Industry" name="industry" :value="old('industry')" />
            <x-input label="Salary Min" name="salary_min" type="number" :value="old('salary_min')" />
            <x-input label="Salary Max" name="salary_max" type="number" :value="old('salary_max')" />
        </div>

        <x-textarea label="Job Description" name="description" rows="6" required>{{ old('description') }}</x-textarea>
        <x-textarea label="Requirements" name="requirements" rows="4">{{ old('requirements') }}</x-textarea>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-input label="Application URL" name="application_url" type="url" :value="old('application_url')" />
            <x-input label="Application Email" name="application_email" type="email" :value="old('application_email')" />
        </div>

        <div>
            <label class="form-label">Application Deadline</label>
            <input type="text" name="deadline" value="{{ old('deadline') }}" class="form-input flatpickr-deadline" autocomplete="off">
        </div>

        <div class="flex justify-end">
            <x-button type="submit">Submit for Review</x-button>
        </div>
    </form>

    @push('scripts')
    <script>flatpickr('.flatpickr-deadline', { dateFormat: 'Y-m-d', minDate: 'today' });</script>
    @endpush
</x-layouts::alumni>
