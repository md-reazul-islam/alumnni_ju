<?php

namespace App\Http\Controllers;

use App\Models\MatrimonyProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatrimonySearchController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'gender' => ['nullable', 'in:male,female'],
            'age_min' => ['nullable', 'integer', 'min:18', 'max:90'],
            'age_max' => ['nullable', 'integer', 'min:18', 'max:90'],
            'country' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'religion' => ['nullable', 'string', 'max:100'],
            'marital_status' => ['nullable', 'in:never_married,divorced,widowed,separated'],
            'keyword' => ['nullable', 'string', 'max:150'],
        ]);

        $query = MatrimonyProfile::searchable()
            ->when($request->user(), fn ($q) => $q->where('created_by', '!=', $request->user()->id));

        if (! empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        if (! empty($filters['age_min'])) {
            $query->where('date_of_birth', '<=', now()->subYears((int) $filters['age_min'])->format('Y-m-d'));
        }

        if (! empty($filters['age_max'])) {
            $query->where('date_of_birth', '>', now()->subYears((int) $filters['age_max'] + 1)->format('Y-m-d'));
        }

        foreach (['country', 'nationality', 'religion'] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, 'like', '%' . $filters[$field] . '%');
            }
        }

        if (! empty($filters['marital_status'])) {
            $query->where('marital_status', $filters['marital_status']);
        }

        if (! empty($filters['keyword'])) {
            $keyword = '%' . $filters['keyword'] . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('occupation', 'like', $keyword)
                    ->orWhere('education_level', 'like', $keyword)
                    ->orWhere('about_me', 'like', $keyword);
            });
        }

        $profiles = $query->with('photos')->latest('id')->paginate(12)->withQueryString();

        return view('public.matrimony.search', [
            'profiles' => $profiles,
            'filters' => $filters,
        ]);
    }
}
