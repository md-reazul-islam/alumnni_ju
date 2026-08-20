<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceListing;
use App\Notifications\MarketplaceListingApproved;
use App\Notifications\MarketplaceListingRejected;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MarketplaceListingController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-marketplace'), 403);
    }

    public function pending(Request $request): View
    {
        $this->ensurePermission($request);

        $listings = MarketplaceListing::pending()->with(['category', 'seller'])->latest()->paginate(15);

        return view('admin.marketplace.listings.pending', compact('listings'));
    }

    public function approvedIndex(Request $request): View
    {
        $this->ensurePermission($request);

        $listings = MarketplaceListing::approved()->with(['category', 'seller'])->latest('approved_at')->paginate(15);

        return view('admin.marketplace.listings.approved', compact('listings'));
    }

    public function rejectedIndex(Request $request): View
    {
        $this->ensurePermission($request);

        $listings = MarketplaceListing::rejected()->with(['category', 'seller'])->latest()->paginate(15);

        return view('admin.marketplace.listings.rejected', compact('listings'));
    }

    public function show(Request $request, MarketplaceListing $marketplaceListing): View
    {
        $this->ensurePermission($request);

        $marketplaceListing->load(['category', 'seller', 'images', 'approver']);

        return view('admin.marketplace.listings.show', ['listing' => $marketplaceListing]);
    }

    public function approve(Request $request, MarketplaceListing $marketplaceListing): RedirectResponse
    {
        $this->ensurePermission($request);

        $marketplaceListing->update([
            'status' => MarketplaceListing::STATUS_APPROVED,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        $marketplaceListing->seller->notify(new MarketplaceListingApproved($marketplaceListing));

        AuditLogger::log('approved_marketplace_listing', $marketplaceListing, "Approved marketplace listing \"{$marketplaceListing->title}\".");
        Cache::forget('homepage.content');

        return back()->with('status', 'Listing approved.');
    }

    public function reject(Request $request, MarketplaceListing $marketplaceListing): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate(['rejection_reason' => ['required', 'string', 'max:1000']]);

        $marketplaceListing->update([
            'status' => MarketplaceListing::STATUS_REJECTED,
            'rejection_reason' => $data['rejection_reason'],
            'approved_by' => null,
            'approved_at' => null,
        ]);

        $marketplaceListing->seller->notify(new MarketplaceListingRejected($marketplaceListing));

        AuditLogger::log('rejected_marketplace_listing', $marketplaceListing, "Rejected marketplace listing \"{$marketplaceListing->title}\".");

        return redirect()->route('admin.marketplace.listings.pending')->with('status', 'Listing rejected.');
    }

    public function destroy(Request $request, MarketplaceListing $marketplaceListing): RedirectResponse
    {
        $this->ensurePermission($request);

        $wasApproved = $marketplaceListing->status === MarketplaceListing::STATUS_APPROVED;

        AuditLogger::log('deleted_marketplace_listing', $marketplaceListing, "Deleted marketplace listing \"{$marketplaceListing->title}\".");

        $marketplaceListing->images->each(fn ($image) => Storage::disk('public')->delete($image->path));
        $marketplaceListing->delete();

        if ($wasApproved) {
            Cache::forget('homepage.content');
        }

        return back()->with('status', 'Listing deleted.');
    }
}
