<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAdvocacyOrder;
use App\Models\User;
use App\Notifications\MediaAdvocacyOrderStatusChanged;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MediaAdvocacyOrderController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-media-advocacy'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $orders = MediaAdvocacyOrder::with(['category', 'customer'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.media-advocacy.orders.index', compact('orders'));
    }

    public function show(Request $request, MediaAdvocacyOrder $mediaAdvocacyOrder): View
    {
        $this->ensurePermission($request);

        $mediaAdvocacyOrder->load(['category', 'customer', 'handler', 'buyerConversation']);

        return view('admin.media-advocacy.orders.show', ['order' => $mediaAdvocacyOrder]);
    }

    public function updateStatus(Request $request, MediaAdvocacyOrder $mediaAdvocacyOrder): RedirectResponse
    {
        $this->ensurePermission($request);

        $requiresPrice = in_array($request->input('status'), [MediaAdvocacyOrder::STATUS_CONFIRMED, MediaAdvocacyOrder::STATUS_COMPLETED], true)
            && $mediaAdvocacyOrder->final_price === null;

        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'confirmed', 'completed', 'cancelled'])],
            'final_price' => [Rule::requiredIf($requiresPrice), 'nullable', 'numeric', 'min:0'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $update = [
            'status' => $data['status'],
            'handled_by' => $request->user()->id,
            'admin_notes' => $data['admin_notes'] ?? $mediaAdvocacyOrder->admin_notes,
        ];

        if (in_array($data['status'], [MediaAdvocacyOrder::STATUS_CONFIRMED, MediaAdvocacyOrder::STATUS_COMPLETED], true)) {
            $update['final_price'] = $data['final_price'] ?? $mediaAdvocacyOrder->final_price;
        }

        $mediaAdvocacyOrder->update($update);

        $mediaAdvocacyOrder->customer->notify(new MediaAdvocacyOrderStatusChanged($mediaAdvocacyOrder));

        AuditLogger::log('media_advocacy_order_status_changed', $mediaAdvocacyOrder, "Marked media advocacy order #{$mediaAdvocacyOrder->id} as {$data['status']}.");

        return back()->with('status', 'Order updated.');
    }

    public function converse(Request $request, MediaAdvocacyOrder $mediaAdvocacyOrder): RedirectResponse
    {
        $this->ensurePermission($request);

        $conversation = $mediaAdvocacyOrder->buyerConversation;

        if (! $conversation) {
            $adminIds = User::withPermission('manage-media-advocacy')->pluck('id');

            $conversation = $mediaAdvocacyOrder->buyerConversation()->create(['context' => 'buyer']);
            $conversation->participants()->attach([...$adminIds->all(), $mediaAdvocacyOrder->customer_id]);
        }

        return redirect()->route('messages.index', $conversation);
    }
}
