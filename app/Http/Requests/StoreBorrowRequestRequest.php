<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBorrowRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isVerified();
    }

    public function rules(): array
    {
        return [
            'duration_months' => ['required', Rule::in([1, 2, 3, 6, 10, 12])],
        ];
    }
}
