<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAlumniProfileRequest;
use App\Models\Interest;
use App\Models\Skill;
use App\Services\ImageUploadService;
use App\Services\ProfileCompletionCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        abort_unless(auth()->user()->alumniProfile, 404);

        $profile = auth()->user()->alumniProfile->load([
            'department', 'program', 'degree', 'campus', 'skills', 'interests',
            'educations', 'employments', 'achievements', 'certifications', 'publications', 'projects',
        ]);

        $interests = Interest::orderBy('name')->get();

        return view('alumni.profile.edit', compact('profile', 'interests'));
    }

    public function update(UpdateAlumniProfileRequest $request, ProfileCompletionCalculator $completionCalculator): RedirectResponse
    {
        $profile = $request->user()->alumniProfile;
        abort_unless($profile, 404);

        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($request->user()->avatar) {
                Storage::disk('public')->delete($request->user()->avatar);
            }
            $request->user()->update(['avatar' => app(ImageUploadService::class)->store($request->file('avatar'), 'avatars', ImageUploadService::MAX_SMALL)]);
        }

        if ($request->hasFile('cover_image')) {
            if ($profile->cover_image) {
                Storage::disk('public')->delete($profile->cover_image);
            }
            $data['cover_image'] = app(ImageUploadService::class)->store($request->file('cover_image'), 'covers', ImageUploadService::MAX_LARGE);
        }

        $profile->update(collect($data)->except(['skills', 'interests', 'avatar'])->toArray());

        if ($request->filled('skills')) {
            $skillIds = collect(explode(',', $request->string('skills')))
                ->map(fn ($name) => trim($name))
                ->filter()
                ->map(fn ($name) => Skill::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name])->id);

            $profile->skills()->sync($skillIds);
        }

        $profile->interests()->sync($request->input('interests', []));

        $completionCalculator->refresh($profile);

        return back()->with('status', 'Your profile has been updated.');
    }
}
