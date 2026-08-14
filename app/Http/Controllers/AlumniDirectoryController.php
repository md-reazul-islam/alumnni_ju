<?php

namespace App\Http\Controllers;

use App\Models\AlumniProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AlumniDirectoryController extends Controller
{
    public function index(Request $request): View
    {
        $profiles = $this->filteredQuery($request)->paginate(24)->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            return view('public.alumni.partials.results', compact('profiles'));
        }

        $filterOptions = Cache::remember('directory.filter-options', now()->addHour(), function () {
            return [
                'departments' => \App\Models\Department::orderBy('name')->get(),
                'degrees' => \App\Models\Degree::orderBy('name')->get(),
                'countries' => AlumniProfile::whereNotNull('country')->distinct()->orderBy('country')->pluck('country'),
                'graduationYears' => AlumniProfile::whereNotNull('graduation_year')->distinct()->orderByDesc('graduation_year')->pluck('graduation_year'),
            ];
        });

        return view('public.alumni.index', compact('profiles') + $filterOptions);
    }

    public function show(Request $request, User $user): View
    {
        abort_unless($user->alumniProfile, 404);

        $this->authorize('view', $user->alumniProfile);

        $profile = $user->alumniProfile->load([
            'department', 'program', 'degree', 'campus', 'skills', 'interests',
            'educations', 'employments', 'achievements', 'certifications', 'publications', 'projects',
        ]);

        if (! $request->user() || $request->user()->id !== $user->id) {
            $profile->increment('views');
        }

        return view('public.alumni.show', compact('profile', 'user'));
    }

    protected function filteredQuery(Request $request)
    {
        $query = AlumniProfile::query()
            ->with(['user', 'department', 'degree'])
            ->whereNotNull('verified_at')
            ->when(! $request->user(), fn ($q) => $q->publiclyVisible())
            ->when($request->user() && ! $request->user()->isAdminStaff(), fn ($q) => $q->visibleToAlumni());

        $query
            ->when($request->filled('name'), function ($q) use ($request) {
                $term = $request->string('name');
                $q->whereHas('user', fn ($u) => $u->where(fn ($w) => $w
                    ->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                ));
            })
            ->when($request->filled('student_id'), fn ($q) => $q->where('student_id', 'like', '%' . $request->string('student_id') . '%'))
            ->when($request->filled('graduation_year'), fn ($q) => $q->where('graduation_year', $request->integer('graduation_year')))
            ->when($request->filled('department_id'), fn ($q) => $q->where('department_id', $request->integer('department_id')))
            ->when($request->filled('program_id'), fn ($q) => $q->where('program_id', $request->integer('program_id')))
            ->when($request->filled('degree_id'), fn ($q) => $q->where('degree_id', $request->integer('degree_id')))
            ->when($request->filled('batch'), fn ($q) => $q->where('batch', 'like', '%' . $request->string('batch') . '%'))
            ->when($request->filled('country'), fn ($q) => $q->where('country', $request->string('country')))
            ->when($request->filled('city'), fn ($q) => $q->where('city', 'like', '%' . $request->string('city') . '%'))
            ->when($request->filled('organization'), fn ($q) => $q->where('organization', 'like', '%' . $request->string('organization') . '%'))
            ->when($request->filled('industry'), fn ($q) => $q->where('industry', 'like', '%' . $request->string('industry') . '%'))
            ->when($request->filled('job_title'), fn ($q) => $q->where('job_title', 'like', '%' . $request->string('job_title') . '%'))
            ->when($request->filled('skill'), function ($q) use ($request) {
                $term = $request->string('skill');
                $q->whereHas('skills', fn ($s) => $s->where('name', 'like', "%{$term}%"));
            });

        return match ($request->string('sort')->toString()) {
            'name' => $query->join('users', 'users.id', '=', 'alumni_profiles.user_id')
                ->orderBy('users.first_name')
                ->select('alumni_profiles.*'),
            'oldest' => $query->orderBy('graduation_year'),
            'recent' => $query->latest('alumni_profiles.created_at'),
            default => $query->orderByDesc('graduation_year'),
        };
    }
}
