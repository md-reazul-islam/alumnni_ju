<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CateringHomemadeCategory;
use App\Models\CateringHomemadeOrder;
use App\Models\CateringOrder;
use App\Models\CateringOrderFeedback;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CateringReportController extends Controller
{
    protected const COMPLAINT_TYPES = [CateringOrder::class, CateringHomemadeOrder::class, User::class];

    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-catering'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $summary = [
            'total_orders' => CateringOrder::count(),
            'submitted_orders' => CateringOrder::where('status', CateringOrder::STATUS_SUBMITTED)->count(),
            'priced_orders' => CateringOrder::where('status', CateringOrder::STATUS_PRICED)->count(),
            'accepted_orders' => CateringOrder::where('status', CateringOrder::STATUS_ACCEPTED)->count(),
            'declined_orders' => CateringOrder::where('status', CateringOrder::STATUS_DECLINED)->count(),
            'delivered_orders' => CateringOrder::where('status', CateringOrder::STATUS_DELIVERED)->count(),
            'cancelled_orders' => CateringOrder::where('status', CateringOrder::STATUS_CANCELLED)->count(),
            'total_revenue' => (float) CateringOrder::where('payment_status', CateringOrder::PAYMENT_PAID)->sum('total_amount'),
            'avg_order_value' => (float) CateringOrder::where('payment_status', CateringOrder::PAYMENT_PAID)->avg('total_amount'),
            'average_rating' => (float) CateringOrderFeedback::avg('rating'),
            'homemade_pending_listings' => \App\Models\CateringHomemadeListing::pending()->count(),
            'homemade_completed_orders' => CateringHomemadeOrder::where('status', CateringHomemadeOrder::STATUS_COMPLETED)->count(),
            'homemade_total_commission' => (float) CateringHomemadeOrder::where('status', CateringHomemadeOrder::STATUS_COMPLETED)->sum('commission_amount'),
            'open_complaints' => Report::whereIn('reportable_type', self::COMPLAINT_TYPES)->where('status', 'pending')->count(),
        ];

        return view('admin.catering.reports.index', compact('summary'));
    }

    public function daily(Request $request): View
    {
        $this->ensurePermission($request);

        $days = CateringOrder::query()
            ->selectRaw('event_date, COUNT(*) as order_count, SUM(CASE WHEN payment_status = "paid" THEN total_amount ELSE 0 END) as revenue')
            ->groupBy('event_date')
            ->orderByDesc('event_date')
            ->paginate(30);

        return view('admin.catering.reports.daily', compact('days'));
    }

    public function monthly(Request $request): View
    {
        $this->ensurePermission($request);

        $months = CateringOrder::query()
            ->selectRaw('DATE_FORMAT(event_date, "%Y-%m") as month, COUNT(*) as order_count, SUM(CASE WHEN payment_status = "paid" THEN total_amount ELSE 0 END) as revenue')
            ->groupBy('month')
            ->orderByDesc('month')
            ->paginate(24);

        return view('admin.catering.reports.monthly', compact('months'));
    }

    public function delivered(Request $request): View
    {
        $this->ensurePermission($request);

        $orders = CateringOrder::where('status', CateringOrder::STATUS_DELIVERED)
            ->with(['customer', 'category'])
            ->latest('delivered_at')
            ->paginate(20);

        return view('admin.catering.reports.delivered', compact('orders'));
    }

    public function cancelled(Request $request): View
    {
        $this->ensurePermission($request);

        $orders = CateringOrder::where('status', CateringOrder::STATUS_CANCELLED)
            ->with(['customer', 'category', 'canceller'])
            ->latest('cancelled_at')
            ->paginate(20);

        return view('admin.catering.reports.cancelled', compact('orders'));
    }

    public function topCustomers(Request $request): View
    {
        $this->ensurePermission($request);

        $customers = User::whereHas('cateringOrders')
            ->withCount('cateringOrders as orders_count')
            ->withSum(['cateringOrders as total_spent' => fn ($q) => $q->where('payment_status', CateringOrder::PAYMENT_PAID)], 'total_amount')
            ->orderByDesc('total_spent')
            ->paginate(20);

        return view('admin.catering.reports.top-customers', compact('customers'));
    }

    public function feedback(Request $request): View
    {
        $this->ensurePermission($request);

        $feedback = CateringOrderFeedback::with(['order.category', 'customer'])
            ->latest()
            ->paginate(20);

        $averageRating = (float) CateringOrderFeedback::avg('rating');

        return view('admin.catering.reports.feedback', compact('feedback', 'averageRating'));
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

        return view('admin.catering.reports.complaints', compact('complaints'));
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

    public function homemadeOrders(Request $request): View
    {
        $this->ensurePermission($request);

        $orders = CateringHomemadeOrder::with(['listing.category', 'buyer', 'seller'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.catering.reports.homemade-orders', compact('orders'));
    }

    public function homemadeVendors(Request $request): View
    {
        $this->ensurePermission($request);

        $vendors = User::whereHas('cateringHomemadeListings')
            ->withCount('cateringHomemadeListings as listings_count')
            ->withCount(['cateringHomemadeOrdersAsSeller as completed_orders_count' => fn ($q) => $q->where('status', CateringHomemadeOrder::STATUS_COMPLETED)])
            ->withSum(['cateringHomemadeOrdersAsSeller as total_sales' => fn ($q) => $q->where('status', CateringHomemadeOrder::STATUS_COMPLETED)], 'final_price')
            ->orderByDesc('total_sales')
            ->paginate(20);

        return view('admin.catering.reports.homemade-vendors', compact('vendors'));
    }

    public function homemadeCategories(Request $request): View
    {
        $this->ensurePermission($request);

        $categories = CateringHomemadeCategory::withCount('listings')
            ->withCount(['listings as completed_orders_count' => fn ($q) => $q->whereHas('orders', fn ($o) => $o->where('status', CateringHomemadeOrder::STATUS_COMPLETED))])
            ->get()
            ->map(function ($category) {
                $category->total_commission = CateringHomemadeOrder::whereHas('listing', fn ($q) => $q->where('catering_homemade_category_id', $category->id))
                    ->where('status', CateringHomemadeOrder::STATUS_COMPLETED)
                    ->sum('commission_amount');

                return $category;
            })
            ->sortByDesc('total_commission')
            ->values();

        return view('admin.catering.reports.homemade-categories', compact('categories'));
    }

    public function export(Request $request, string $type): StreamedResponse
    {
        $this->ensurePermission($request);

        return match ($type) {
            'daily' => $this->exportDaily(),
            'monthly' => $this->exportMonthly(),
            'delivered' => $this->exportDelivered(),
            'cancelled' => $this->exportCancelled(),
            'top-customers' => $this->exportTopCustomers(),
            'feedback' => $this->exportFeedback(),
            'homemade-orders' => $this->exportHomemadeOrders(),
            'homemade-vendors' => $this->exportHomemadeVendors(),
            default => abort(404),
        };
    }

    protected function exportDaily(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Event Date', 'Orders', 'Revenue']);

            CateringOrder::query()
                ->selectRaw('event_date, COUNT(*) as order_count, SUM(CASE WHEN payment_status = "paid" THEN total_amount ELSE 0 END) as revenue')
                ->groupBy('event_date')
                ->orderByDesc('event_date')
                ->get()
                ->each(fn ($row) => fputcsv($handle, [$row->event_date, $row->order_count, $row->revenue]));

            fclose($handle);
        }, 'catering-daily.csv', ['Content-Type' => 'text/csv']);
    }

    protected function exportMonthly(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Month', 'Orders', 'Revenue']);

            CateringOrder::query()
                ->selectRaw('DATE_FORMAT(event_date, "%Y-%m") as month, COUNT(*) as order_count, SUM(CASE WHEN payment_status = "paid" THEN total_amount ELSE 0 END) as revenue')
                ->groupBy('month')
                ->orderByDesc('month')
                ->get()
                ->each(fn ($row) => fputcsv($handle, [$row->month, $row->order_count, $row->revenue]));

            fclose($handle);
        }, 'catering-monthly.csv', ['Content-Type' => 'text/csv']);
    }

    protected function exportDelivered(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order #', 'Customer', 'Category', 'Total', 'Delivered At']);

            CateringOrder::where('status', CateringOrder::STATUS_DELIVERED)
                ->with(['customer', 'category'])
                ->chunk(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $order) {
                        fputcsv($handle, [$order->id, $order->customer->full_name, $order->category->name, $order->total_amount, $order->delivered_at?->format('Y-m-d H:i')]);
                    }
                });

            fclose($handle);
        }, 'catering-delivered.csv', ['Content-Type' => 'text/csv']);
    }

    protected function exportCancelled(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order #', 'Customer', 'Category', 'Cancelled By', 'Reason', 'Cancelled At']);

            CateringOrder::where('status', CateringOrder::STATUS_CANCELLED)
                ->with(['customer', 'category', 'canceller'])
                ->chunk(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $order) {
                        fputcsv($handle, [$order->id, $order->customer->full_name, $order->category->name, $order->canceller?->full_name, $order->cancellation_reason, $order->cancelled_at?->format('Y-m-d H:i')]);
                    }
                });

            fclose($handle);
        }, 'catering-cancelled.csv', ['Content-Type' => 'text/csv']);
    }

    protected function exportTopCustomers(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Customer', 'Email', 'Orders', 'Total Spent']);

            User::whereHas('cateringOrders')
                ->withCount('cateringOrders as orders_count')
                ->withSum(['cateringOrders as total_spent' => fn ($q) => $q->where('payment_status', CateringOrder::PAYMENT_PAID)], 'total_amount')
                ->chunk(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $customer) {
                        fputcsv($handle, [$customer->full_name, $customer->email, $customer->orders_count, $customer->total_spent ?? 0]);
                    }
                });

            fclose($handle);
        }, 'catering-top-customers.csv', ['Content-Type' => 'text/csv']);
    }

    protected function exportFeedback(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order #', 'Customer', 'Rating', 'Comment', 'Date']);

            CateringOrderFeedback::with(['order', 'customer'])
                ->chunk(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $fb) {
                        fputcsv($handle, [$fb->catering_order_id, $fb->customer->full_name, $fb->rating, $fb->comment, $fb->created_at->format('Y-m-d')]);
                    }
                });

            fclose($handle);
        }, 'catering-feedback.csv', ['Content-Type' => 'text/csv']);
    }

    protected function exportHomemadeOrders(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order #', 'Listing', 'Buyer', 'Seller', 'Qty', 'Status', 'Final Price', 'Commission']);

            CateringHomemadeOrder::with(['listing', 'buyer', 'seller'])
                ->chunk(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $order) {
                        fputcsv($handle, [$order->id, $order->listing->title, $order->buyer->full_name, $order->seller->full_name, $order->quantity, $order->status, $order->final_price, $order->commission_amount]);
                    }
                });

            fclose($handle);
        }, 'catering-homemade-orders.csv', ['Content-Type' => 'text/csv']);
    }

    protected function exportHomemadeVendors(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Vendor', 'Email', 'Listings', 'Completed Orders', 'Total Sales']);

            User::whereHas('cateringHomemadeListings')
                ->withCount('cateringHomemadeListings as listings_count')
                ->withCount(['cateringHomemadeOrdersAsSeller as completed_orders_count' => fn ($q) => $q->where('status', CateringHomemadeOrder::STATUS_COMPLETED)])
                ->withSum(['cateringHomemadeOrdersAsSeller as total_sales' => fn ($q) => $q->where('status', CateringHomemadeOrder::STATUS_COMPLETED)], 'final_price')
                ->chunk(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $vendor) {
                        fputcsv($handle, [$vendor->full_name, $vendor->email, $vendor->listings_count, $vendor->completed_orders_count, $vendor->total_sales ?? 0]);
                    }
                });

            fclose($handle);
        }, 'catering-homemade-vendors.csv', ['Content-Type' => 'text/csv']);
    }
}
