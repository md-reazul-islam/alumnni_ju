<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Certification;
use App\Models\Education;
use App\Models\Employment;
use App\Models\Project;
use App\Models\Publication;
use App\Services\ProfileCompletionCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileItemController extends Controller
{
    protected const TYPES = [
        'education' => Education::class,
        'employment' => Employment::class,
        'achievement' => Achievement::class,
        'certification' => Certification::class,
        'publication' => Publication::class,
        'project' => Project::class,
    ];

    public function store(Request $request, string $type, ProfileCompletionCalculator $completionCalculator): RedirectResponse
    {
        $modelClass = $this->resolveType($type);
        $profile = $request->user()->alumniProfile;

        $data = $request->validate($this->rulesFor($type));
        $data['alumni_profile_id'] = $profile->id;

        if ($type === 'employment' && ($data['is_current'] ?? false)) {
            $data['end_date'] = null;
        }

        $modelClass::create($data);

        $completionCalculator->refresh($profile);

        return back()->with('status', ucfirst($type) . ' added successfully.');
    }

    public function destroy(Request $request, string $type, int $item, ProfileCompletionCalculator $completionCalculator): RedirectResponse
    {
        $modelClass = $this->resolveType($type);
        $profile = $request->user()->alumniProfile;

        $record = $modelClass::where('alumni_profile_id', $profile->id)->findOrFail($item);
        $record->delete();

        $completionCalculator->refresh($profile);

        return back()->with('status', ucfirst($type) . ' removed.');
    }

    protected function resolveType(string $type): string
    {
        abort_unless(isset(self::TYPES[$type]), 404);

        return self::TYPES[$type];
    }

    protected function rulesFor(string $type): array
    {
        return match ($type) {
            'education' => [
                'institution' => ['required', 'string', 'max:255'],
                'degree' => ['nullable', 'string', 'max:150'],
                'field_of_study' => ['nullable', 'string', 'max:150'],
                'start_year' => ['nullable', 'integer', 'min:1950', 'max:' . (now()->year + 10)],
                'end_year' => ['nullable', 'integer', 'min:1950', 'max:' . (now()->year + 10)],
                'description' => ['nullable', 'string', 'max:1000'],
            ],
            'employment' => [
                'job_title' => ['required', 'string', 'max:150'],
                'company_name' => ['nullable', 'string', 'max:150'],
                'industry' => ['nullable', 'string', 'max:150'],
                'employment_type' => ['required', Rule::in(['full_time', 'part_time', 'internship', 'contract', 'freelance'])],
                'location' => ['nullable', 'string', 'max:150'],
                'start_date' => ['nullable', 'date'],
                'end_date' => ['nullable', 'date'],
                'is_current' => ['nullable', 'boolean'],
                'description' => ['nullable', 'string', 'max:1000'],
            ],
            'achievement' => [
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string', 'max:1000'],
                'achieved_on' => ['nullable', 'date'],
            ],
            'certification' => [
                'name' => ['required', 'string', 'max:255'],
                'issuing_organization' => ['nullable', 'string', 'max:255'],
                'issue_date' => ['nullable', 'date'],
                'expiry_date' => ['nullable', 'date'],
                'credential_id' => ['nullable', 'string', 'max:100'],
                'credential_url' => ['nullable', 'url', 'max:255'],
            ],
            'publication' => [
                'title' => ['required', 'string', 'max:255'],
                'publisher' => ['nullable', 'string', 'max:255'],
                'published_on' => ['nullable', 'date'],
                'url' => ['nullable', 'url', 'max:255'],
                'description' => ['nullable', 'string', 'max:1000'],
            ],
            'project' => [
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string', 'max:1000'],
                'url' => ['nullable', 'url', 'max:255'],
                'start_date' => ['nullable', 'date'],
                'end_date' => ['nullable', 'date'],
            ],
            default => [],
        };
    }
}
