<?php

namespace App\Http\Controllers;

use App\Models\MatrimonyProfile;
use App\Models\MatrimonyProfilePhoto;
use App\Services\MatrimonyProfileCompletionCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MatrimonyPhotoController extends Controller
{
    protected const MAX_PHOTOS = 6;

    public function index(MatrimonyProfile $profile): View
    {
        $this->authorize('update', $profile);

        return view('matrimony.profiles.photos', compact('profile'));
    }

    public function store(Request $request, MatrimonyProfile $profile): RedirectResponse
    {
        $this->authorize('update', $profile);

        $remaining = self::MAX_PHOTOS - $profile->photos()->count();
        abort_if($remaining <= 0, 422, 'You can upload up to ' . self::MAX_PHOTOS . ' photos.');

        $data = $request->validate([
            'photos' => ['required', 'array', 'min:1', 'max:' . $remaining],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $nextSort = (int) $profile->photos()->max('sort_order') + 1;
        $hasPrimary = $profile->photos()->where('is_primary', true)->exists();

        foreach ($data['photos'] as $index => $file) {
            $profile->photos()->create([
                'path' => $file->store('matrimony', 'public'),
                'is_primary' => ! $hasPrimary && $index === 0,
                'sort_order' => $nextSort + $index,
            ]);
        }

        app(MatrimonyProfileCompletionCalculator::class)->refresh($profile);

        return back()->with('status', 'Photo(s) uploaded.');
    }

    public function setPrimary(MatrimonyProfilePhoto $photo): RedirectResponse
    {
        $this->authorize('update', $photo->profile);

        $photo->profile->photos()->update(['is_primary' => false]);
        $photo->update(['is_primary' => true]);

        return back()->with('status', 'Primary photo updated.');
    }

    public function destroy(MatrimonyProfilePhoto $photo): RedirectResponse
    {
        $this->authorize('update', $photo->profile);

        $profile = $photo->profile;
        $wasPrimary = $photo->is_primary;

        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        if ($wasPrimary) {
            $profile->photos()->orderBy('sort_order')->first()?->update(['is_primary' => true]);
        }

        app(MatrimonyProfileCompletionCalculator::class)->refresh($profile);

        return back()->with('status', 'Photo removed.');
    }
}
