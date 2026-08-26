<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CateringHomemadeOrder;
use App\Models\User;
use App\Notifications\CateringHomemadeOrderStatusChanged;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CateringHomemadeOrderController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-catering'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $orders = CateringHomemadeOrder::with(['listing.category', 'buyer', 'seller'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.catering.homemade.orders.index', compact('orders'));
    }

    public function show(Request $request, CateringHomemadeOrder $homemadeOrder): View
    {
        $this->ensurePermission($request);

        $homemadeOrder->load(['listing.category', 'buyer', 'seller', 'handler', 'buyerConversation', 'sellerConversation']);

        return view('admin.catering.homemade.orders.show', ['order' => $homemadeOrder]);
    }

    public function updateStatus(Request $request, CateringHomemadeOrder $homemadeOrder): RedirectResponse
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
            'admin_notes' => $data['admin_notes'] ?? $homemadeOrder->admin_notes,
        ];

        if ($data['status'] === CateringHomemadeOrder::STATUS_COMPLETED) {
            $finalPrice = $data['final_price'] ?? round($homemadeOrder->listing->price * $homemadeOrder->quantity, 2);
            $commissionPercentage = $homemadeOrder->listing->category->commission_percentage;

            $update['final_price'] = $finalPrice;
            $update['commission_percentage_snapshot'] = $commissionPercentage;
            $update['commission_amount'] = round($finalPrice * $commissionPercentage / 100, 2);
        }

        $homemadeOrder->update($update);

        $homemadeOrder->buyer->notify(new CateringHomemadeOrderStatusChanged($homemadeOrder));

        AuditLogger::log('catering_homemade_order_status_changed', $homemadeOrder, "Marked home made food order #{$homemadeOrder->id} as {$data['status']}.");

        return back()->with('status', 'Order updated.');
    }

    public function converse(Request $request, CateringHomemadeOrder $homemadeOrder, string $role): RedirectResponse
    {
        $this->ensurePermission($request);
        abort_unless(in_array($role, ['buyer', 'seller'], true), 404);

        $relation = $role === 'buyer' ? 'buyerConversation' : 'sellerConversation';
        $conversation = $homemadeOrder->{$relation};

        if (! $conversation) {
            $otherUserId = $role === 'buyer' ? $homemadeOrder->buyer_id : $homemadeOrder->seller_id;
            $adminIds = User::withPermission('manage-catering')->pluck('id');

            $conversation = $homemadeOrder->{$relation}()->create(['context' => $role]);
            $conversation->participants()->attach([...$adminIds->all(), $otherUserId]);
        }

        return redirect()->route('admin.messages.index', $conversation);
    }
}
