<x-layouts::admin :title="'Add Story'">
    <x-breadcrumb :items="[['label' => 'Stories', 'url' => route('admin.stories.index')], ['label' => 'Add Story']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Add Story</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Create a story on behalf of a verified alumnus.</p>

    <form method="POST" action="{{ route('admin.stories.store') }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf

        <x-select
            label="Alumnus"
            name="alumni_profile_id"
            required
            :selected="old('alumni_profile_id')"
            :options="$alumniProfiles->mapWithKeys(fn ($profile) => [$profile->id => $profile->user->full_name])"
        />

        <x-input label="Story Title" name="title" :value="old('title')" required />
        <x-input label="Career Highlight" name="career_highlight" :value="old('career_highlight')" placeholder="e.g. Founder & CEO at Acme Inc." />

        <div>
            <label class="form-label">Cover Image</label>
            <input type="file" name="cover_image" accept="image/*" class="form-input">
        </div>

        <x-textarea label="Story" name="story" rows="10" required>{{ old('story') }}</x-textarea>
        <x-textarea label="Notable Achievements" name="achievements" rows="3">{{ old('achievements') }}</x-textarea>

        <x-select
            label="Status"
            name="status"
            required
            :selected="old('status', 'published')"
            :placeholder="null"
            :options="['draft' => 'Draft', 'pending_review' => 'Pending Review', 'published' => 'Published']"
        />

        <div class="flex justify-end gap-3">
            <x-button :href="route('admin.stories.index')" variant="secondary">Cancel</x-button>
            <x-button type="submit">Save Story</x-button>
        </div>
    </form>
</x-layouts::admin>
