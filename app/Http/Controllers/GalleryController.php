<?php

namespace App\Http\Controllers;

use App\Models\GalleryPhoto;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        $photos = GalleryPhoto::approved()->with('user')->latest('approved_at')->paginate(24);

        return view('public.gallery.index', compact('photos'));
    }
}
