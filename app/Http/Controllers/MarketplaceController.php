<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMarketplaceListingRequest;
use App\Http\Requests\UpdateMarketplaceListingRequest;
use App\Models\MarketplaceCategory;
use App\Models\MarketplaceListing;
use App\Models\MarketplaceOrder;
use App\Models\User;
use App\Notifications\NewMessageReceived;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function index(): View
    {
        $categories = MarketplaceCategory::active()->orderBy('name')->get();

        $listings = MarketplaceListing::approved()
            ->with(['category', 'images'])
            ->latest('approved_at')
            ->get();

        return view('public.marketplace.index', compact('listings', 'categories'));
    }

    public function show(Request $request, MarketplaceListing $listing): View
    {
        abort_unless($listing->status === MarketplaceListing::STATUS_APPROVED, 404);

        $listing->load(['category', 'images']);
        $listing->increment('views_count');

        $hasActiveInquiry = $request->user()
            ? $listing->orders()->where('buyer_id', $request->user()->id)->whereIn('status', ['pending', 'ongoing'])->exists()
            : false;

        return view('public.marketplace.show', compact('listing', 'hasActiveInquiry'));
    }

    public function inquire(Request $request, MarketplaceListing $listing): RedirectResponse
    {
        abort_unless($listing->status === MarketplaceListing::STATUS_APPROVED, 404);

        $buyer = $request->user();
        abort_if($buyer->id === $listing->user_id, 422, "You can't inquire about your own listing.");

        $order = MarketplaceOrder::where('marketplace_listing_id', $listing->id)
            ->where('buyer_id', $buyer->id)
            ->whereIn('status', [MarketplaceOrder::STATUS_PENDING, MarketplaceOrder::STATUS_ONGOING])
            ->first();

        if (! $order) {
            $order = MarketplaceOrder::create([
                'marketplace_listing_id' => $listing->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $listing->user_id,
                'status' => MarketplaceOrder::STATUS_PENDING,
            ]);
        }

        $conversation = $order->buyerConversation;

        if (! $conversation) {
            $conversation = $order->buyerConversation()->create(['context' => 'buyer']);
            $adminIds = User::withPermission('manage-marketplace')->pluck('id');
            $conversation->participants()->attach([...$adminIds->all(), $buyer->id]);

            $message = $conversation->messages()->create([
                'user_id' => $buyer->id,
                'body' => "I'm interested in this product: " . route('marketplace.show', $listing),
            ]);
            $conversation->update(['last_message_at' => now()]);

            $conversation->participants()->where('users.id', '!=', $buyer->id)->get()
                ->each(fn ($recipient) => $recipient->notify(new NewMessageReceived($message->load('sender'))));
        }

        return redirect()->route('messages.index', $conversation)
            ->with('status', 'Your inquiry has been sent to our team. We\'ll be in touch shortly.');
    }

    public function create(): View
    {
        $this->authorize('create', MarketplaceListing::class);

        $categories = MarketplaceCategory::active()->orderBy('name')->get();

        return view('alumni.marketplace.create', compact('categories'));
    }

    public function store(StoreMarketplaceListingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $images = $data['images'];
        $details = $this->cleanDetails($data['details'] ?? []);
        unset($data['images'], $data['details']);

        $data['user_id'] = $request->user()->id;
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['status'] = MarketplaceListing::STATUS_PENDING;
        $data['details'] = $details;

        $listing = MarketplaceListing::create($data);

        $this->storeImages($listing, $images);

        return redirect()->route('marketplace.mine')->with('status', 'Your listing has been submitted for review.');
    }

    public function mine(Request $request): View
    {
        $listings = $request->user()->marketplaceListings()
            ->withCount([
                'orders as pending_orders_count' => fn ($q) => $q->where('status', 'pending'),
                'orders as ongoing_orders_count' => fn ($q) => $q->where('status', 'ongoing'),
                'orders as completed_orders_count' => fn ($q) => $q->where('status', 'completed'),
            ])
            ->with('category')
            ->latest()
            ->paginate(12);

        return view('alumni.marketplace.mine', compact('listings'));
    }

    public function edit(MarketplaceListing $listing): View
    {
        $this->authorize('update', $listing);

        $categories = MarketplaceCategory::active()->orderBy('name')->get();
        $listing->load('images');

        return view('alumni.marketplace.edit', compact('listing', 'categories'));
    }

    public function update(UpdateMarketplaceListingRequest $request, MarketplaceListing $listing): RedirectResponse
    {
        $data = $request->validated();
        $images = $data['images'] ?? [];
        $details = $this->cleanDetails($data['details'] ?? []);
        unset($data['images'], $data['details']);

        $wasApproved = $listing->status === MarketplaceListing::STATUS_APPROVED;

        $data['details'] = $details;
        $data['status'] = MarketplaceListing::STATUS_PENDING;
        $data['approved_by'] = null;
        $data['approved_at'] = null;

        $listing->update($data);

        if ($request->filled('remove_images')) {
            $toRemove = $listing->images()->whereIn('id', $request->input('remove_images'))->get();
            $toRemove->each(function ($image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            });
        }

        $this->storeImages($listing, $images);

        if ($wasApproved) {
            Cache::forget('homepage.content');
        }

        return redirect()->route('marketplace.mine')->with('status', 'Your changes have been submitted for review.');
    }

    public function destroy(MarketplaceListing $listing): RedirectResponse
    {
        $this->authorize('delete', $listing);

        $wasApproved = $listing->status === MarketplaceListing::STATUS_APPROVED;

        $listing->images->each(fn ($image) => Storage::disk('public')->delete($image->path));
        $listing->delete();

        if ($wasApproved) {
            Cache::forget('homepage.content');
        }

        return redirect()->route('marketplace.mine')->with('status', 'Listing deleted.');
    }

    protected function storeImages(MarketplaceListing $listing, array $images): void
    {
        $nextSort = $listing->images()->max('sort_order') + 1;

        foreach ($images as $index => $file) {
            $listing->images()->create([
                'path' => app(ImageUploadService::class)->store($file, 'marketplace', ImageUploadService::MAX_LARGE),
                'sort_order' => $nextSort + $index,
            ]);
        }
    }

    protected function cleanDetails(array $details): array
    {
        return collect($details)
            ->filter(fn ($detail) => filled($detail['label'] ?? null) && filled($detail['value'] ?? null))
            ->map(fn ($detail) => ['label' => $detail['label'], 'value' => $detail['value']])
            ->values()
            ->all();
    }

    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 1;

        while (MarketplaceListing::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}
