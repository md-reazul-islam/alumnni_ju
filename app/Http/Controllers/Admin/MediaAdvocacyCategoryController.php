<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAdvocacyCategory;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MediaAdvocacyCategoryController extends Controller
{
    public const ICON_OPTIONS = [
        'megaphone', 'monitor', 'newspaper', 'camera', 'video', 'image', 'share-2',
        'facebook', 'instagram', 'whatsapp', 'linkedin', 'mail', 'printer',
        'file-text', 'mic', 'globe', 'tag', 'target', 'rocket',
    ];

    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-media-advocacy'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $categories = MediaAdvocacyCategory::withCount('orders')
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->string('search') . '%'))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $iconOptions = array_combine(self::ICON_OPTIONS, self::ICON_OPTIONS);

        return view('admin.media-advocacy.categories.index', compact('categories', 'iconOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=400,min_height=300'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);
        $data['icon'] = $data['icon'] ?: 'megaphone';
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $data['image'] = app(ImageUploadService::class)->store($request->file('image'), 'media-advocacy', ImageUploadService::MAX_LARGE);
        }

        MediaAdvocacyCategory::create($data);

        return back()->with('status', 'Category added.');
    }

    public function update(Request $request, MediaAdvocacyCategory $mediaAdvocacyCategory): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=400,min_height=300'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['icon'] = $data['icon'] ?: 'megaphone';
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($mediaAdvocacyCategory->image) {
                Storage::disk('public')->delete($mediaAdvocacyCategory->image);
            }
            $data['image'] = app(ImageUploadService::class)->store($request->file('image'), 'media-advocacy', ImageUploadService::MAX_LARGE);
        }

        $mediaAdvocacyCategory->update($data);

        return back()->with('status', 'Category updated.');
    }

    public function destroy(Request $request, MediaAdvocacyCategory $mediaAdvocacyCategory): RedirectResponse
    {
        $this->ensurePermission($request);

        abort_if($mediaAdvocacyCategory->orders()->exists(), 422, 'This category has orders on record and cannot be deleted.');

        if ($mediaAdvocacyCategory->image) {
            Storage::disk('public')->delete($mediaAdvocacyCategory->image);
        }

        $mediaAdvocacyCategory->delete();

        return back()->with('status', 'Category removed.');
    }
}
