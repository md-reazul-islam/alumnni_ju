<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlumniProfile;
use App\Models\Donation;
use App\Models\Event;
use App\Models\Mentorship;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-reports') || $request->user()->hasPermission('manage-alumni'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        return view('admin.reports.index');
    }

    public function chartData(Request $request): JsonResponse
    {
        $this->ensurePermission($request);

        $alumniQuery = fn () => User::whereHas('role', fn ($q) => $q->where('slug', Role::ALUMNI));

        $growth = collect(range(11, 0))->map(function ($i) use ($alumniQuery) {
            $month = now()->subMonths($i);

            return [
                'month' => $month->format('M Y'),
                'total' => $alumniQuery()->where('created_at', '<=', $month->endOfMonth())->count(),
            ];
        });

        $byDepartment = AlumniProfile::query()
            ->selectRaw('department_id, COUNT(*) as total')
            ->whereNotNull('department_id')
            ->groupBy('department_id')
            ->with('department:id,name')
            ->get()
            ->map(fn ($row) => ['label' => $row->department?->name ?? 'Unknown', 'total' => $row->total]);

        $byGraduationYear = AlumniProfile::query()
            ->selectRaw('graduation_year, COUNT(*) as total')
            ->whereNotNull('graduation_year')
            ->groupBy('graduation_year')
            ->orderBy('graduation_year')
            ->get();

        $byCountry = AlumniProfile::query()
            ->selectRaw('country, COUNT(*) as total')
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $byIndustry = AlumniProfile::query()
            ->selectRaw('industry, COUNT(*) as total')
            ->whereNotNull('industry')
            ->groupBy('industry')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $eventParticipation = Event::withCount(['registrations' => fn ($q) => $q->where('status', 'registered')])
            ->published()
            ->orderByDesc('event_date')
            ->limit(8)
            ->get()
            ->map(fn ($e) => ['label' => $e->title, 'total' => $e->registrations_count])
            ->reverse()
            ->values();

        return response()->json([
            'growth' => $growth,
            'byDepartment' => $byDepartment,
            'byGraduationYear' => $byGraduationYear,
            'byCountry' => $byCountry,
            'byIndustry' => $byIndustry,
            'eventParticipation' => $eventParticipation,
        ]);
    }

    public function export(Request $request, string $type): StreamedResponse
    {
        $this->ensurePermission($request);

        return match ($type) {
            'demographics' => $this->exportDemographics(),
            'events' => $this->exportEvents(),
            'donations' => $this->exportDonations(),
            'mentorship' => $this->exportMentorship(),
            default => abort(404),
        };
    }

    protected function exportDemographics(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Department', 'Graduation Year', 'Country', 'Industry']);

            AlumniProfile::with(['user', 'department'])->chunk(200, function ($chunk) use ($handle) {
                foreach ($chunk as $profile) {
                    fputcsv($handle, [
                        $profile->user->full_name,
                        $profile->user->email,
                        $profile->department?->name,
                        $profile->graduation_year,
                        $profile->country,
                        $profile->industry,
                    ]);
                }
            });

            fclose($handle);
        }, 'alumni-demographics.csv', ['Content-Type' => 'text/csv']);
    }

    protected function exportEvents(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Event', 'Date', 'Category', 'Status', 'Registrations']);

            Event::withCount('registrations')->chunk(200, function ($chunk) use ($handle) {
                foreach ($chunk as $event) {
                    fputcsv($handle, [$event->title, $event->event_date->format('Y-m-d'), $event->category, $event->status, $event->registrations_count]);
                }
            });

            fclose($handle);
        }, 'events-report.csv', ['Content-Type' => 'text/csv']);
    }

    protected function exportDonations(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Donor', 'Amount', 'Category', 'Payment Status', 'Date']);

            Donation::with('donor')->chunk(200, function ($chunk) use ($handle) {
                foreach ($chunk as $donation) {
                    fputcsv($handle, [$donation->displayName(), $donation->amount, $donation->category, $donation->payment_status, $donation->created_at->format('Y-m-d')]);
                }
            });

            fclose($handle);
        }, 'donations-report.csv', ['Content-Type' => 'text/csv']);
    }

    protected function exportMentorship(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Mentor', 'Mentee', 'Status', 'Started']);

            Mentorship::with(['mentor', 'mentee'])->chunk(200, function ($chunk) use ($handle) {
                foreach ($chunk as $m) {
                    fputcsv($handle, [$m->mentor->full_name, $m->mentee->full_name, $m->status, $m->started_at?->format('Y-m-d')]);
                }
            });

            fclose($handle);
        }, 'mentorship-report.csv', ['Content-Type' => 'text/csv']);
    }
}
