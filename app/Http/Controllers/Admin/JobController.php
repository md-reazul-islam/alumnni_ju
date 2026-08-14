<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Notifications\JobApproved;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
