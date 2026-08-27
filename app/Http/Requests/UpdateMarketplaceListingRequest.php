<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMarketplaceListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('listing'));
    }

    public function rules(): array
    {
        return [
            'marketplace_category_id' => ['required', 'exists:marketplace_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0'],
            'price_unit' => ['required', Rule::in(['total', 'per_month', 'per_year'])],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:150'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=400,min_height=300'],
            'details' => ['nullable', 'array'],
            'details.*.label' => ['nullable', 'string', 'max:100'],
            'details.*.value' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'string', 'max:500'],
        ];
    }
}
