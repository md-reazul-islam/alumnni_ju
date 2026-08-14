<x-layouts::alumni :title="'Share Your Story'">
    <x-breadcrumb :items="[['label' => 'Alumni Stories', 'url' => route('stories.index')], ['label' => 'Share Your Story']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Share Your Story</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Inspire fellow alumni with your journey. Submissions are reviewed before publishing.</p>

    <form method="POST" action="{{ route('stories.store') }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf

        <x-input label="Story Title" name="title" :value="old('title')" required />
        <x-input label="Career Highlight" name="career_highlight" :value="old('career_highlight')" placeholder="e.g. Founder & CEO at Acme Inc." />

        <div>
            <label class="form-label">Cover Image</label>
            <input type="file" name="cover_image" accept="image/*" class="form-input">
        </div>

        <x-textarea label="Your Story" name="story" rows="10" required>{{ old('story') }}</x-textarea>
        <x-textarea label="Notable Achievements" name="achievements" rows="3">{{ old('achievements') }}</x-textarea>

        <div class="flex justify-end">
            <x-button type="submit">Submit for Review</x-button>
        </div>
    </form>
</x-layouts::alumni>
