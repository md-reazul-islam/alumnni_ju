<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Event::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'category' => ['required', Rule::in(['reunion', 'networking', 'workshop', 'seminar', 'webinar', 'career', 'sports', 'cultural', 'fundraising', 'alumni_meetup'])],
            'mode' => ['required', Rule::in(['online', 'offline'])],
            'venue' => ['nullable', 'required_if:mode,offline', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'meeting_url' => ['nullable', 'required_if:mode,online', 'url', 'max:255'],
            'event_date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'registration_deadline' => ['nullable', 'date', 'before_or_equal:event_date'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
            'organizer_name' => ['nullable', 'string', 'max:150'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', Rule::in(['draft', 'published', 'scheduled', 'cancelled'])],
        ];
    }
}
