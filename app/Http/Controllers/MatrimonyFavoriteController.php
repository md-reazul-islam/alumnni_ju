<?php

namespace App\Http\Controllers;

use App\Models\MatrimonyFavorite;
use App\Models\MatrimonyProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatrimonyFavoriteController extends Controller
{
    public function index(Request $request): View
    {
        $favorites = $request->user()->matrimonyFavorites()
            ->with('profile.photos')
            ->whereHas('profile')
            ->latest()
            ->get();

        return view('matrimony.favorites.index', compact('favorites'));
    }

    public function store(Request $request, MatrimonyProfile $profile): RedirectResponse
    {
        MatrimonyFavorite::firstOrCreate([
            'user_id' => $request->user()->id,
            'matrimony_profile_id' => $profile->id,
        ]);

        return back()->with('status', 'Added to favorites.');
    }

    public function destroy(Request $request, MatrimonyProfile $profile): RedirectResponse
    {
        MatrimonyFavorite::where('user_id', $request->user()->id)
            ->where('matrimony_profile_id', $profile->id)
            ->delete();

        return back()->with('status', 'Removed from favorites.');
    }
}
