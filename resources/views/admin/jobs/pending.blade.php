<x-layouts::admin :title="'Pending Jobs'">
    <x-breadcrumb :items="[['label' => 'Jobs', 'url' => route('admin.jobs.index')], ['label' => 'Pending']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Pending Job Approvals</h1>

    @if ($jobs->isEmpty())
        <x-empty-state icon="clipboard-check" title="No jobs awaiting review" class="mt-8" />
    @else
        <div class="mt-6 space-y-4">
            @foreach ($jobs as $job)
                <div class="card card-body">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $job->title }}</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $job->company_name }} &middot; {{ $job->location }}</p>
                            <p class="mt-1 text-xs text-slate-400">Submitted by {{ $job->poster->full_name }}</p>
                        </div>
                        <div class="flex flex-shrink-0 gap-2">
                            <form method="POST" action="{{ route('admin.jobs.approve', $job) }}">
                                @csrf
                                <button type="submit" class="btn-primary btn-sm">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.jobs.reject', $job) }}">
                                @csrf
                                <button type="submit" class="btn-secondary btn-sm">Reject</button>
                            </form>
                        </div>
                    </div>
                    <p class="mt-3 line-clamp-2 text-sm text-slate-500 dark:text-slate-400">{{ $job->description }}</p>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $jobs->links() }}</div>
    @endif
</x-layouts::admin>
