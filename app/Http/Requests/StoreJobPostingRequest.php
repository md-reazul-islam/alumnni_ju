<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobPostingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\JobPosting::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:150'],
            'employment_type' => ['required', Rule::in(['full_time', 'part_time', 'internship', 'remote', 'contract', 'freelance'])],
            'industry' => ['nullable', 'string', 'max:150'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'salary_currency' => ['nullable', 'string', 'max:3'],
            'description' => ['required', 'string', 'max:5000'],
            'requirements' => ['nullable', 'string', 'max:3000'],
            'application_url' => ['nullable', 'url', 'max:255'],
            'application_email' => ['nullable', 'email', 'max:255'],
            'deadline' => ['nullable', 'date', 'after:today'],
            'tags' => ['nullable', 'string', 'max:500'],
        ];
    }
}
