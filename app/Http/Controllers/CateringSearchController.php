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
}
