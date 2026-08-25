<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MatrimonyInterest;
use App\Models\MatrimonyProfile;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MatrimonyReportController extends Controller
{
    protected const COMPLAINT_TYPES = [MatrimonyProfile::class, User::class];

    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-matrimony'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $summary = [
            'pending_profiles' => MatrimonyProfile::pending()->count(),
            'approved_profiles' => MatrimonyProfile::approved()->count(),
            'rejected_profiles' => MatrimonyProfile::rejected()->count(),
            'suspended_profiles' => MatrimonyProfile::suspended()->count(),
            'verified_profiles' => MatrimonyProfile::where('is_verified', true)->count(),
            'male_profiles' => MatrimonyProfile::searchable()->where('gender', 'male')->count(),
            'female_profiles' => MatrimonyProfile::searchable()->where('gender', 'female')->count(),
            'total_interests' => MatrimonyInterest::count(),
            'accepted_interests' => MatrimonyInterest::accepted()->count(),
            'declined_interests' => MatrimonyInterest::where('status', MatrimonyInterest::STATUS_DECLINED)->count(),
            'open_complaints' => Report::whereIn('reportable_type', self::COMPLAINT_TYPES)->where('status', 'pending')->count(),
        ];

        return view('admin.matrimony.reports.index', compact('summary'));
    }

    public function profiles(Request $request): View
    {
        $this->ensurePermission($request);

        $profiles = MatrimonyProfile::with('creator')
            ->withCount(['interests as interests_received_count'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('country'), fn ($q) => $q->where('country', 'like', '%' . $request->string('country') . '%'))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.matrimony.reports.profiles', compact('profiles'));
    }

    public function interests(Request $request): View
    {
        $this->ensurePermission($request);

        $interests = MatrimonyInterest::with(['profile.creator', 'requester'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.matrimony.reports.interests', compact('interests'));
    }

    public function complaints(Request $request): View
    {
        $this->ensurePermission($request);

        $complaints = Report::with(['reporter', 'reportable'])
            ->whereIn('reportable_type', self::COMPLAINT_TYPES)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.matrimony.reports.complaints', compact('complaints'));
    }

    public function resolveComplaint(Request $request, Report $report): RedirectResponse
    {
        $this->ensurePermission($request);
        abort_unless(in_array($report->reportable_type, self::COMPLAINT_TYPES, true), 404);

        $data = $request->validate(['status' => ['required', 'in:reviewed,dismissed,action_taken']]);

        $report->update([
            'status' => $data['status'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('status', 'Complaint updated.');
    }

    public function export(Request $request, string $type): StreamedResponse
    {
        $this->ensurePermission($request);

        return match ($type) {
            'profiles' => $this->exportProfiles(),
            'interests' => $this->exportInterests(),
            default => abort(404),
        };
    }

    protected function exportProfiles(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Display Name', 'Managed By', 'Status', 'Verified', 'Gender', 'Country', 'Views']);

            MatrimonyProfile::with('creator')->chunk(200, function ($chunk) use ($handle) {
                foreach ($chunk as $profile) {
                    fputcsv($handle, [
                        $profile->display_name,
                        $profile->creator->full_name,
                        $profile->status,
                        $profile->is_verified ? 'Yes' : 'No',
                        $profile->gender,
                        $profile->country,
                        $profile->views_count,
                    ]);
                }
            });

            fclose($handle);
        }, 'matrimony-profiles.csv', ['Content-Type' => 'text/csv']);
    }

    protected function exportInterests(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Target Profile', 'Requested By', 'Status', 'Sent At']);

            MatrimonyInterest::with(['profile', 'requester'])->chunk(200, function ($chunk) use ($handle) {
                foreach ($chunk as $interest) {
                    fputcsv($handle, [
                        $interest->profile->display_name,
                        $interest->requester->full_name,
                        $interest->status,
                        $interest->created_at->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($handle);
        }, 'matrimony-interests.csv', ['Content-Type' => 'text/csv']);
    }
}
