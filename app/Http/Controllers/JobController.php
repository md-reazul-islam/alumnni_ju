<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobPostingRequest;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\SavedJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class JobController extends Controller
{
    public function index(Request $request): View
    {
        $jobs = JobPosting::query()
            ->approved()
            ->with('company')
            ->when($request->filled('type'), fn ($q) => $q->where('employment_type', $request->string('type')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($q2) => $q2->where('title', 'like', "%{$term}%")->orWhere('company_name', 'like', "%{$term}%"));
            })
            ->latest('approved_at')
            ->paginate(12)
            ->withQueryString();

        return view('public.jobs.index', compact('jobs'));
    }

    public function show(Request $request, JobPosting $job): View
    {
        abort_unless($job->status === JobPosting::STATUS_APPROVED, 404);

        $hasApplied = $request->user()
            ? $job->applications()->where('user_id', $request->user()->id)->exists()
            : false;

        $isSaved = $request->user()
            ? $job->savedBy()->where('user_id', $request->user()->id)->exists()
            : false;

        return view('public.jobs.show', compact('job', 'hasApplied', 'isSaved'));
    }

    public function create(): View
    {
        $this->authorize('create', JobPosting::class);

        return view('alumni.jobs.create');
    }

    public function store(StoreJobPostingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['posted_by'] = $request->user()->id;
        $data['slug'] = $this->uniqueSlug($data['title'] . '-' . $data['company_name']);
        $data['status'] = JobPosting::STATUS_PENDING;

        JobPosting::create($data);

        return redirect()->route('jobs.mine')->with('status', 'Your job posting has been submitted for review.');
    }

    public function mine(Request $request): View
    {
        $jobs = $request->user()->jobPostings()->withCount('applications')->latest()->paginate(15);

        return view('alumni.jobs.mine', compact('jobs'));
    }

    public function saved(Request $request): View
    {
        $savedJobs = $request->user()->savedJobs()->with('jobPosting.company')->latest()->paginate(15);

        return view('alumni.jobs.saved', compact('savedJobs'));
    }

    public function apply(Request $request, JobPosting $job): RedirectResponse
    {
        abort_unless($job->isOpen(), 422, 'This job is no longer accepting applications.');

        $data = $request->validate(['cover_letter' => ['nullable', 'string', 'max:3000']]);

        JobApplication::firstOrCreate(
            ['job_posting_id' => $job->id, 'user_id' => $request->user()->id],
            ['cover_letter' => $data['cover_letter'] ?? null, 'applied_at' => now()]
        );

        return back()->with('status', 'Your application has been submitted.');
    }

    public function toggleSave(Request $request, JobPosting $job): RedirectResponse
    {
        $existing = SavedJob::where('job_posting_id', $job->id)->where('user_id', $request->user()->id)->first();

        if ($existing) {
            $existing->delete();

            return back()->with('status', 'Job removed from saved list.');
        }

        SavedJob::create(['job_posting_id' => $job->id, 'user_id' => $request->user()->id]);

        return back()->with('status', 'Job saved.');
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
}
