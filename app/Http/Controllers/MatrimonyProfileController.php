<?php

namespace App\Http\Controllers;

use App\Models\MatrimonyProfile;
use App\Models\MatrimonyProfileView;
use App\Services\MatrimonyProfileCompletionCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MatrimonyProfileController extends Controller
{
    public function show(Request $request, MatrimonyProfile $profile): View
    {
        $viewer = $request->user();
        $canPreview = $viewer && ($viewer->id === $profile->created_by || $viewer->hasPermission('manage-matrimony'));

        abort_unless(($profile->status === MatrimonyProfile::STATUS_APPROVED && $profile->is_active) || $canPreview, 404);

        if (! $canPreview) {
            $profile->increment('views_count');
            MatrimonyProfileView::create(['matrimony_profile_id' => $profile->id, 'viewer_id' => $viewer?->id]);
        }

        $profile->load('photos');

        $isFavorited = $viewer && $viewer->matrimonyFavorites()->where('matrimony_profile_id', $profile->id)->exists();

        return view('public.matrimony.show', compact('profile', 'isFavorited'));
    }

    public function mine(Request $request): View
    {
        $profiles = $request->user()->matrimonyProfiles()->with('photos')->latest()->get();

        return view('matrimony.profiles.mine', compact('profiles'));
    }

    public function create(): View
    {
        return view('matrimony.profiles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;
        $data['status'] = MatrimonyProfile::STATUS_DRAFT;

        if (empty($data['display_name'])) {
            $data['display_name'] = explode(' ', trim($data['full_name']))[0];
        }

        $profile = MatrimonyProfile::create($data);
        app(MatrimonyProfileCompletionCalculator::class)->refresh($profile);

        return redirect()->route('matrimony.profiles.edit', $profile)->with('status', 'Profile created. Complete it and submit for review when ready.');
    }

    public function edit(MatrimonyProfile $profile): View
    {
        $this->authorize('update', $profile);

        return view('matrimony.profiles.edit', compact('profile'));
    }

    public function update(Request $request, MatrimonyProfile $profile): RedirectResponse
    {
        $this->authorize('update', $profile);

        $data = $this->validated($request);

        if (empty($data['display_name'])) {
            $data['display_name'] = explode(' ', trim($data['full_name']))[0];
        }

        // Editing an approved profile pulls it back to pending for re-review — the same
        // income-integrity-style rule used for carpool schedules: content admin already
        // vetted shouldn't change unreviewed.
        if ($profile->status === MatrimonyProfile::STATUS_APPROVED) {
            $data['status'] = MatrimonyProfile::STATUS_PENDING;
            $data['reviewed_by'] = null;
            $data['reviewed_at'] = null;
        }

        $profile->update($data);
        app(MatrimonyProfileCompletionCalculator::class)->refresh($profile);

        return redirect()->route('matrimony.profiles.mine')->with('status', 'Profile updated.');
    }

    public function submitForReview(Request $request, MatrimonyProfile $profile): RedirectResponse
    {
        $this->authorize('update', $profile);

        abort_unless(
            in_array($profile->status, [MatrimonyProfile::STATUS_DRAFT, MatrimonyProfile::STATUS_REJECTED], true),
            422,
            'This profile has already been submitted.'
        );

        $request->validate(['terms_accepted' => ['accepted']]);

        abort_if(
            $profile->profile_completion < 80,
            422,
            'Complete at least 80% of the profile before submitting for review. Add more details and a photo.'
        );

        $profile->update([
            'status' => MatrimonyProfile::STATUS_PENDING,
            'terms_accepted_at' => now(),
            'rejection_reason' => null,
        ]);

        return redirect()->route('matrimony.profiles.mine')->with('status', 'Profile submitted for admin review.');
    }

    public function toggleActive(MatrimonyProfile $profile): RedirectResponse
    {
        $this->authorize('update', $profile);

        $profile->update(['is_active' => ! $profile->is_active]);
        Cache::forget('homepage.content');

        return back()->with('status', $profile->is_active ? 'Profile reactivated.' : 'Profile paused — it will not appear in search until reactivated.');
    }

    public function destroy(MatrimonyProfile $profile): RedirectResponse
    {
        $this->authorize('delete', $profile);

        foreach ($profile->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }

        $profile->delete();
        Cache::forget('homepage.content');

        return redirect()->route('matrimony.profiles.mine')->with('status', 'Profile deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'managed_by_relation' => ['required', 'in:self,parent,guardian,sibling,relative'],
            'full_name' => ['required', 'string', 'max:150'],
            'display_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:' . now()->subYears(18)->format('Y-m-d')],
            'height_cm' => ['nullable', 'integer', 'min:100', 'max:250'],
            'marital_status' => ['required', 'in:never_married,divorced,widowed,separated'],
            'religion' => ['required', 'string', 'max:100'],
            'sect' => ['nullable', 'string', 'max:100'],
            'mother_tongue' => ['nullable', 'string', 'max:100'],
            'nationality' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'visa_status' => ['nullable', 'string', 'max:150'],
            'education_level' => ['required', 'string', 'max:150'],
            'education_details' => ['nullable', 'string', 'max:2000'],
            'occupation' => ['required', 'string', 'max:150'],
            'occupation_details' => ['nullable', 'string', 'max:2000'],
            'about_me' => ['nullable', 'string', 'max:3000'],
            'income_range' => ['nullable', 'string', 'max:100'],
            'physical_description' => ['nullable', 'string', 'max:1000'],
            'family_details' => ['nullable', 'string', 'max:3000'],
            'guardian_name' => ['nullable', 'string', 'max:150'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'guardian_email' => ['nullable', 'email', 'max:150'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'preferred_age_min' => ['nullable', 'integer', 'min:18', 'max:90'],
            'preferred_age_max' => ['nullable', 'integer', 'min:18', 'max:90', 'gte:preferred_age_min'],
            'preferred_country' => ['nullable', 'string', 'max:100'],
            'preferred_partner_details' => ['nullable', 'string', 'max:2000'],
            'photo_visibility' => ['required', 'in:public,private'],
        ]);
    }
}
