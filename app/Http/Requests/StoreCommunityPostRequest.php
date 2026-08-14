<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommunityPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isVerified();
    }

    public function rules(): array
    {
        return [
            'category' => ['required', Rule::in(['academic', 'career', 'entrepreneurship', 'technology', 'research', 'social', 'regional'])],
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'post_type' => ['required', Rule::in(['post', 'poll', 'announcement'])],
            'poll_question' => ['required_if:post_type,poll', 'nullable', 'string', 'max:255'],
            'poll_options' => ['required_if:post_type,poll', 'nullable', 'array', 'min:2'],
            'poll_options.*' => ['nullable', 'string', 'max:150'],
            'poll_expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }
}
