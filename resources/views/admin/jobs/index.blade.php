<x-layouts::admin :title="'Jobs'">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">All Jobs</h1>
        <x-button :href="route('admin.jobs.pending')" variant="secondary" size="sm">View Pending</x-button>
    </div>

    <form method="GET" class="mt-6 flex flex-col gap-3 sm:flex-row">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search jobs" class="form-input sm:max-w-xs">
        <select name="status" class="form-select sm:max-w-xs">
            <option value="">All statuses</option>
            @foreach (['pending', 'approved', 'rejected', 'expired', 'closed'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <x-button type="submit" variant="secondary">Filter</x-button>
    </form>

    @if ($jobs->isEmpty())
        <x-empty-state icon="briefcase" title="No jobs found" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Title</th><th>Company</th><th>Posted By</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @foreach ($jobs as $job)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $job->title }}</td>
                        <td>{{ $job->displayCompanyName() }}</td>
                        <td>{{ $job->poster->full_name }}</td>
                        <td><x-badge :variant="match($job->status) { 'approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger', default => 'neutral' }">{{ ucfirst($job->status) }}</x-badge></td>
                        <td>
                            <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Delete this job?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Delete'}).then(r=>r.isConfirmed&&this.submit())">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-600"><x-icon name="trash-2" class="h-4 w-4" /></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $jobs->links() }}</div>
    @endif
</x-layouts::admin>
