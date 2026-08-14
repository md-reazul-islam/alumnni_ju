<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Connection;
use App\Models\Donation;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\JobPosting;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class AdminDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $stats = Cache::remember('admin.dashboard.stats', now()->addMinutes(5), function () {
            $alumniQuery = User::whereHas('role', fn ($q) => $q->where('slug', Role::ALUMNI));

            return [
                'total_alumni' => (clone $alumniQuery)->count(),
                'verified_alumni' => (clone $alumniQuery)->verified()->count(),
                'pending_alumni' => (clone $alumniQuery)->pending()->count(),
                'active_users' => User::whereNotNull('last_login_at')->where('last_login_at', '>=', now()->subDays(30))->count(),
                'upcoming_events' => Event::published()->upcoming()->count(),
                'event_registrations' => EventRegistration::where('status', 'registered')->count(),
                'total_jobs' => JobPosting::approved()->count(),
                'total_donations' => Donation::completed()->sum('amount'),
                'total_connections' => Connection::accepted()->count(),
                'new_registrations' => (clone $alumniQuery)->where('created_at', '>=', now()->subDays(30))->count(),
            ];
        });

        $growthTrend = Cache::remember('admin.dashboard.growth', now()->addMinutes(15), function () {
            $alumniQuery = User::whereHas('role', fn ($q) => $q->where('slug', Role::ALUMNI));

            return collect(range(5, 0))->map(function ($i) use ($alumniQuery) {
                $month = now()->subMonths($i);

                return [
                    'month' => $month->format('M Y'),
                    'total' => (clone $alumniQuery)->where('created_at', '<=', $month->endOfMonth())->count(),
                ];
            });
        });

        return view('admin.dashboard', compact('stats', 'growthTrend'));
    }
}
