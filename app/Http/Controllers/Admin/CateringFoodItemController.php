<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CateringFoodItem;
use App\Models\CateringProgramCategory;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CateringFoodItemController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-catering'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $items = CateringFoodItem::with('categories')
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->string('search') . '%'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $categories = CateringProgramCategory::active()->orderBy('name')->get();

        return view('admin.catering.items.index', compact('items', 'categories'));
    }

    public function create(Request $request): View
    {
        $this->ensurePermission($request);

        $categories = CateringProgramCategory::active()->orderBy('name')->get();

        return view('admin.catering.items.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $this->validated($request);
        $categoryIds = $data['category_ids'];
        unset($data['category_ids']);

        if ($request->hasFile('image')) {
            $data['image'] = app(ImageUploadService::class)->store($request->file('image'), 'catering-items', ImageUploadService::MAX_LARGE);
        }

        $item = CateringFoodItem::create($data);
        $item->categories()->sync($categoryIds);

        return redirect()->route('admin.catering.items.index')->with('status', 'Food item added.');
    }

    public function edit(Request $request, CateringFoodItem $cateringFoodItem): View
    {
        $this->ensurePermission($request);

        $categories = CateringProgramCategory::active()->orderBy('name')->get();
        $selectedCategoryIds = $cateringFoodItem->categories()->pluck('catering_program_categories.id')->all();

        return view('admin.catering.items.edit', ['item' => $cateringFoodItem, 'categories' => $categories, 'selectedCategoryIds' => $selectedCategoryIds]);
    }

    public function update(Request $request, CateringFoodItem $cateringFoodItem): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $this->validated($request);
        $categoryIds = $data['category_ids'];
        unset($data['category_ids']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($cateringFoodItem->image) {
                Storage::disk('public')->delete($cateringFoodItem->image);
            }
            $data['image'] = app(ImageUploadService::class)->store($request->file('image'), 'catering-items', ImageUploadService::MAX_LARGE);
        }

        $cateringFoodItem->update($data);
        $cateringFoodItem->categories()->sync($categoryIds);

        return redirect()->route('admin.catering.items.index')->with('status', 'Food item updated.');
    }

    public function destroy(Request $request, CateringFoodItem $cateringFoodItem): RedirectResponse
    {
        $this->ensurePermission($request);

        abort_if($cateringFoodItem->orderItems()->exists(), 422, 'This item has orders on record and cannot be deleted.');

        if ($cateringFoodItem->image) {
            Storage::disk('public')->delete($cateringFoodItem->image);
        }

        $cateringFoodItem->categories()->detach();
        $cateringFoodItem->delete();

        return back()->with('status', 'Food item removed.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'base_price' => ['required', 'numeric', 'min:0.01', 'max:99999.99'],
            'unit_label' => ['required', 'string', 'max:50'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=400,min_height=300'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['exists:catering_program_categories,id'],
        ]);
    }
}
