<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CateringProgramCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CateringCategoryController extends Controller
{
    public const ICON_OPTIONS = [
        'utensils', 'cooking-pot', 'gift', 'sun', 'sunrise', 'users', 'house', 'heart',
        'briefcase', 'graduation-cap', 'landmark', 'sparkles', 'handshake', 'flame',
        'camera', 'star', 'trophy', 'building', 'building-2', 'school',
    ];

    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-catering'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $categories = CateringProgramCategory::withCount('foodItems', 'orders')
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->string('search') . '%'))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $iconOptions = array_combine(self::ICON_OPTIONS, self::ICON_OPTIONS);

        return view('admin.catering.categories.index', compact('categories', 'iconOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);
        $data['icon'] = $data['icon'] ?: 'utensils';
        $data['sort_order'] = $data['sort_order'] ?? 0;

        CateringProgramCategory::create($data);

        return back()->with('status', 'Program category added.');
    }

    public function update(Request $request, CateringProgramCategory $cateringProgramCategory): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['icon'] = $data['icon'] ?: 'utensils';
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        $cateringProgramCategory->update($data);

        return back()->with('status', 'Program category updated.');
    }

    public function destroy(Request $request, CateringProgramCategory $cateringProgramCategory): RedirectResponse
    {
        $this->ensurePermission($request);

        abort_if($cateringProgramCategory->orders()->exists(), 422, 'This category has orders on record and cannot be deleted.');

        $cateringProgramCategory->foodItems()->detach();
        $cateringProgramCategory->delete();

        return back()->with('status', 'Program category removed.');
    }
}
