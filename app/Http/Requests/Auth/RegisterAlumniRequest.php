<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterAlumniRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Step 1 — Personal Information
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'date_of_birth' => ['nullable', 'date', 'before:-16 years'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other', 'prefer_not_to_say'])],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'confirmed', Password::defaults()],

            // Step 2 — Academic Information
            'student_id' => ['required', 'string', 'max:50', 'unique:alumni_profiles,student_id'],
            'department_id' => ['required', 'exists:departments,id'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'degree_id' => ['required', 'exists:degrees,id'],
            'major' => ['nullable', 'string', 'max:150'],
            'admission_year' => ['required', 'integer', 'min:1950', 'max:' . now()->year],
            'graduation_year' => ['required', 'integer', 'min:1950', 'max:' . (now()->year + 10), 'gte:admission_year'],
            'campus_id' => ['nullable', 'exists:campuses,id'],
            'batch' => ['nullable', 'string', 'max:50'],

            // Step 3 — Professional Information
            'job_title' => ['nullable', 'string', 'max:150'],
            'organization' => ['nullable', 'string', 'max:150'],
            'industry' => ['nullable', 'string', 'max:150'],
            'employment_type' => ['nullable', Rule::in(['full_time', 'part_time', 'self_employed', 'internship', 'unemployed', 'student'])],
            'work_location' => ['nullable', 'string', 'max:150'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],

            // Step 4 — Interests
            'interests' => ['nullable', 'array'],
            'interests.*' => ['exists:interests,id'],

            // Step 5 — Confirmation
            'terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'terms.accepted' => 'You must accept the terms and conditions to register.',
            'graduation_year.gte' => 'Graduation year must be on or after your admission year.',
            'date_of_birth.before' => 'You must be at least 16 years old to register.',
        ];
    }
}
