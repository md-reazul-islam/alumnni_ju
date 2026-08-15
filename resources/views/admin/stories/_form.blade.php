@php $story = $story ?? null; @endphp

<x-searchable-select
    label="Alumnus"
    name="alumni_profile_id"
    required
    placeholder="Search alumni by name…"
    :selected="old('alumni_profile_id', $story?->alumni_profile_id)"
    :options="$alumniProfiles->map(fn ($profile) => ['value' => $profile->id, 'label' => $profile->user->full_name])"
/>

<x-input label="Story Title" name="title" :value="old('title', $story?->title)" required />
<x-input label="Career Highlight" name="career_highlight" :value="old('career_highlight', $story?->career_highlight)" placeholder="e.g. Founder & CEO at Acme Inc." />

<div>
    <label class="form-label">Cover Image</label>
    <input type="file" name="cover_image" accept="image/*" class="form-input">
    @if ($story?->cover_image)
        <img src="{{ asset('storage/' . $story->cover_image) }}" class="mt-2 h-20 rounded-lg object-cover">
    @endif
</div>

<x-textarea label="Story" name="story" rows="10" required>{{ old('story', $story?->story) }}</x-textarea>
<x-textarea label="Notable Achievements" name="achievements" rows="3">{{ old('achievements', $story?->achievements) }}</x-textarea>

<x-select
    label="Status"
    name="status"
    required
    :selected="old('status', $story?->status ?? 'published')"
    :placeholder="null"
    :options="['draft' => 'Draft', 'pending_review' => 'Pending Review', 'published' => 'Published']"
/>
