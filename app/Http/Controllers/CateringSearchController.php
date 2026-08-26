<?php

namespace App\Http\Controllers;

use App\Models\CateringFoodItem;
use App\Models\CateringProgramCategory;
use Illuminate\View\View;

class CateringSearchController extends Controller
{
    public function index(): View
    {
        $categories = CateringProgramCategory::active()
            ->with(['foodItems' => fn ($q) => $q->active()->orderBy('name')])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $foodItems = CateringFoodItem::active()->with('categories')->orderBy('name')->get();

        return view('public.catering.search', compact('categories', 'foodItems'));
    }

    public function show(CateringFoodItem $foodItem): View
    {
        abort_unless($foodItem->is_active, 404);

        $foodItem->load('categories');

        $relatedItems = CateringFoodItem::active()
            ->whereHas('categories', fn ($q) => $q->whereIn('catering_program_categories.id', $foodItem->categories->pluck('id')))
            ->where('id', '!=', $foodItem->id)
            ->with('categories')
            ->limit(4)
            ->get();

        return view('public.catering.item', compact('foodItem', 'relatedItems'));
    }
}
