<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CateringHomemadeCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CateringHomemadeCategoryController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-catering'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $categories = CateringHomemadeCategory::withCount('listings')
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->string('search') . '%'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.catering.homemade.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'icon' => ['nullable', 'string', 'max:50'],
            'commission_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);

        CateringHomemadeCategory::create($data);

        return back()->with('status', 'Category added.');
    }

    public function update(Request $request, CateringHomemadeCategory $cateringHomemadeCategory): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'icon' => ['nullable', 'string', 'max:50'],
            'commission_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $cateringHomemadeCategory->update($data);

        return back()->with('status', 'Category updated.');
    }

    public function destroy(Request $request, CateringHomemadeCategory $cateringHomemadeCategory): RedirectResponse
    {
        $this->ensurePermission($request);

        abort_if($cateringHomemadeCategory->listings()->exists(), 422, 'Reassign or remove listings in this category before deleting it.');

        $cateringHomemadeCategory->delete();

        return back()->with('status', 'Category removed.');
    }
}
