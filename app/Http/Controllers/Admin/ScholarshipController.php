<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreScholarshipRequest;
use App\Models\Scholarship;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ScholarshipController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('manage-scholarships'), 403);

        $scholarships = Scholarship::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.scholarships.index', compact('scholarships'));
    }

    public function store(StoreScholarshipRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $data['slug'] = $this->uniqueSlug($data['name']);

        Scholarship::create($data);

        return back()->with('status', 'Scholarship created.');
    }

    public function update(StoreScholarshipRequest $request, Scholarship $scholarship): RedirectResponse
    {
        $data = $request->validated();

        if ($data['name'] !== $scholarship->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $scholarship->id);
        }

        $scholarship->update($data);

        return back()->with('status', 'Scholarship updated.');
    }

    public function destroy(Request $request, Scholarship $scholarship): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('manage-scholarships'), 403);

        $scholarship->delete();

        return back()->with('status', 'Scholarship deleted.');
    }

    protected function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (Scholarship::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}
