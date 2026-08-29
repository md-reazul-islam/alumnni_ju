<?php

namespace App\Http\Controllers;

use App\Models\MediaAdvocacyCategory;
use App\Models\MediaAdvocacyOrder;
use App\Models\PublishedMedia;
use App\Models\User;
use App\Notifications\NewMessageReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaAdvocacyController extends Controller
{
    public function index(Request $request): View
    {
        $categories = MediaAdvocacyCategory::active()
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->string('q');
                $q->where(fn ($w) => $w->where('name', 'like', "%{$term}%")->orWhere('description', 'like', "%{$term}%"));
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('public.media-advocacy.index', compact('categories'));
    }

    public function published(): View
    {
        $media = PublishedMedia::active()->orderBy('sort_order')->latest()->paginate(15);

        return view('public.media-advocacy.published', compact('media'));
    }

    public function inquire(Request $request, MediaAdvocacyCategory $mediaAdvocacyCategory): RedirectResponse
    {
        abort_unless($mediaAdvocacyCategory->is_active, 404);

        $customer = $request->user();

        $order = MediaAdvocacyOrder::where('media_advocacy_category_id', $mediaAdvocacyCategory->id)
            ->where('customer_id', $customer->id)
            ->whereIn('status', [MediaAdvocacyOrder::STATUS_PENDING, MediaAdvocacyOrder::STATUS_CONFIRMED])
            ->first();

        if (! $order) {
            $order = MediaAdvocacyOrder::create([
                'customer_id' => $customer->id,
                'media_advocacy_category_id' => $mediaAdvocacyCategory->id,
                'status' => MediaAdvocacyOrder::STATUS_PENDING,
            ]);
        }

        $conversation = $order->buyerConversation;

        if (! $conversation) {
            $conversation = $order->buyerConversation()->create(['context' => 'buyer']);
            $adminIds = User::withPermission('manage-media-advocacy')->pluck('id');
            $conversation->participants()->attach([...$adminIds->all(), $customer->id]);

            $message = $conversation->messages()->create([
                'user_id' => $customer->id,
                'body' => "I'm interested in this media advocacy service: {$mediaAdvocacyCategory->name}",
            ]);
            $conversation->update(['last_message_at' => now()]);

            $conversation->participants()->where('users.id', '!=', $customer->id)->get()
                ->each(fn ($recipient) => $recipient->notify(new NewMessageReceived($message->load('sender'))));
        }

        return redirect()->route('messages.index', $conversation)
            ->with('status', 'Your request has been sent to our team. We\'ll be in touch shortly.');
    }
}
