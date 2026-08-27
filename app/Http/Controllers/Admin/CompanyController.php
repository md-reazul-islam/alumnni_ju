<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('manage-jobs'), 403);

        $companies = Company::withCount('jobPostings')
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->string('search') . '%'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.companies.index', compact('companies'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('manage-jobs'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'website' => ['nullable', 'url', 'max:255'],
            'industry' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);

        if ($request->hasFile('logo')) {
            $data['logo'] = app(ImageUploadService::class)->store($request->file('logo'), 'companies', ImageUploadService::MAX_SMALL);
        }

        Company::create($data);

        return back()->with('status', 'Company added.');
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('manage-jobs'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'website' => ['nullable', 'url', 'max:255'],
            'industry' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $company->update($data);

        return back()->with('status', 'Company updated.');
    }

    public function destroy(Request $request, Company $company): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('manage-jobs'), 403);

        $company->delete();

        return back()->with('status', 'Company removed.');
    }
}
