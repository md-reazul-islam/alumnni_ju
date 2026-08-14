<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:1'],
            'donor_name' => ['required_if:is_anonymous,0', 'nullable', 'string', 'max:150'],
            'donor_email' => ['required', 'email', 'max:255'],
            'category' => ['required', Rule::in([
                'scholarship', 'research', 'student_support', 'infrastructure',
                'emergency_fund', 'alumni_association', 'general_fund',
            ])],
            'is_anonymous' => ['nullable', 'boolean'],
            'payment_method' => ['required', Rule::in(['card', 'bank_transfer', 'paypal', 'other'])],
        ];
    }
}
