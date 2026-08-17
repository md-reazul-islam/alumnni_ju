<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreJobPostingRequest;
use App\Models\JobPosting;
use App\Models\Role;
use App\Models\User;
use App\Notifications\JobApproved;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class JobController extends Controller
{
    public function index(Request $request): View
    {
        $jobs = JobPosting::query()
            ->with(['company', 'poster'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%' . $request->string('search') . '%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.jobs.index', compact('jobs'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->hasPermission('manage-jobs'), 403);

        $posters = User::verified()
            ->whereHas('role', fn ($q) => $q->where('slug', Role::ALUMNI))
            ->get()
            ->sortBy(fn ($user) => $user->full_name);

        return view('admin.jobs.create', compact('posters'));
    }

    public function store(StoreJobPostingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['title'] . '-' . $data['company_name']);

        if ($data['status'] === JobPosting::STATUS_APPROVED) {
            $data['approved_by'] = $request->user()->id;
            $data['approved_at'] = now();
        }

        $job = JobPosting::create($data);

        AuditLogger::log('created_job', $job, "Created job posting \"{$job->title}\".");

        if ($job->status === JobPosting::STATUS_APPROVED) {
            Cache::forget('homepage.content');
        }

        return redirect()->route('admin.jobs.index')->with('status', 'Job posting created.');
    }

    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 1;

        while (JobPosting::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }

    public function pending(): View
    {
        $jobs = JobPosting::with(['company', 'poster'])->where('status', JobPosting::STATUS_PENDING)->latest()->paginate(15);

        return view('admin.jobs.pending', compact('jobs'));
    }

    public function approve(Request $request, JobPosting $job): RedirectResponse
    {
        $this->authorize('approve', $job);

        $job->update(['status' => JobPosting::STATUS_APPROVED, 'approved_by' => $request->user()->id, 'approved_at' => now()]);

        $job->poster->notify(new JobApproved($job));

        AuditLogger::log('approved_job', $job, "Approved job posting \"{$job->title}\".");
        Cache::forget('homepage.content');

        return back()->with('status', 'Job posting approved.');
    }

    public function reject(Request $request, JobPosting $job): RedirectResponse
    {
        $this->authorize('approve', $job);

        $job->update(['status' => JobPosting::STATUS_REJECTED]);

        AuditLogger::log('rejected_job', $job, "Rejected job posting \"{$job->title}\".");

        return back()->with('status', 'Job posting rejected.');
    }

    public function destroy(JobPosting $job): RedirectResponse
    {
        $this->authorize('delete', $job);

        $job->delete();

        return back()->with('status', 'Job posting deleted.');
    }
}
