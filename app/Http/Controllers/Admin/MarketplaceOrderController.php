<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceOrder;
use App\Models\User;
use App\Notifications\MarketplaceOrderStatusChanged;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MarketplaceOrderController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-marketplace'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $orders = MarketplaceOrder::with(['listing.category', 'buyer', 'seller'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.marketplace.orders.index', compact('orders'));
    }

    public function show(Request $request, MarketplaceOrder $marketplaceOrder): View
    {
        $this->ensurePermission($request);

        $marketplaceOrder->load(['listing.category', 'buyer', 'seller', 'handler', 'buyerConversation', 'sellerConversation']);

        return view('admin.marketplace.orders.show', ['order' => $marketplaceOrder]);
    }

    public function updateStatus(Request $request, MarketplaceOrder $marketplaceOrder): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'ongoing', 'completed', 'cancelled'])],
            'final_price' => ['nullable', 'numeric', 'min:0'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $update = [
            'status' => $data['status'],
            'handled_by' => $request->user()->id,
            'admin_notes' => $data['admin_notes'] ?? $marketplaceOrder->admin_notes,
        ];

        if ($data['status'] === MarketplaceOrder::STATUS_COMPLETED) {
            $finalPrice = $data['final_price'] ?? $marketplaceOrder->listing->price;
            $commissionPercentage = $marketplaceOrder->listing->category->commission_percentage;

            $update['final_price'] = $finalPrice;
            $update['commission_percentage_snapshot'] = $commissionPercentage;
            $update['commission_amount'] = round($finalPrice * $commissionPercentage / 100, 2);
        }

        $marketplaceOrder->update($update);

        $marketplaceOrder->buyer->notify(new MarketplaceOrderStatusChanged($marketplaceOrder));

        AuditLogger::log('marketplace_order_status_changed', $marketplaceOrder, "Marked order #{$marketplaceOrder->id} as {$data['status']}.");

        return back()->with('status', 'Order updated.');
    }

    public function converse(Request $request, MarketplaceOrder $marketplaceOrder, string $role): RedirectResponse
    {
        $this->ensurePermission($request);
        abort_unless(in_array($role, ['buyer', 'seller'], true), 404);

        $relation = $role === 'buyer' ? 'buyerConversation' : 'sellerConversation';
        $conversation = $marketplaceOrder->{$relation};

        if (! $conversation) {
            $otherUserId = $role === 'buyer' ? $marketplaceOrder->buyer_id : $marketplaceOrder->seller_id;
            $adminIds = User::withPermission('manage-marketplace')->pluck('id');

            $conversation = $marketplaceOrder->{$relation}()->create(['context' => $role]);
            $conversation->participants()->attach([...$adminIds->all(), $otherUserId]);
        }

        return redirect()->route('admin.messages.index', $conversation);
    }
}
