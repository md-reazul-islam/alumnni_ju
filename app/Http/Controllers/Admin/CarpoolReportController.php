<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarpoolBooking;
use App\Models\CarpoolDriverProfile;
use App\Models\CarpoolPayment;
use App\Models\CarpoolSchedule;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CarpoolReportController extends Controller
{
    protected const COMPLAINT_TYPES = [CarpoolBooking::class, User::class];

    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-carpooling'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $summary = [
            'verified_drivers' => CarpoolDriverProfile::approved()->count(),
            'pending_drivers' => CarpoolDriverProfile::pending()->count(),
            'rejected_drivers' => CarpoolDriverProfile::rejected()->count(),
            'suspended_drivers' => CarpoolDriverProfile::where('status', CarpoolDriverProfile::STATUS_SUSPENDED)->count(),
            'total_trips' => CarpoolSchedule::count(),
            'completed_trips' => CarpoolSchedule::where('status', CarpoolSchedule::STATUS_COMPLETED)->count(),
            'cancelled_trips' => CarpoolSchedule::where('status', CarpoolSchedule::STATUS_CANCELLED)->count(),
            'confirmed_bookings' => CarpoolBooking::confirmed()->count(),
            'total_revenue' => (float) CarpoolBooking::where('payment_status', CarpoolBooking::PAYMENT_PAID)->sum('total_fare'),
            'total_commission' => (float) CarpoolBooking::where('payment_status', CarpoolBooking::PAYMENT_PAID)->sum('commission_amount'),
            'pending_payouts' => (float) CarpoolBooking::payoutPending()->sum('driver_payout_amount'),
            'open_complaints' => Report::whereIn('reportable_type', self::COMPLAINT_TYPES)->where('status', 'pending')->count(),
        ];

        return view('admin.carpooling.reports.index', compact('summary'));
    }

    public function days(Request $request): View
    {
        $this->ensurePermission($request);

        $days = CarpoolSchedule::query()
            ->selectRaw('departure_date, COUNT(*) as trip_count, SUM(seats_offered) as seats_offered, SUM(seats_booked) as seats_booked')
            ->groupBy('departure_date')
            ->orderByDesc('departure_date')
            ->paginate(30);

        return view('admin.carpooling.reports.days', compact('days'));
    }

    public function drivers(Request $request): View
    {
        $this->ensurePermission($request);

        $drivers = CarpoolDriverProfile::with('user')
            ->withCount('schedules as trips_count')
            ->withCount(['schedules as completed_trips_count' => fn ($q) => $q->where('status', CarpoolSchedule::STATUS_COMPLETED)])
            ->withCount(['schedules as cancelled_trips_count' => fn ($q) => $q->where('status', CarpoolSchedule::STATUS_CANCELLED)])
            ->orderByDesc('total_earned')
            ->paginate(20);

        return view('admin.carpooling.reports.drivers', compact('drivers'));
    }

    public function passengers(Request $request): View
    {
        $this->ensurePermission($request);

        $passengers = User::query()
            ->whereHas('carpoolBookings')
            ->withCount('carpoolBookings as bookings_count')
            ->withCount(['carpoolBookings as confirmed_bookings_count' => fn ($q) => $q->whereIn('status', [CarpoolBooking::STATUS_CONFIRMED, CarpoolBooking::STATUS_COMPLETED])])
            ->withSum(['carpoolBookings as total_spent' => fn ($q) => $q->where('payment_status', CarpoolBooking::PAYMENT_PAID)], 'total_fare')
            ->orderByDesc('bookings_count')
            ->paginate(20);

        return view('admin.carpooling.reports.passengers', compact('passengers'));
    }

    public function payments(Request $request): View
    {
        $this->ensurePermission($request);

        $payments = CarpoolPayment::with(['booking.passenger', 'booking.schedule.driverProfile.user'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.carpooling.reports.payments', compact('payments'));
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

        return view('admin.carpooling.reports.complaints', compact('complaints'));
    }

    public function resolveComplaint(Request $request, Report $report): RedirectResponse
    {
        $this->ensurePermission($request);
        abort_unless(in_array($report->reportable_type, self::COMPLAINT_TYPES, true), 404);

        $data = $request->validate([
            'status' => ['required', 'in:reviewed,dismissed,action_taken'],
        ]);

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
            'drivers' => $this->exportDrivers(),
            'passengers' => $this->exportPassengers(),
            'payments' => $this->exportPayments(),
            'days' => $this->exportDays(),
            default => abort(404),
        };
    }

    protected function exportDrivers(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Driver', 'Email', 'Status', 'License #', 'Trips Posted', 'Completed', 'Cancelled', 'Total Earned']);

            CarpoolDriverProfile::with('user')
                ->withCount('schedules as trips_count')
                ->withCount(['schedules as completed_trips_count' => fn ($q) => $q->where('status', CarpoolSchedule::STATUS_COMPLETED)])
                ->withCount(['schedules as cancelled_trips_count' => fn ($q) => $q->where('status', CarpoolSchedule::STATUS_CANCELLED)])
                ->chunk(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $driver) {
                        fputcsv($handle, [
                            $driver->user->full_name,
                            $driver->user->email,
                            $driver->status,
                            $driver->license_number,
                            $driver->trips_count,
                            $driver->completed_trips_count,
                            $driver->cancelled_trips_count,
                            $driver->total_earned,
                        ]);
                    }
                });

            fclose($handle);
        }, 'carpool-drivers.csv', ['Content-Type' => 'text/csv']);
    }

    protected function exportPassengers(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Passenger', 'Email', 'Bookings', 'Confirmed/Completed', 'Total Spent']);

            User::whereHas('carpoolBookings')
                ->withCount('carpoolBookings as bookings_count')
                ->withCount(['carpoolBookings as confirmed_bookings_count' => fn ($q) => $q->whereIn('status', [CarpoolBooking::STATUS_CONFIRMED, CarpoolBooking::STATUS_COMPLETED])])
                ->withSum(['carpoolBookings as total_spent' => fn ($q) => $q->where('payment_status', CarpoolBooking::PAYMENT_PAID)], 'total_fare')
                ->chunk(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $passenger) {
                        fputcsv($handle, [
                            $passenger->full_name,
                            $passenger->email,
                            $passenger->bookings_count,
                            $passenger->confirmed_bookings_count,
                            $passenger->total_spent ?? 0,
                        ]);
                    }
                });

            fclose($handle);
        }, 'carpool-passengers.csv', ['Content-Type' => 'text/csv']);
    }

    protected function exportPayments(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Booking #', 'Passenger', 'Driver', 'Amount', 'Status', 'Paid At']);

            CarpoolPayment::with(['booking.passenger', 'booking.schedule.driverProfile.user'])
                ->chunk(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $payment) {
                        fputcsv($handle, [
                            $payment->carpool_booking_id,
                            $payment->booking?->passenger?->full_name,
                            $payment->booking?->schedule?->driverProfile?->user?->full_name,
                            $payment->amount,
                            $payment->status,
                            $payment->paid_at?->format('Y-m-d H:i'),
                        ]);
                    }
                });

            fclose($handle);
        }, 'carpool-payments.csv', ['Content-Type' => 'text/csv']);
    }

    protected function exportDays(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Trips', 'Seats Offered', 'Seats Booked']);

            CarpoolSchedule::query()
                ->selectRaw('departure_date, COUNT(*) as trip_count, SUM(seats_offered) as seats_offered, SUM(seats_booked) as seats_booked')
                ->groupBy('departure_date')
                ->orderByDesc('departure_date')
                ->get()
                ->each(function ($row) use ($handle) {
                    fputcsv($handle, [
                        $row->departure_date,
                        $row->trip_count,
                        $row->seats_offered,
                        $row->seats_booked,
                    ]);
                });

            fclose($handle);
        }, 'carpool-days.csv', ['Content-Type' => 'text/csv']);
    }
}
