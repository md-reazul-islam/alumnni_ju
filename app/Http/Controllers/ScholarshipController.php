<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use Illuminate\View\View;

class ScholarshipController extends Controller
{
    public function index(): View
    {
        $scholarships = Scholarship::open()->latest('deadline')->paginate(9);

        return view('public.scholarships.index', compact('scholarships'));
    }

    public function show(Scholarship $scholarship): View
    {
        abort_unless($scholarship->status === Scholarship::STATUS_OPEN, 404);

        return view('public.scholarships.show', compact('scholarship'));
    }
}
