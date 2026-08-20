<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceCategory;
use App\Models\MarketplaceListing;
use App\Models\MarketplaceOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MarketplaceReportController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-marketplace'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $summary = [
            'total_listings' => MarketplaceListing::count(),
            'pending_listings' => MarketplaceListing::pending()->count(),
            'approved_listings' => MarketplaceListing::approved()->count(),
            'total_orders' => MarketplaceOrder::count(),
            'ongoing_orders' => MarketplaceOrder::ongoing()->count(),
            'completed_orders' => MarketplaceOrder::completed()->count(),
            'total_commission' => (float) MarketplaceOrder::completed()->sum('commission_amount'),
        ];

        return view('admin.marketplace.reports.index', compact('summary'));
    }

    public function orders(Request $request): View
    {
        $this->ensurePermission($request);

        $orders = MarketplaceOrder::with(['listing.category', 'buyer', 'seller'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.marketplace.reports.orders', compact('orders'));
    }

    public function sellers(Request $request): View
    {
        $this->ensurePermission($request);

        $sellers = User::query()
            ->whereHas('marketplaceListings')
            ->withCount('marketplaceListings as listings_count')
            ->withCount(['marketplaceListings as approved_listings_count' => fn ($q) => $q->where('status', 'approved')])
            ->withCount(['marketplaceOrdersAsSeller as completed_orders_count' => fn ($q) => $q->where('status', 'completed')])
            ->withSum(['marketplaceOrdersAsSeller as total_commission' => fn ($q) => $q->where('status', 'completed')], 'commission_amount')
            ->orderByDesc('listings_count')
            ->paginate(20)
            ->withQueryString();

        return view('admin.marketplace.reports.sellers', compact('sellers'));
    }

    public function categories(Request $request): View
    {
        $this->ensurePermission($request);

        $categories = MarketplaceCategory::query()
            ->withCount('listings as listings_count')
            ->withCount(['listings as approved_listings_count' => fn ($q) => $q->where('status', 'approved')])
            ->get()
            ->map(function ($category) {
                $completedOrders = MarketplaceOrder::whereHas('listing', fn ($q) => $q->where('marketplace_category_id', $category->id))
                    ->completed();

                $category->completed_orders_count = $completedOrders->count();
                $category->total_commission = (float) $completedOrders->sum('commission_amount');

                return $category;
            })
            ->sortByDesc('listings_count')
            ->values();

        return view('admin.marketplace.reports.categories', compact('categories'));
    }

    public function export(Request $request, string $type): StreamedResponse
    {
        $this->ensurePermission($request);

        return match ($type) {
            'orders' => $this->exportOrders(),
            'sellers' => $this->exportSellers(),
            'categories' => $this->exportCategories(),
            default => abort(404),
        };
    }

    protected function exportOrders(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Listing', 'Buyer', 'Seller', 'Status', 'Final Price', 'Commission', 'Created']);

            MarketplaceOrder::with(['listing', 'buyer', 'seller'])->chunk(200, function ($chunk) use ($handle) {
                foreach ($chunk as $order) {
                    fputcsv($handle, [
                        $order->listing->title,
                        $order->buyer->full_name,
                        $order->seller->full_name,
                        $order->status,
                        $order->final_price,
                        $order->commission_amount,
                        $order->created_at->format('Y-m-d'),
                    ]);
                }
            });

            fclose($handle);
        }, 'marketplace-orders.csv', ['Content-Type' => 'text/csv']);
    }

    protected function exportSellers(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Seller', 'Email', 'Listings', 'Approved Listings', 'Completed Orders', 'Total Commission']);

            User::whereHas('marketplaceListings')
                ->withCount('marketplaceListings as listings_count')
                ->withCount(['marketplaceListings as approved_listings_count' => fn ($q) => $q->where('status', 'approved')])
                ->withCount(['marketplaceOrdersAsSeller as completed_orders_count' => fn ($q) => $q->where('status', 'completed')])
                ->withSum(['marketplaceOrdersAsSeller as total_commission' => fn ($q) => $q->where('status', 'completed')], 'commission_amount')
                ->chunk(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $seller) {
                        fputcsv($handle, [
                            $seller->full_name,
                            $seller->email,
                            $seller->listings_count,
                            $seller->approved_listings_count,
                            $seller->completed_orders_count,
                            $seller->total_commission ?? 0,
                        ]);
                    }
                });

            fclose($handle);
        }, 'marketplace-sellers.csv', ['Content-Type' => 'text/csv']);
    }

    protected function exportCategories(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Category', 'Commission %', 'Listings', 'Completed Orders', 'Total Commission']);

            MarketplaceCategory::withCount('listings as listings_count')->get()->each(function ($category) use ($handle) {
                $completedOrders = MarketplaceOrder::whereHas('listing', fn ($q) => $q->where('marketplace_category_id', $category->id))->completed();

                fputcsv($handle, [
                    $category->name,
                    $category->commission_percentage,
                    $category->listings_count,
                    $completedOrders->count(),
                    $completedOrders->sum('commission_amount'),
                ]);
            });

            fclose($handle);
        }, 'marketplace-categories.csv', ['Content-Type' => 'text/csv']);
    }
}
