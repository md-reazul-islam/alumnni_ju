<x-layouts::admin :title="'Edit Job'">
    <x-breadcrumb :items="[['label' => 'Jobs', 'url' => route('admin.jobs.index')], ['label' => 'Edit']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Job</h1>

    <form method="POST" action="{{ route('admin.jobs.update', $job) }}" class="card card-body mt-6 space-y-5">
        @csrf
        @method('PUT')

        <x-searchable-select
            label="Posted By"
            name="posted_by"
            required
            placeholder="Search alumni by name…"
            :selected="old('posted_by', $job->posted_by)"
            :options="$posters->map(fn ($user) => ['value' => $user->id, 'label' => $user->full_name])"
        />

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-input label="Job Title" name="title" :value="old('title', $job->title)" required />
            <x-input label="Company Name" name="company_name" :value="old('company_name', $job->company_name)" required />
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-input label="Location" name="location" :value="old('location', $job->location)" />
            <x-select label="Employment Type" name="employment_type" :selected="old('employment_type', $job->employment_type)" required :options="['full_time' => 'Full Time', 'part_time' => 'Part Time', 'internship' => 'Internship', 'remote' => 'Remote', 'contract' => 'Contract', 'freelance' => 'Freelance']" />
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <x-input label="Industry" name="industry" :value="old('industry', $job->industry)" />
            <x-input label="Salary Min" name="salary_min" type="number" :value="old('salary_min', $job->salary_min)" />
            <x-input label="Salary Max" name="salary_max" type="number" :value="old('salary_max', $job->salary_max)" />
        </div>

        <x-textarea label="Job Description" name="description" rows="6" required>{{ old('description', $job->description) }}</x-textarea>
        <x-textarea label="Requirements" name="requirements" rows="4">{{ old('requirements', $job->requirements) }}</x-textarea>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-input label="Application URL" name="application_url" type="url" :value="old('application_url', $job->application_url)" />
            <x-input label="Application Email" name="application_email" type="email" :value="old('application_email', $job->application_email)" />
        </div>

        <div>
            <label class="form-label">Application Deadline</label>
            <input type="text" name="deadline" value="{{ old('deadline', $job->deadline?->format('Y-m-d')) }}" class="form-input flatpickr-deadline" autocomplete="off">
        </div>

        <x-select
            label="Status"
            name="status"
            required
            :selected="old('status', $job->status)"
            :placeholder="null"
            :options="['approved' => 'Approved', 'pending' => 'Pending review', 'rejected' => 'Rejected', 'expired' => 'Expired', 'closed' => 'Closed']"
        />

        <div class="flex justify-end gap-3">
            <x-button :href="route('admin.jobs.index')" variant="secondary">Cancel</x-button>
            <x-button type="submit">Save Changes</x-button>
        </div>
    </form>

    @push('scripts')
    <script>document.addEventListener('DOMContentLoaded', () => flatpickr('.flatpickr-deadline', { dateFormat: 'Y-m-d' }));</script>
    @endpush
</x-layouts::admin>
