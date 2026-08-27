<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGalleryPhotoRequest;
use App\Http\Requests\UpdateGalleryPhotoRequest;
use App\Models\GalleryPhoto;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function create(): View
    {
        $this->authorize('create', GalleryPhoto::class);

        return view('alumni.gallery.create');
    }

    public function store(StoreGalleryPhotoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['status'] = GalleryPhoto::STATUS_PENDING;
        $data['image'] = app(ImageUploadService::class)->store($request->file('image'), 'gallery', ImageUploadService::MAX_LARGE);

        GalleryPhoto::create($data);

        return redirect()->route('gallery.mine')->with('status', 'Your photo has been submitted for review.');
    }

    public function mine(Request $request): View
    {
        $photos = GalleryPhoto::where('user_id', $request->user()->id)->latest()->paginate(12);

        return view('alumni.gallery.mine', compact('photos'));
    }

    public function edit(GalleryPhoto $galleryPhoto): View
    {
        $this->authorize('update', $galleryPhoto);

        return view('alumni.gallery.edit', ['photo' => $galleryPhoto]);
    }

    public function update(UpdateGalleryPhotoRequest $request, GalleryPhoto $galleryPhoto): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($galleryPhoto->image);
            $data['image'] = app(ImageUploadService::class)->store($request->file('image'), 'gallery', ImageUploadService::MAX_LARGE);
        }

        // Any change to an alumni's own photo must go back through admin review.
        $data['status'] = GalleryPhoto::STATUS_PENDING;
        $data['approved_by'] = null;
        $data['approved_at'] = null;

        $galleryPhoto->update($data);

        return redirect()->route('gallery.mine')->with('status', 'Your changes have been submitted for review.');
    }

    public function destroy(GalleryPhoto $galleryPhoto): RedirectResponse
    {
        $this->authorize('delete', $galleryPhoto);

        Storage::disk('public')->delete($galleryPhoto->image);
        $galleryPhoto->delete();

        return redirect()->route('gallery.mine')->with('status', 'Photo deleted.');
    }
}
