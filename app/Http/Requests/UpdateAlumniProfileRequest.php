<?php

namespace App\Http\Requests;

use App\Models\AlumniProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAlumniProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->user()->alumniProfile);
    }

    public function rules(): array
    {
        return [
            'bio' => ['nullable', 'string', 'max:2000'],
            'job_title' => ['nullable', 'string', 'max:150'],
            'organization' => ['nullable', 'string', 'max:150'],
            'industry' => ['nullable', 'string', 'max:150'],
            'employment_type' => ['nullable', Rule::in(['full_time', 'part_time', 'self_employed', 'internship', 'unemployed', 'student'])],
            'work_location' => ['nullable', 'string', 'max:150'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'country' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'profile_visibility' => ['required', Rule::in([
                AlumniProfile::VISIBILITY_PUBLIC, AlumniProfile::VISIBILITY_ALUMNI, AlumniProfile::VISIBILITY_PRIVATE,
            ])],
            'skills' => ['nullable', 'string', 'max:1000'],
            'interests' => ['nullable', 'array'],
            'interests.*' => ['exists:interests,id'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
