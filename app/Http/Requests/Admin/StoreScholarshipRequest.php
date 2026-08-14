<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScholarshipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('manage-scholarships');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'eligibility' => ['nullable', 'string', 'max:3000'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'deadline' => ['nullable', 'date'],
            'application_url' => ['nullable', 'url', 'max:255'],
            'required_documents' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['draft', 'open', 'closed'])],
        ];
    }
}
