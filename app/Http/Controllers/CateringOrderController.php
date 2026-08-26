<?php

namespace App\Http\Controllers;

use App\Models\CateringFoodItem;
use App\Models\CateringOrder;
use App\Models\CateringProgramCategory;
use App\Models\User;
use App\Notifications\CateringOrderDeclined;
use App\Notifications\CateringOrderSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class CateringOrderController extends Controller
{
    public function create(Request $request): View
    {
        $categories = CateringProgramCategory::active()
            ->with(['foodItems' => fn ($q) => $q->active()->orderBy('name')])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        abort_if($categories->isEmpty(), 422, 'No catering categories are available yet.');

        $selectedCategoryId = $request->integer('category') ?: $categories->first()->id;

        return view('catering.orders.create', compact('categories', 'selectedCategoryId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'catering_program_category_id' => ['required', 'exists:catering_program_categories,id'],
            'event_date' => ['required', 'date', 'after_or_equal:today'],
            'guest_count' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'delivery_address' => ['nullable', 'string', 'max:1000'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items_json' => ['required', 'string'],
        ]);

        $items = json_decode($data['items_json'], true);
        abort_unless(is_array($items) && count($items) > 0, 422, 'Add at least one item to your order.');

        $order = DB::transaction(function () use ($data, $items, $request) {
            $order = CateringOrder::create([
                'customer_id' => $request->user()->id,
                'catering_program_category_id' => $data['catering_program_category_id'],
                'event_date' => $data['event_date'],
                'guest_count' => $data['guest_count'] ?? null,
                'delivery_address' => $data['delivery_address'] ?? null,
                'contact_phone' => $data['contact_phone'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => CateringOrder::STATUS_SUBMITTED,
            ]);

            foreach ($items as $item) {
                $quantity = max(1, (int) ($item['quantity'] ?? 1));

                if (! empty($item['food_item_id'])) {
                    $foodItem = CateringFoodItem::find($item['food_item_id']);

                    if (! $foodItem) {
                        continue;
                    }

                    $order->items()->create([
                        'catering_food_item_id' => $foodItem->id,
                        'quantity' => $quantity,
                        'unit_price' => $foodItem->base_price,
                        'line_total' => round($foodItem->base_price * $quantity, 2),
                    ]);
                } elseif (! empty($item['custom_name'])) {
                    $order->items()->create([
                        'custom_item_name' => substr($item['custom_name'], 0, 150),
                        'quantity' => $quantity,
                    ]);
                }
            }

            return $order;
        });

        if ($order->items()->count() === 0) {
            $order->delete();
            abort(422, 'Add at least one valid item to your order.');
        }

        $admins = User::withPermission('manage-catering')->get();
        Notification::send($admins, new CateringOrderSubmitted($order));

        return redirect()->route('catering.orders.show', $order)->with('status', 'Your order has been submitted for pricing.');
    }

    public function mine(Request $request): View
    {
        $orders = $request->user()->cateringOrders()
            ->with('category')
            ->latest()
            ->paginate(15);

        return view('catering.orders.mine', compact('orders'));
    }

    public function show(Request $request, CateringOrder $order): View
    {
        abort_unless($order->customer_id === $request->user()->id, 403);

        $order->load(['category', 'items.foodItem', 'feedback']);

        return view('catering.orders.show', compact('order'));
    }

    public function decline(Request $request, CateringOrder $order): RedirectResponse
    {
        abort_unless($order->customer_id === $request->user()->id, 403);
        abort_unless($order->status === CateringOrder::STATUS_PRICED, 422, 'This order is not awaiting your decision.');

        $data = $request->validate(['rejection_reason' => ['nullable', 'string', 'max:1000']]);

        $order->update([
            'status' => CateringOrder::STATUS_DECLINED,
            'rejection_reason' => $data['rejection_reason'] ?? null,
            'customer_responded_at' => now(),
        ]);

        $admins = User::withPermission('manage-catering')->get();
        Notification::send($admins, new CateringOrderDeclined($order));

        return redirect()->route('catering.orders.show', $order)->with('status', 'You declined this invoice.');
    }
}
