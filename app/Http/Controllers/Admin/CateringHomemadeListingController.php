<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CateringHomemadeListing;
use App\Notifications\CateringHomemadeListingApproved;
use App\Notifications\CateringHomemadeListingRejected;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CateringHomemadeListingController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-catering'), 403);
    }

    public function pending(Request $request): View
    {
        $this->ensurePermission($request);

        $listings = CateringHomemadeListing::pending()->with(['category', 'seller'])->latest()->paginate(15);

        return view('admin.catering.homemade.listings.pending', compact('listings'));
    }

    public function approvedIndex(Request $request): View
    {
        $this->ensurePermission($request);

        $listings = CateringHomemadeListing::approved()->with(['category', 'seller'])->latest('approved_at')->paginate(15);

        return view('admin.catering.homemade.listings.approved', compact('listings'));
    }

    public function rejectedIndex(Request $request): View
    {
        $this->ensurePermission($request);

        $listings = CateringHomemadeListing::rejected()->with(['category', 'seller'])->latest()->paginate(15);

        return view('admin.catering.homemade.listings.rejected', compact('listings'));
    }

    public function show(Request $request, CateringHomemadeListing $homemadeListing): View
    {
        $this->ensurePermission($request);

        $homemadeListing->load(['category', 'seller', 'images', 'approver']);

        return view('admin.catering.homemade.listings.show', ['listing' => $homemadeListing]);
    }

    public function approve(Request $request, CateringHomemadeListing $homemadeListing): RedirectResponse
    {
        $this->ensurePermission($request);

        $homemadeListing->update([
            'status' => CateringHomemadeListing::STATUS_APPROVED,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        $homemadeListing->seller->notify(new CateringHomemadeListingApproved($homemadeListing));

        AuditLogger::log('approved_catering_homemade_listing', $homemadeListing, "Approved home made food listing \"{$homemadeListing->title}\".");
        Cache::forget('homepage.content');

        return back()->with('status', 'Listing approved.');
    }

    public function reject(Request $request, CateringHomemadeListing $homemadeListing): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate(['rejection_reason' => ['required', 'string', 'max:1000']]);

        $homemadeListing->update([
            'status' => CateringHomemadeListing::STATUS_REJECTED,
            'rejection_reason' => $data['rejection_reason'],
            'approved_by' => null,
            'approved_at' => null,
        ]);

        $homemadeListing->seller->notify(new CateringHomemadeListingRejected($homemadeListing));

        AuditLogger::log('rejected_catering_homemade_listing', $homemadeListing, "Rejected home made food listing \"{$homemadeListing->title}\".");

        return redirect()->route('admin.catering.homemade-listings.pending')->with('status', 'Listing rejected.');
    }

    public function destroy(Request $request, CateringHomemadeListing $homemadeListing): RedirectResponse
    {
        $this->ensurePermission($request);

        $wasApproved = $homemadeListing->status === CateringHomemadeListing::STATUS_APPROVED;

        AuditLogger::log('deleted_catering_homemade_listing', $homemadeListing, "Deleted home made food listing \"{$homemadeListing->title}\".");

        $homemadeListing->images->each(fn ($image) => Storage::disk('public')->delete($image->path));
        $homemadeListing->delete();

        if ($wasApproved) {
            Cache::forget('homepage.content');
        }

        return back()->with('status', 'Listing deleted.');
    }
}
