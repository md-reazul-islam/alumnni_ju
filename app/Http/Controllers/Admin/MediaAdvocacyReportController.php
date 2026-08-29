<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAdvocacyCategory;
use App\Models\MediaAdvocacyOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaAdvocacyReportController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-media-advocacy'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $summary = [
            'total_orders' => MediaAdvocacyOrder::count(),
            'pending_orders' => MediaAdvocacyOrder::where('status', MediaAdvocacyOrder::STATUS_PENDING)->count(),
            'confirmed_orders' => MediaAdvocacyOrder::where('status', MediaAdvocacyOrder::STATUS_CONFIRMED)->count(),
            'completed_orders' => MediaAdvocacyOrder::where('status', MediaAdvocacyOrder::STATUS_COMPLETED)->count(),
            'cancelled_orders' => MediaAdvocacyOrder::where('status', MediaAdvocacyOrder::STATUS_CANCELLED)->count(),
            'total_income' => (float) MediaAdvocacyOrder::where('status', MediaAdvocacyOrder::STATUS_COMPLETED)->sum('final_price'),
            'pipeline_value' => (float) MediaAdvocacyOrder::where('status', MediaAdvocacyOrder::STATUS_CONFIRMED)->sum('final_price'),
        ];

        return view('admin.media-advocacy.reports.index', compact('summary'));
    }

    public function pending(Request $request): View
    {
        $this->ensurePermission($request);

        $orders = MediaAdvocacyOrder::where('status', MediaAdvocacyOrder::STATUS_PENDING)
            ->with(['category', 'customer'])
            ->latest()
            ->paginate(20);

        return view('admin.media-advocacy.reports.pending', compact('orders'));
    }

    public function confirmed(Request $request): View
    {
        $this->ensurePermission($request);

        $orders = MediaAdvocacyOrder::whereIn('status', [MediaAdvocacyOrder::STATUS_CONFIRMED, MediaAdvocacyOrder::STATUS_COMPLETED])
            ->with(['category', 'customer', 'handler'])
            ->latest()
            ->paginate(20);

        return view('admin.media-advocacy.reports.confirmed', compact('orders'));
    }

    public function cancelled(Request $request): View
    {
        $this->ensurePermission($request);

        $orders = MediaAdvocacyOrder::where('status', MediaAdvocacyOrder::STATUS_CANCELLED)
            ->with(['category', 'customer', 'handler'])
            ->latest()
            ->paginate(20);

        return view('admin.media-advocacy.reports.cancelled', compact('orders'));
    }

    public function incomeByService(Request $request): View
    {
        $this->ensurePermission($request);

        $categories = MediaAdvocacyCategory::query()
            ->withCount(['orders as completed_orders_count' => fn ($q) => $q->where('status', MediaAdvocacyOrder::STATUS_COMPLETED)])
            ->withSum(['orders as total_income' => fn ($q) => $q->where('status', MediaAdvocacyOrder::STATUS_COMPLETED)], 'final_price')
            ->orderByDesc('total_income')
            ->get();

        return view('admin.media-advocacy.reports.income-by-service', compact('categories'));
    }

    public function incomeByCustomer(Request $request): View
    {
        $this->ensurePermission($request);

        $customers = User::whereHas('mediaAdvocacyOrders')
            ->withCount('mediaAdvocacyOrders as orders_count')
            ->withCount(['mediaAdvocacyOrders as completed_orders_count' => fn ($q) => $q->where('status', MediaAdvocacyOrder::STATUS_COMPLETED)])
            ->withSum(['mediaAdvocacyOrders as total_income' => fn ($q) => $q->where('status', MediaAdvocacyOrder::STATUS_COMPLETED)], 'final_price')
            ->orderByDesc('total_income')
            ->paginate(20);

        return view('admin.media-advocacy.reports.income-by-customer', compact('customers'));
    }

    public function export(Request $request, string $type): StreamedResponse
    {
        $this->ensurePermission($request);

        return match ($type) {
            'pending' => $this->exportPending(),
            'confirmed' => $this->exportConfirmed(),
            'cancelled' => $this->exportCancelled(),
            'income-by-service' => $this->exportIncomeByService(),
            'income-by-customer' => $this->exportIncomeByCustomer(),
            default => abort(404),
        };
    }

    protected function exportPending(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order #', 'Service', 'Customer', 'Requested']);

            MediaAdvocacyOrder::where('status', MediaAdvocacyOrder::STATUS_PENDING)
                ->with(['category', 'customer'])
                ->chunk(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $order) {
                        fputcsv($handle, [$order->id, $order->category->name, $order->customer->full_name, $order->created_at->format('Y-m-d')]);
                    }
                });

            fclose($handle);
        }, 'media-advocacy-pending.csv', ['Content-Type' => 'text/csv']);
    }

    protected function exportConfirmed(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order #', 'Service', 'Customer', 'Status', 'Price', 'Handled By']);

            MediaAdvocacyOrder::whereIn('status', [MediaAdvocacyOrder::STATUS_CONFIRMED, MediaAdvocacyOrder::STATUS_COMPLETED])
                ->with(['category', 'customer', 'handler'])
                ->chunk(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $order) {
                        fputcsv($handle, [$order->id, $order->category->name, $order->customer->full_name, $order->status, $order->final_price, $order->handler?->full_name]);
                    }
                });

            fclose($handle);
        }, 'media-advocacy-confirmed.csv', ['Content-Type' => 'text/csv']);
    }

    protected function exportCancelled(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order #', 'Service', 'Customer', 'Handled By', 'Cancelled At']);

            MediaAdvocacyOrder::where('status', MediaAdvocacyOrder::STATUS_CANCELLED)
                ->with(['category', 'customer', 'handler'])
                ->chunk(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $order) {
                        fputcsv($handle, [$order->id, $order->category->name, $order->customer->full_name, $order->handler?->full_name, $order->updated_at->format('Y-m-d H:i')]);
                    }
                });

            fclose($handle);
        }, 'media-advocacy-cancelled.csv', ['Content-Type' => 'text/csv']);
    }

    protected function exportIncomeByService(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Service', 'Completed Orders', 'Total Income']);

            MediaAdvocacyCategory::query()
                ->withCount(['orders as completed_orders_count' => fn ($q) => $q->where('status', MediaAdvocacyOrder::STATUS_COMPLETED)])
                ->withSum(['orders as total_income' => fn ($q) => $q->where('status', MediaAdvocacyOrder::STATUS_COMPLETED)], 'final_price')
                ->chunk(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $category) {
                        fputcsv($handle, [$category->name, $category->completed_orders_count, $category->total_income ?? 0]);
                    }
                });

            fclose($handle);
        }, 'media-advocacy-income-by-service.csv', ['Content-Type' => 'text/csv']);
    }

    protected function exportIncomeByCustomer(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Customer', 'Email', 'Orders', 'Completed Orders', 'Total Income']);

            User::whereHas('mediaAdvocacyOrders')
                ->withCount('mediaAdvocacyOrders as orders_count')
                ->withCount(['mediaAdvocacyOrders as completed_orders_count' => fn ($q) => $q->where('status', MediaAdvocacyOrder::STATUS_COMPLETED)])
                ->withSum(['mediaAdvocacyOrders as total_income' => fn ($q) => $q->where('status', MediaAdvocacyOrder::STATUS_COMPLETED)], 'final_price')
                ->chunk(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $customer) {
                        fputcsv($handle, [$customer->full_name, $customer->email, $customer->orders_count, $customer->completed_orders_count, $customer->total_income ?? 0]);
                    }
                });

            fclose($handle);
        }, 'media-advocacy-income-by-customer.csv', ['Content-Type' => 'text/csv']);
    }
}
