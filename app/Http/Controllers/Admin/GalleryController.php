<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryPhoto;
use App\Services\AuditLogger;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-gallery'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $photos = GalleryPhoto::with('user')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.gallery.index', compact('photos'));
    }

    public function create(Request $request): View
    {
        $this->ensurePermission($request);

        return view('admin.gallery.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=400,min_height=300'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $data['user_id'] = $request->user()->id;
        $data['image'] = app(ImageUploadService::class)->store($request->file('image'), 'gallery', ImageUploadService::MAX_LARGE);
        $data['status'] = GalleryPhoto::STATUS_APPROVED;
        $data['approved_by'] = $request->user()->id;
        $data['approved_at'] = now();

        $photo = GalleryPhoto::create($data);

        AuditLogger::log('created_gallery_photo', $photo, 'Added a gallery photo.');
        Cache::forget('homepage.content');

        return redirect()->route('admin.gallery.index')->with('status', 'Photo added.');
    }

    public function edit(GalleryPhoto $galleryPhoto): View
    {
        $this->authorize('update', $galleryPhoto);

        return view('admin.gallery.edit', ['photo' => $galleryPhoto]);
    }

    public function update(Request $request, GalleryPhoto $galleryPhoto): RedirectResponse
    {
        $this->authorize('update', $galleryPhoto);

        $data = $request->validate([
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=400,min_height=300'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($galleryPhoto->image);
            $data['image'] = app(ImageUploadService::class)->store($request->file('image'), 'gallery', ImageUploadService::MAX_LARGE);
        }

        $galleryPhoto->update($data);

        AuditLogger::log('updated_gallery_photo', $galleryPhoto, 'Updated a gallery photo.');
        Cache::forget('homepage.content');

        return redirect()->route('admin.gallery.index')->with('status', 'Photo updated.');
    }

    public function approve(Request $request, GalleryPhoto $galleryPhoto): RedirectResponse
    {
        $this->authorize('review', $galleryPhoto);

        $galleryPhoto->update([
            'status' => GalleryPhoto::STATUS_APPROVED,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        AuditLogger::log('approved_gallery_photo', $galleryPhoto, 'Approved a gallery photo.');
        Cache::forget('homepage.content');

        return back()->with('status', 'Photo approved.');
    }

    public function reject(Request $request, GalleryPhoto $galleryPhoto): RedirectResponse
    {
        $this->authorize('review', $galleryPhoto);

        $galleryPhoto->update(['status' => GalleryPhoto::STATUS_REJECTED]);

        AuditLogger::log('rejected_gallery_photo', $galleryPhoto, 'Rejected a gallery photo.');

        return back()->with('status', 'Photo rejected.');
    }

    public function destroy(GalleryPhoto $galleryPhoto): RedirectResponse
    {
        $this->authorize('delete', $galleryPhoto);

        Storage::disk('public')->delete($galleryPhoto->image);
        $galleryPhoto->delete();

        Cache::forget('homepage.content');

        return back()->with('status', 'Photo deleted.');
    }
}
