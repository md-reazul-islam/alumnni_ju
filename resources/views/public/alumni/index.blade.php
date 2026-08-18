<x-layouts::app>
  <div>
    <div
        x-data="{
            loading: false,
            search(resetPage = true) {
                this.loading = true;
                const form = document.getElementById('directory-filters');
                const params = new URLSearchParams(new FormData(form));
                if (resetPage) params.delete('page');

                const url = '{{ route('alumni.directory') }}?' + params.toString();
                window.history.replaceState({}, '', url);

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then((r) => r.text())
                    .then((html) => {
                        document.getElementById('directory-results').innerHTML = html;
                        this.loading = false;
                    })
                    .catch(() => { this.loading = false; });
            },
        }"
        x-init="
            document.addEventListener('click', (e) => {
                const link = e.target.closest('#directory-results a[href*=\'page=\']');
                if (!link) return;
                e.preventDefault();
                const url = new URL(link.href);
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then((r) => r.text())
                    .then((html) => {
                        document.getElementById('directory-results').innerHTML = html;
                        window.history.replaceState({}, '', url);
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
            });
        "
        class="section-container py-12"
    >
        <x-breadcrumb :items="[['label' => 'Alumni Directory']]" onDark class="mb-4" />

        <h1 class="text-3xl font-bold text-white">Alumni Directory</h1>
        <p class="mt-1.5 text-navy-200">Search and connect with graduates around the world.</p>

        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-4">
            <form id="directory-filters" @submit.prevent="search()" @change="search()" class="space-y-4 lg:col-span-1">
                <div class="card card-body space-y-4">
                    <div class="relative">
                        <x-icon name="search" class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
                        <input type="text" name="name" value="{{ request('name') }}" placeholder="Search by name"
                               @input.debounce.400ms="search()" class="form-input pl-9">
                    </div>

                    <input type="text" name="student_id" value="{{ request('student_id') }}" placeholder="Student ID"
                           @input.debounce.400ms="search()" class="form-input">

                    <select name="department_id" class="form-select">
                        <option value="">All departments</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected(request('department_id') == $department->id)>{{ $department->name }}</option>
                        @endforeach
                    </select>

                    <select name="degree_id" class="form-select">
                        <option value="">All degrees</option>
                        @foreach ($degrees as $degree)
                            <option value="{{ $degree->id }}" @selected(request('degree_id') == $degree->id)>{{ $degree->abbreviation }}</option>
                        @endforeach
                    </select>

                    <select name="graduation_year" class="form-select">
                        <option value="">All graduation years</option>
                        @foreach ($graduationYears as $year)
                            <option value="{{ $year }}" @selected(request('graduation_year') == $year)>{{ $year }}</option>
                        @endforeach
                    </select>

                    <input type="text" name="batch" value="{{ request('batch') }}" placeholder="Batch"
                           @input.debounce.400ms="search()" class="form-input">

                    <select name="country" class="form-select">
                        <option value="">All countries</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country }}" @selected(request('country') === $country)>{{ $country }}</option>
                        @endforeach
                    </select>

                    <input type="text" name="city" value="{{ request('city') }}" placeholder="City"
                           @input.debounce.400ms="search()" class="form-input">
                    <input type="text" name="organization" value="{{ request('organization') }}" placeholder="Organization"
                           @input.debounce.400ms="search()" class="form-input">
                    <input type="text" name="industry" value="{{ request('industry') }}" placeholder="Industry"
                           @input.debounce.400ms="search()" class="form-input">
                    <input type="text" name="job_title" value="{{ request('job_title') }}" placeholder="Job title"
                           @input.debounce.400ms="search()" class="form-input">
                    <input type="text" name="skill" value="{{ request('skill') }}" placeholder="Skill"
                           @input.debounce.400ms="search()" class="form-input">

                    <select name="sort" class="form-select">
                        <option value="recent_grad" @selected(request('sort', 'recent_grad') === 'recent_grad')>Graduation year (newest)</option>
                        <option value="oldest" @selected(request('sort') === 'oldest')>Graduation year (oldest)</option>
                        <option value="name" @selected(request('sort') === 'name')>Name (A&ndash;Z)</option>
                        <option value="recent" @selected(request('sort') === 'recent')>Recently joined</option>
                    </select>
                </div>
            </form>

            <div class="lg:col-span-3">
                <div x-show="loading" x-cloak class="mb-4 flex items-center gap-2 text-sm text-slate-400">
                    <x-loading size="sm" /> Searching...
                </div>

                <div id="directory-results">
                    @include('public.alumni.partials.results')
                </div>
            </div>
        </div>
    </div>
  </div>
</x-layouts::app>
