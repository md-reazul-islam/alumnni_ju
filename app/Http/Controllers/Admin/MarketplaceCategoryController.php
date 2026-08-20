<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MarketplaceCategoryController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-marketplace'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $categories = MarketplaceCategory::withCount('listings')
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->string('search') . '%'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.marketplace.categories.index', compact('categories'));
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

        MarketplaceCategory::create($data);

        return back()->with('status', 'Category added.');
    }

    public function update(Request $request, MarketplaceCategory $marketplaceCategory): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'icon' => ['nullable', 'string', 'max:50'],
            'commission_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $marketplaceCategory->update($data);

        return back()->with('status', 'Category updated.');
    }

    public function destroy(Request $request, MarketplaceCategory $marketplaceCategory): RedirectResponse
    {
        $this->ensurePermission($request);

        abort_if($marketplaceCategory->listings()->exists(), 422, 'Reassign or remove listings in this category before deleting it.');

        $marketplaceCategory->delete();

        return back()->with('status', 'Category removed.');
    }
}
