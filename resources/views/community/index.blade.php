<x-layouts::alumni :title="'Community'">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
        <div class="lg:col-span-1">
            <div class="card card-body">
                <p class="text-sm font-semibold text-slate-900 dark:text-white">Categories</p>
                <div class="mt-3 space-y-1">
                    <a href="{{ route('community.index') }}" class="block rounded-lg px-3 py-2 text-sm {{ !request('category') ? 'bg-navy-50 font-medium text-navy-800 dark:bg-navy-800 dark:text-white' : 'text-slate-600 dark:text-slate-300' }}">All Posts</a>
                    @foreach (['academic' => 'Academic', 'career' => 'Career', 'entrepreneurship' => 'Entrepreneurship', 'technology' => 'Technology', 'research' => 'Research', 'social' => 'Social', 'regional' => 'Regional Groups'] as $value => $label)
                        <a href="{{ route('community.index', ['category' => $value]) }}" class="block rounded-lg px-3 py-2 text-sm {{ request('category') === $value ? 'bg-navy-50 font-medium text-navy-800 dark:bg-navy-800 dark:text-white' : 'text-slate-600 dark:text-slate-300' }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div x-data="{ open: false, postType: 'post' }" class="card card-body">
                <button @click="open = !open" class="flex w-full items-center gap-3 text-left">
                    <x-avatar :src="auth()->user()->avatar_url" :name="auth()->user()->full_name" size="sm" />
                    <span class="flex-1 rounded-full border border-slate-200 px-4 py-2 text-sm text-slate-400 dark:border-navy-700">Share something with the community...</span>
                </button>

                <form method="POST" action="{{ route('community.store') }}" enctype="multipart/form-data" x-show="open" x-cloak class="mt-4 space-y-4 border-t border-slate-100 pt-4 dark:border-navy-800">
                    @csrf

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <x-select label="Category" name="category" required :options="['academic' => 'Academic', 'career' => 'Career', 'entrepreneurship' => 'Entrepreneurship', 'technology' => 'Technology', 'research' => 'Research', 'social' => 'Social', 'regional' => 'Regional Groups']" placeholder="Select category" />
                        <x-select label="Post Type" name="post_type" x-model="postType" required :options="['post' => 'Discussion', 'poll' => 'Poll', 'announcement' => 'Announcement']" />
                    </div>

                    <x-input label="Title (optional)" name="title" />
                    <x-textarea label="What's on your mind?" name="body" rows="3" required />

                    <div x-show="postType === 'poll'" class="space-y-3 rounded-lg border border-slate-200 p-4 dark:border-navy-700">
                        <x-input label="Poll Question" name="poll_question" />
                        <x-input label="Option 1" name="poll_options[]" />
                        <x-input label="Option 2" name="poll_options[]" />
                        <x-input label="Option 3 (optional)" name="poll_options[]" />
                        <x-input label="Option 4 (optional)" name="poll_options[]" />
                    </div>

                    <div>
                        <label class="form-label">Image (optional)</label>
                        <input type="file" name="image" accept="image/*" class="form-input">
                    </div>

                    <div class="flex justify-end"><x-button type="submit" size="sm">Post</x-button></div>
                </form>
            </div>

            <div class="mt-6 space-y-6">
                @forelse ($posts as $post)
                    @include('community.partials.post')
                @empty
                    <x-empty-state icon="message-square" title="No posts yet" description="Be the first to start a discussion." />
                @endforelse
            </div>

            <div class="mt-6">{{ $posts->links() }}</div>
        </div>
    </div>
</x-layouts::alumni>
