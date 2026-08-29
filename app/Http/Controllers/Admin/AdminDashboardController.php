<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BorrowRequest;
use App\Models\CateringHomemadeOrder;
use App\Models\CateringOrder;
use App\Models\Connection;
use App\Models\Donation;
use App\Models\DonationCampaign;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\MarketplaceCategory;
use App\Models\MarketplaceListing;
use App\Models\MarketplaceOrder;
use App\Models\MatrimonyInterest;
use App\Models\MatrimonyProfile;
use App\Models\MediaAdvocacyCategory;
use App\Models\MediaAdvocacyOrder;
use App\Models\MentorProfile;
use App\Models\Mentorship;
use App\Models\MentorshipRequest;
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

        $modules = Cache::remember('admin.dashboard.modules', now()->addMinutes(5), function () {
            $lastSixMonths = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i));

            return [
                'career' => [
                    'stats' => [
                        'total' => JobPosting::count(),
                        'approved' => JobPosting::approved()->count(),
                        'pending' => JobPosting::where('status', JobPosting::STATUS_PENDING)->count(),
                        'applications' => JobApplication::count(),
                    ],
                    'chart' => JobPosting::approved()
                        ->whereNotNull('industry')
                        ->selectRaw('industry, COUNT(*) as total')
                        ->groupBy('industry')
                        ->orderByDesc('total')
                        ->limit(6)
                        ->get(),
                ],
                'marketplace' => [
                    'stats' => [
                        'total_listings' => MarketplaceListing::count(),
                        'pending_listings' => MarketplaceListing::pending()->count(),
                        'completed_orders' => MarketplaceOrder::completed()->count(),
                        'total_commission' => (float) MarketplaceOrder::completed()->sum('commission_amount'),
                    ],
                    'chart' => MarketplaceCategory::active()
                        ->withCount('listings as listings_count')
                        ->orderByDesc('listings_count')
                        ->limit(6)
                        ->get(['name']),
                ],
                'matrimony' => [
                    'stats' => [
                        'pending_profiles' => MatrimonyProfile::pending()->count(),
                        'approved_profiles' => MatrimonyProfile::approved()->count(),
                        'total_interests' => MatrimonyInterest::count(),
                        'accepted_interests' => MatrimonyInterest::accepted()->count(),
                    ],
                    'chart' => [
                        'male' => MatrimonyProfile::searchable()->where('gender', 'male')->count(),
                        'female' => MatrimonyProfile::searchable()->where('gender', 'female')->count(),
                        'other' => MatrimonyProfile::searchable()->whereNotIn('gender', ['male', 'female'])->count(),
                    ],
                ],
                'catering' => [
                    'stats' => [
                        'total_orders' => CateringOrder::count(),
                        'delivered_orders' => CateringOrder::where('status', CateringOrder::STATUS_DELIVERED)->count(),
                        'total_revenue' => (float) CateringOrder::where('payment_status', CateringOrder::PAYMENT_PAID)->sum('total_amount'),
                        'homemade_completed' => CateringHomemadeOrder::where('status', CateringHomemadeOrder::STATUS_COMPLETED)->count(),
                    ],
                    'chart' => $lastSixMonths->map(fn ($month) => [
                        'month' => $month->format('M Y'),
                        'revenue' => (float) CateringOrder::where('payment_status', CateringOrder::PAYMENT_PAID)
                            ->whereYear('event_date', $month->year)
                            ->whereMonth('event_date', $month->month)
                            ->sum('total_amount'),
                    ]),
                ],
                'media_advocacy' => [
                    'stats' => [
                        'total_orders' => MediaAdvocacyOrder::count(),
                        'pending_orders' => MediaAdvocacyOrder::where('status', MediaAdvocacyOrder::STATUS_PENDING)->count(),
                        'completed_orders' => MediaAdvocacyOrder::where('status', MediaAdvocacyOrder::STATUS_COMPLETED)->count(),
                        'total_income' => (float) MediaAdvocacyOrder::where('status', MediaAdvocacyOrder::STATUS_COMPLETED)->sum('final_price'),
                    ],
                    'chart' => MediaAdvocacyCategory::query()
                        ->withSum(['orders as total_income' => fn ($q) => $q->where('status', MediaAdvocacyOrder::STATUS_COMPLETED)], 'final_price')
                        ->orderByDesc('total_income')
                        ->limit(6)
                        ->get(['name']),
                ],
                'mentorship' => [
                    'stats' => [
                        'active_mentors' => MentorProfile::active()->count(),
                        'active_mentorships' => Mentorship::active()->count(),
                        'pending_requests' => MentorshipRequest::where('status', MentorshipRequest::STATUS_PENDING)->count(),
                        'completed_mentorships' => Mentorship::where('status', Mentorship::STATUS_COMPLETED)->count(),
                    ],
                    'chart' => [
                        'pending' => MentorshipRequest::where('status', MentorshipRequest::STATUS_PENDING)->count(),
                        'accepted' => MentorshipRequest::where('status', MentorshipRequest::STATUS_ACCEPTED)->count(),
                        'rejected' => MentorshipRequest::where('status', MentorshipRequest::STATUS_REJECTED)->count(),
                        'completed' => MentorshipRequest::where('status', MentorshipRequest::STATUS_COMPLETED)->count(),
                    ],
                ],
                'library' => [
                    'stats' => [
                        'total_books' => Book::count(),
                        'approved_books' => Book::approved()->count(),
                        'pending_requests' => BorrowRequest::pending()->count(),
                        'overdue' => BorrowRequest::handedOver()->where('due_date', '<', now())->count(),
                    ],
                    'chart' => [
                        'pending' => BorrowRequest::pending()->count(),
                        'approved' => BorrowRequest::approved()->count(),
                        'handed_over' => BorrowRequest::handedOver()->count(),
                        'returned' => BorrowRequest::returned()->count(),
                        'rejected' => BorrowRequest::rejected()->count(),
                    ],
                ],
                'donations' => [
                    'stats' => [
                        'total' => (float) Donation::completed()->sum('amount'),
                        'this_month' => (float) Donation::completed()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount'),
                        'active_campaigns' => DonationCampaign::active()->count(),
                        'total_donations' => Donation::completed()->count(),
                    ],
                    'chart' => $lastSixMonths->map(fn ($month) => [
                        'month' => $month->format('M Y'),
                        'total' => (float) Donation::completed()
                            ->whereYear('created_at', $month->year)
                            ->whereMonth('created_at', $month->month)
                            ->sum('amount'),
                    ]),
                ],
            ];
        });

        return view('admin.dashboard', compact('stats', 'growthTrend', 'modules'));
    }
}
