<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CateringOrder;
use App\Models\Setting;
use App\Notifications\CateringOrderCancelled;
use App\Notifications\CateringOrderDelivered;
use App\Notifications\CateringOrderPriced;
use App\Notifications\CateringOrderRejected;
use App\Services\AuditLogger;
use App\Services\Catering\CateringRefundService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CateringOrderController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-catering'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $orders = CateringOrder::with(['customer', 'category'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.catering.orders.index', compact('orders'));
    }

    public function show(Request $request, CateringOrder $cateringOrder): View
    {
        $this->ensurePermission($request);

        $cateringOrder->load(['customer', 'category', 'items.foodItem', 'pricer', 'deliverer', 'canceller', 'feedback']);

        $rates = [
            'tax_percentage' => (float) Setting::get('catering', 'tax_percentage', 8),
            'vat_percentage' => (float) Setting::get('catering', 'vat_percentage', 0),
            'service_fee_percentage' => (float) Setting::get('catering', 'service_fee_percentage', 10),
        ];

        return view('admin.catering.orders.show', ['order' => $cateringOrder, 'rates' => $rates]);
    }

    public function price(Request $request, CateringOrder $cateringOrder): RedirectResponse
    {
        $this->ensurePermission($request);

        abort_unless($cateringOrder->status === CateringOrder::STATUS_SUBMITTED, 422, 'This order has already been priced or is no longer awaiting pricing.');

        $data = $request->validate([
            'prices' => ['required', 'array'],
            'prices.*' => ['required', 'numeric', 'min:0.01', 'max:99999.99'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($cateringOrder, $data, $request) {
            $locked = CateringOrder::lockForUpdate()->find($cateringOrder->id);
            abort_unless($locked->status === CateringOrder::STATUS_SUBMITTED, 422, 'This order has already been priced.');

            $subtotal = 0;

            foreach ($locked->items as $item) {
                $unitPrice = (float) ($data['prices'][$item->id] ?? $item->unit_price);
                abort_if($unitPrice <= 0, 422, "Every item needs a price greater than zero.");

                $lineTotal = round($unitPrice * $item->quantity, 2);
                $item->update(['unit_price' => $unitPrice, 'line_total' => $lineTotal]);
                $subtotal += $lineTotal;
            }

            $taxPct = (float) Setting::get('catering', 'tax_percentage', 8);
            $vatPct = (float) Setting::get('catering', 'vat_percentage', 0);
            $serviceFeePct = (float) Setting::get('catering', 'service_fee_percentage', 10);

            $taxAmount = round($subtotal * $taxPct / 100, 2);
            $vatAmount = round($subtotal * $vatPct / 100, 2);
            $serviceFeeAmount = round($subtotal * $serviceFeePct / 100, 2);
            $totalAmount = round($subtotal + $taxAmount + $vatAmount + $serviceFeeAmount, 2);

            $locked->update([
                'status' => CateringOrder::STATUS_PRICED,
                'subtotal' => $subtotal,
                'tax_percentage_snapshot' => $taxPct,
                'tax_amount' => $taxAmount,
                'vat_percentage_snapshot' => $vatPct,
                'vat_amount' => $vatAmount,
                'service_fee_percentage_snapshot' => $serviceFeePct,
                'service_fee_amount' => $serviceFeeAmount,
                'total_amount' => $totalAmount,
                'priced_by' => $request->user()->id,
                'priced_at' => now(),
                'admin_notes' => $data['admin_notes'] ?? $locked->admin_notes,
            ]);
        });

        $cateringOrder->refresh();
        $cateringOrder->customer->notify(new CateringOrderPriced($cateringOrder));

        AuditLogger::log('priced_catering_order', $cateringOrder, "Priced catering order #{$cateringOrder->id} at \${$cateringOrder->total_amount}.");

        return redirect()->route('admin.catering.orders.show', $cateringOrder)->with('status', 'Order priced and invoice sent to the customer.');
    }

    public function reject(Request $request, CateringOrder $cateringOrder): RedirectResponse
    {
        $this->ensurePermission($request);

        abort_unless($cateringOrder->status === CateringOrder::STATUS_SUBMITTED, 422, 'Only a newly submitted order can be rejected before pricing.');

        $data = $request->validate(['cancellation_reason' => ['required', 'string', 'max:1000']]);

        $cateringOrder->update([
            'status' => CateringOrder::STATUS_CANCELLED,
            'cancelled_by' => $request->user()->id,
            'cancellation_reason' => $data['cancellation_reason'],
            'cancelled_at' => now(),
        ]);

        $cateringOrder->customer->notify(new CateringOrderRejected($cateringOrder));

        AuditLogger::log('rejected_catering_order', $cateringOrder, "Rejected catering order #{$cateringOrder->id} before pricing. Reason: {$data['cancellation_reason']}");

        return redirect()->route('admin.catering.orders.index')->with('status', 'Order rejected.');
    }

    public function markDelivered(Request $request, CateringOrder $cateringOrder): RedirectResponse
    {
        $this->ensurePermission($request);

        abort_unless($cateringOrder->status === CateringOrder::STATUS_ACCEPTED, 422, 'Only an accepted order can be marked delivered.');

        $cateringOrder->update([
            'status' => CateringOrder::STATUS_DELIVERED,
            'delivered_by' => $request->user()->id,
            'delivered_at' => now(),
        ]);

        $cateringOrder->customer->notify(new CateringOrderDelivered($cateringOrder));

        AuditLogger::log('delivered_catering_order', $cateringOrder, "Marked catering order #{$cateringOrder->id} delivered.");

        return back()->with('status', 'Order marked delivered.');
    }

    public function cancel(Request $request, CateringOrder $cateringOrder): RedirectResponse
    {
        $this->ensurePermission($request);

        abort_unless($cateringOrder->status === CateringOrder::STATUS_ACCEPTED, 422, 'Only an accepted order can be cancelled here.');

        $data = $request->validate(['cancellation_reason' => ['required', 'string', 'max:1000']]);

        // A driver-side (here: admin-side) cancellation of a paid order always force-refunds the
        // customer. Refund with Stripe BEFORE touching our own records, same ordering as the
        // customer-initiated cancellation path.
        if ($cateringOrder->payment_status === CateringOrder::PAYMENT_PAID) {
            app(CateringRefundService::class)->refund($cateringOrder);
        }

        $cateringOrder->update([
            'status' => CateringOrder::STATUS_CANCELLED,
            'cancelled_by' => $request->user()->id,
            'cancellation_reason' => $data['cancellation_reason'],
            'cancelled_at' => now(),
        ]);

        $cateringOrder->customer->notify(new CateringOrderCancelled($cateringOrder, cancelledByRole: 'admin'));

        AuditLogger::log('admin_cancelled_paid_catering_order', $cateringOrder, "Admin cancelled catering order #{$cateringOrder->id} and refunded the customer. Reason: {$data['cancellation_reason']}");

        return redirect()->route('admin.catering.orders.index')->with('status', 'Order cancelled and customer refunded.');
    }
}
