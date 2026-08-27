<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCateringHomemadeListingRequest;
use App\Http\Requests\UpdateCateringHomemadeListingRequest;
use App\Models\CateringHomemadeCategory;
use App\Models\CateringHomemadeListing;
use App\Models\CateringHomemadeOrder;
use App\Models\User;
use App\Notifications\NewMessageReceived;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CateringHomemadeController extends Controller
{
    public function index(): View
    {
        $categories = CateringHomemadeCategory::active()->orderBy('name')->get();

        $listings = CateringHomemadeListing::approved()
            ->with(['category', 'images'])
            ->latest('approved_at')
            ->get();

        return view('public.catering.homemade.index', compact('listings', 'categories'));
    }

    public function show(Request $request, CateringHomemadeListing $homemadeListing): View
    {
        abort_unless($homemadeListing->status === CateringHomemadeListing::STATUS_APPROVED, 404);

        $homemadeListing->load(['category', 'images', 'seller']);
        $homemadeListing->increment('views_count');

        $hasActiveInquiry = $request->user()
            ? $homemadeListing->orders()->where('buyer_id', $request->user()->id)->whereIn('status', ['pending', 'ongoing'])->exists()
            : false;

        return view('public.catering.homemade.show', ['listing' => $homemadeListing, 'hasActiveInquiry' => $hasActiveInquiry]);
    }

    public function inquire(Request $request, CateringHomemadeListing $homemadeListing): RedirectResponse
    {
        abort_unless($homemadeListing->status === CateringHomemadeListing::STATUS_APPROVED, 404);

        $buyer = $request->user();
        abort_if($buyer->id === $homemadeListing->user_id, 422, "You can't order your own listing.");

        $data = $request->validate(['quantity' => ['nullable', 'integer', 'min:1', 'max:1000']]);

        $order = CateringHomemadeOrder::where('catering_homemade_listing_id', $homemadeListing->id)
            ->where('buyer_id', $buyer->id)
            ->whereIn('status', [CateringHomemadeOrder::STATUS_PENDING, CateringHomemadeOrder::STATUS_ONGOING])
            ->first();

        if (! $order) {
            $order = CateringHomemadeOrder::create([
                'catering_homemade_listing_id' => $homemadeListing->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $homemadeListing->user_id,
                'quantity' => $data['quantity'] ?? 1,
                'status' => CateringHomemadeOrder::STATUS_PENDING,
            ]);
        }

        $conversation = $order->buyerConversation;

        if (! $conversation) {
            $conversation = $order->buyerConversation()->create(['context' => 'buyer']);
            $adminIds = User::withPermission('manage-catering')->pluck('id');
            $conversation->participants()->attach([...$adminIds->all(), $buyer->id]);

            $message = $conversation->messages()->create([
                'user_id' => $buyer->id,
                'body' => "I'm interested in this home made food: " . route('catering.homemade.show', $homemadeListing),
            ]);
            $conversation->update(['last_message_at' => now()]);

            $conversation->participants()->where('users.id', '!=', $buyer->id)->get()
                ->each(fn ($recipient) => $recipient->notify(new NewMessageReceived($message->load('sender'))));
        }

        return redirect()->route('messages.index', $conversation)
            ->with('status', 'Your order inquiry has been sent to our team. We\'ll be in touch shortly.');
    }

    public function create(): View
    {
        $this->authorize('create', CateringHomemadeListing::class);

        $categories = CateringHomemadeCategory::active()->orderBy('name')->get();

        return view('alumni.catering.homemade.create', compact('categories'));
    }

    public function store(StoreCateringHomemadeListingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $images = $data['images'];
        unset($data['images']);

        $data['user_id'] = $request->user()->id;
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['status'] = CateringHomemadeListing::STATUS_PENDING;

        $listing = CateringHomemadeListing::create($data);

        $this->storeImages($listing, $images);

        return redirect()->route('catering.homemade.mine')->with('status', 'Your listing has been submitted for review.');
    }

    public function mine(): View
    {
        $listings = auth()->user()->cateringHomemadeListings()
            ->withCount([
                'orders as pending_orders_count' => fn ($q) => $q->where('status', 'pending'),
                'orders as ongoing_orders_count' => fn ($q) => $q->where('status', 'ongoing'),
                'orders as completed_orders_count' => fn ($q) => $q->where('status', 'completed'),
            ])
            ->with('category')
            ->latest()
            ->paginate(12);

        return view('alumni.catering.homemade.mine', compact('listings'));
    }

    public function edit(CateringHomemadeListing $homemadeListing): View
    {
        $this->authorize('update', $homemadeListing);

        $categories = CateringHomemadeCategory::active()->orderBy('name')->get();
        $homemadeListing->load('images');

        return view('alumni.catering.homemade.edit', ['listing' => $homemadeListing, 'categories' => $categories]);
    }

    public function update(UpdateCateringHomemadeListingRequest $request, CateringHomemadeListing $homemadeListing): RedirectResponse
    {
        $data = $request->validated();
        $images = $data['images'] ?? [];
        unset($data['images']);

        $wasApproved = $homemadeListing->status === CateringHomemadeListing::STATUS_APPROVED;

        $data['status'] = CateringHomemadeListing::STATUS_PENDING;
        $data['approved_by'] = null;
        $data['approved_at'] = null;

        $homemadeListing->update($data);

        if ($request->filled('remove_images')) {
            $toRemove = $homemadeListing->images()->whereIn('id', $request->input('remove_images'))->get();
            $toRemove->each(function ($image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            });
        }

        $this->storeImages($homemadeListing, $images);

        if ($wasApproved) {
            Cache::forget('homepage.content');
        }

        return redirect()->route('catering.homemade.mine')->with('status', 'Your changes have been submitted for review.');
    }

    public function destroy(CateringHomemadeListing $homemadeListing): RedirectResponse
    {
        $this->authorize('delete', $homemadeListing);

        $wasApproved = $homemadeListing->status === CateringHomemadeListing::STATUS_APPROVED;

        $homemadeListing->images->each(fn ($image) => Storage::disk('public')->delete($image->path));
        $homemadeListing->delete();

        if ($wasApproved) {
            Cache::forget('homepage.content');
        }

        return redirect()->route('catering.homemade.mine')->with('status', 'Listing deleted.');
    }

    protected function storeImages(CateringHomemadeListing $listing, array $images): void
    {
        $nextSort = $listing->images()->max('sort_order') + 1;
        $imageUploadService = app(ImageUploadService::class);

        foreach ($images as $index => $file) {
            $listing->images()->create([
                'path' => $imageUploadService->store($file, 'catering-homemade', ImageUploadService::MAX_LARGE),
                'sort_order' => $nextSort + $index,
            ]);
        }
    }

    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 1;

        while (CateringHomemadeListing::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}
