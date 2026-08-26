<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCateringHomemadeListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\CateringHomemadeListing::class);
    }

    public function rules(): array
    {
        return [
            'catering_homemade_category_id' => ['required', 'exists:catering_homemade_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0'],
            'price_unit' => ['required', Rule::in(['per_item', 'per_box', 'per_dozen', 'total'])],
            'images' => ['required', 'array', 'min:1', 'max:8'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'tags' => ['nullable', 'string', 'max:500'],
        ];
    }
}
