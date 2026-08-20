@php $event = $event ?? null; @endphp

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-input label="Event Title" name="title" :value="old('title', $event?->title)" required />
    </div>

    <div class="sm:col-span-2">
        <x-textarea label="Description" name="description" rows="4">{{ old('description', $event?->description) }}</x-textarea>
    </div>

    <div class="sm:col-span-2">
        <x-input label="Tags" name="tags" :value="old('tags', $event?->tags)" placeholder="e.g. reunion, networking, 2026" hint="Optional keywords to help alumni find this event in search. Separate multiple tags with commas." />
    </div>

    <div>
        <label class="form-label">Event Image</label>
        <input type="file" name="image" accept="image/*" class="form-input">
        @if ($event?->image_url)
            <img src="{{ $event->image_url }}" class="mt-2 h-20 rounded-lg object-cover">
        @endif
    </div>

    <x-select label="Category" name="category" :selected="old('category', $event?->category)" required :options="[
        'reunion' => 'Reunion', 'networking' => 'Networking', 'workshop' => 'Workshop', 'seminar' => 'Seminar',
        'webinar' => 'Webinar', 'career' => 'Career', 'sports' => 'Sports', 'cultural' => 'Cultural',
        'fundraising' => 'Fundraising', 'alumni_meetup' => 'Alumni Meetup',
    ]" />

    <div x-data="{ mode: '{{ old('mode', $event?->mode ?? 'offline') }}' }" class="contents">
        <div>
            <x-select label="Mode" name="mode" x-model="mode" :selected="old('mode', $event?->mode)" required :options="['offline' => 'Offline', 'online' => 'Online']" />
        </div>

        <div x-show="mode === 'offline'" class="sm:col-span-2 grid grid-cols-1 gap-5 sm:grid-cols-3">
            <x-input label="Venue" name="venue" :value="old('venue', $event?->venue)" />
            <x-input label="City" name="city" :value="old('city', $event?->city)" />
            <x-input label="Country" name="country" :value="old('country', $event?->country)" />
        </div>
        <div x-show="mode === 'online'" class="sm:col-span-2">
            <x-input label="Meeting URL" name="meeting_url" type="url" :value="old('meeting_url', $event?->meeting_url)" />
        </div>
    </div>

    <div>
        <label class="form-label">Event Date</label>
        <input type="text" name="event_date" value="{{ old('event_date', $event?->event_date?->format('Y-m-d')) }}" class="form-input flatpickr-date" required autocomplete="off">
    </div>
    <div>
        <label class="form-label">Registration Deadline</label>
        <input type="text" name="registration_deadline" value="{{ old('registration_deadline', $event?->registration_deadline?->format('Y-m-d H:i')) }}" class="form-input flatpickr-datetime" autocomplete="off">
    </div>

    <div>
        <label class="form-label">Start Time</label>
        <input type="text" name="start_time" value="{{ old('start_time', $event?->start_time ? \Illuminate\Support\Carbon::parse($event->start_time)->format('H:i') : '') }}" class="form-input flatpickr-time" autocomplete="off">
    </div>
    <div>
        <label class="form-label">End Time</label>
        <input type="text" name="end_time" value="{{ old('end_time', $event?->end_time ? \Illuminate\Support\Carbon::parse($event->end_time)->format('H:i') : '') }}" class="form-input flatpickr-time" autocomplete="off">
    </div>

    <x-input label="Maximum Participants" name="max_participants" type="number" min="1" :value="old('max_participants', $event?->max_participants)" />
    <x-input label="Organizer Name" name="organizer_name" :value="old('organizer_name', $event?->organizer_name)" />

    <x-input label="Contact Email" name="contact_email" type="email" :value="old('contact_email', $event?->contact_email)" />
    <x-input label="Contact Phone" name="contact_phone" :value="old('contact_phone', $event?->contact_phone)" />

    <x-select label="Status" name="status" :selected="old('status', $event?->status ?? 'draft')" required :options="[
        'draft' => 'Draft', 'published' => 'Published', 'scheduled' => 'Scheduled', 'cancelled' => 'Cancelled',
    ]" />
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        flatpickr('.flatpickr-date', { dateFormat: 'Y-m-d' });
        flatpickr('.flatpickr-datetime', { dateFormat: 'Y-m-d H:i', enableTime: true });
        flatpickr('.flatpickr-time', { enableTime: true, noCalendar: true, dateFormat: 'H:i' });
    });
</script>
@endpush
