<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('manage-stories');
    }

    public function rules(): array
    {
        return [
            'alumni_profile_id' => ['required', 'exists:alumni_profiles,id'],
            'title' => ['required', 'string', 'max:255'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'story' => ['required', 'string', 'max:10000'],
            'achievements' => ['nullable', 'string', 'max:2000'],
            'career_highlight' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['draft', 'pending_review', 'published'])],
        ];
    }
}
