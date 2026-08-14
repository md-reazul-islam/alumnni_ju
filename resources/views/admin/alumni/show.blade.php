@php $profile = $alumnus->alumniProfile; @endphp

<x-layouts::admin :title="$alumnus->full_name">
    <x-breadcrumb :items="[['label' => 'Alumni', 'url' => route('admin.alumni.index')], ['label' => $alumnus->full_name]]" class="mb-4" />

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <x-avatar :src="$alumnus->avatar_url" :name="$alumnus->full_name" size="lg" />
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $alumnus->full_name }}</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $alumnus->email }}</p>
                <x-badge :variant="match($alumnus->status) { 'verified' => 'success', 'pending' => 'warning', 'suspended' => 'danger', default => 'neutral' }" class="mt-1">
                    {{ ucfirst($alumnus->status) }}
                </x-badge>
            </div>
        </div>

        <div class="flex gap-2">
            @if ($alumnus->status === 'pending')
                <form method="POST" action="{{ route('admin.alumni.verify', $alumnus) }}">
                    @csrf
                    <button type="submit" class="btn-primary btn-sm">Verify</button>
                </form>
                <form method="POST" action="{{ route('admin.alumni.reject', $alumnus) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Reject application?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Reject'}).then(r=>r.isConfirmed&&this.submit())">
                    @csrf
                    <button type="submit" class="btn-secondary btn-sm">Reject</button>
                </form>
            @elseif ($alumnus->status === 'verified')
                <form method="POST" action="{{ route('admin.alumni.suspend', $alumnus) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Suspend this account?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Suspend'}).then(r=>r.isConfirmed&&this.submit())">
                    @csrf
                    <button type="submit" class="btn-secondary btn-sm">Suspend</button>
                </form>
            @elseif ($alumnus->status === 'suspended')
                <form method="POST" action="{{ route('admin.alumni.restore', $alumnus) }}">
                    @csrf
                    <button type="submit" class="btn-primary btn-sm">Restore</button>
                </form>
            @endif
        </div>
    </div>

    @if ($profile)
        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="card card-body lg:col-span-2">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Academic Information</h2>
                <dl class="mt-3 grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-slate-400">Student ID</dt><dd class="text-slate-700 dark:text-slate-200">{{ $profile->student_id }}</dd></div>
                    <div><dt class="text-slate-400">Department</dt><dd class="text-slate-700 dark:text-slate-200">{{ $profile->department?->name }}</dd></div>
                    <div><dt class="text-slate-400">Program</dt><dd class="text-slate-700 dark:text-slate-200">{{ $profile->program?->name }}</dd></div>
                    <div><dt class="text-slate-400">Degree</dt><dd class="text-slate-700 dark:text-slate-200">{{ $profile->degree?->name }}</dd></div>
                    <div><dt class="text-slate-400">Admission Year</dt><dd class="text-slate-700 dark:text-slate-200">{{ $profile->admission_year }}</dd></div>
                    <div><dt class="text-slate-400">Graduation Year</dt><dd class="text-slate-700 dark:text-slate-200">{{ $profile->graduation_year }}</dd></div>
                    <div><dt class="text-slate-400">Campus</dt><dd class="text-slate-700 dark:text-slate-200">{{ $profile->campus?->name }}</dd></div>
                    <div><dt class="text-slate-400">Batch</dt><dd class="text-slate-700 dark:text-slate-200">{{ $profile->batch }}</dd></div>
                </dl>

                <h2 class="mt-6 text-sm font-semibold text-slate-900 dark:text-white">Professional Information</h2>
                <dl class="mt-3 grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-slate-400">Job Title</dt><dd class="text-slate-700 dark:text-slate-200">{{ $profile->job_title ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400">Organization</dt><dd class="text-slate-700 dark:text-slate-200">{{ $profile->organization ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400">Industry</dt><dd class="text-slate-700 dark:text-slate-200">{{ $profile->industry ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400">Location</dt><dd class="text-slate-700 dark:text-slate-200">{{ collect([$profile->city, $profile->country])->filter()->implode(', ') ?: '—' }}</dd></div>
                </dl>

                @if ($alumnus->rejection_reason)
                    <x-alert variant="warning" class="mt-6">{{ $alumnus->rejection_reason }}</x-alert>
                @endif
            </div>

            <div class="space-y-6">
                <div class="card card-body">
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Profile Completion</h2>
                    <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-navy-800">
                        <div class="h-full rounded-full bg-navy-700" style="width: {{ $profile->profile_completion }}%"></div>
                    </div>
                    <p class="mt-1.5 text-xs text-slate-400">{{ $profile->profile_completion }}% complete</p>
                </div>

                @if ($profile->skills->isNotEmpty())
                    <div class="card card-body">
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Skills</h2>
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach ($profile->skills as $skill)<x-badge variant="neutral">{{ $skill->name }}</x-badge>@endforeach
                        </div>
                    </div>
                @endif

                <a href="{{ route('alumni.profile.show', $alumnus) }}" class="btn-secondary btn-sm w-full justify-center">View Public Profile</a>
            </div>
        </div>
    @else
        <x-alert variant="warning" class="mt-8">This user has no alumni profile record.</x-alert>
    @endif
</x-layouts::admin>
