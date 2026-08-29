<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublishedMedia;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublishedMediaController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-media-advocacy'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $media = PublishedMedia::when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%' . $request->string('search') . '%'))
            ->orderBy('sort_order')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.media-advocacy.published.index', compact('media'));
    }

    public function create(Request $request): View
    {
        $this->ensurePermission($request);

        return view('admin.media-advocacy.published.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $this->validated($request);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($data['type'] === PublishedMedia::TYPE_IMAGE) {
            $data['video_url'] = null;
            if ($request->hasFile('image')) {
                $data['image'] = app(ImageUploadService::class)->store($request->file('image'), 'media-advocacy-published', ImageUploadService::MAX_LARGE);
            }
        } else {
            $data['image'] = null;
        }

        PublishedMedia::create($data);

        Cache::forget('homepage.content');

        return redirect()->route('admin.media-advocacy.published.index')->with('status', 'Published media added.');
    }

    public function edit(Request $request, PublishedMedia $publishedMedium): View
    {
        $this->ensurePermission($request);

        return view('admin.media-advocacy.published.edit', ['item' => $publishedMedium]);
    }

    public function update(Request $request, PublishedMedia $publishedMedium): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($data['type'] === PublishedMedia::TYPE_IMAGE) {
            $data['video_url'] = null;
            if ($request->hasFile('image')) {
                if ($publishedMedium->image) {
                    Storage::disk('public')->delete($publishedMedium->image);
                }
                $data['image'] = app(ImageUploadService::class)->store($request->file('image'), 'media-advocacy-published', ImageUploadService::MAX_LARGE);
            }
        } else {
            if ($publishedMedium->image) {
                Storage::disk('public')->delete($publishedMedium->image);
            }
            $data['image'] = null;
        }

        $publishedMedium->update($data);

        Cache::forget('homepage.content');

        return redirect()->route('admin.media-advocacy.published.index')->with('status', 'Published media updated.');
    }

    public function destroy(Request $request, PublishedMedia $publishedMedium): RedirectResponse
    {
        $this->ensurePermission($request);

        if ($publishedMedium->image) {
            Storage::disk('public')->delete($publishedMedium->image);
        }

        $publishedMedium->delete();

        Cache::forget('homepage.content');

        return back()->with('status', 'Published media removed.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'type' => ['required', Rule::in([PublishedMedia::TYPE_IMAGE, PublishedMedia::TYPE_VIDEO])],
            'image' => [
                Rule::requiredIf(fn () => $request->input('type') === PublishedMedia::TYPE_IMAGE && ! $request->route('publishedMedium')?->image),
                'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=400,min_height=300',
            ],
            'video_url' => [
                Rule::requiredIf(fn () => $request->input('type') === PublishedMedia::TYPE_VIDEO),
                'nullable', 'url', 'max:255',
            ],
            'tag' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
