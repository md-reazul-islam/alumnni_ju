<x-layouts::alumni :title="$post->title ?? 'Community Post'">
    <x-breadcrumb :items="[['label' => 'Community', 'url' => route('community.index')], ['label' => 'Post']]" class="mb-4" />

    @include('community.partials.post')

    <div class="card card-body mt-6">
        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Comments</h2>

        <form method="POST" action="{{ route('comments.store', ['post', $post->id]) }}" class="mt-4 flex gap-3">
            @csrf
            <x-avatar :src="auth()->user()->avatar_url" :name="auth()->user()->full_name" size="sm" />
            <div class="flex-1">
                <textarea name="body" rows="2" placeholder="Write a comment..." class="form-textarea" required></textarea>
                <div class="mt-2 flex justify-end"><x-button type="submit" size="sm">Comment</x-button></div>
            </div>
        </form>

        <div class="mt-4 space-y-4">
            @forelse ($post->comments as $comment)
                <div class="flex gap-3">
                    <x-avatar :src="$comment->user->avatar_url" :name="$comment->user->full_name" size="sm" />
                    <div class="flex-1">
                        <div class="rounded-xl bg-slate-50 p-3 dark:bg-navy-800">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $comment->user->full_name }}</p>
                            <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">{{ $comment->body }}</p>
                        </div>
                        <div class="mt-1 flex items-center gap-3 pl-1 text-xs text-slate-400">
                            <span>{{ $comment->created_at->diffForHumans() }}</span>
                            @if (auth()->id() === $comment->user_id || auth()->user()->hasPermission('moderate-comments'))
                                <form method="POST" action="{{ route('comments.destroy', $comment) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Delete comment?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Delete'}).then(r=>r.isConfirmed&&this.submit())">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="hover:text-red-600">Delete</button>
                                </form>
                            @endif
                        </div>

                        @foreach ($comment->replies as $reply)
                            <div class="mt-2 flex gap-2 pl-4">
                                <x-avatar :src="$reply->user->avatar_url" :name="$reply->user->full_name" size="xs" />
                                <div class="rounded-xl bg-slate-50 p-2.5 dark:bg-navy-800">
                                    <p class="text-xs font-semibold text-slate-900 dark:text-white">{{ $reply->user->full_name }}</p>
                                    <p class="mt-0.5 text-xs text-slate-700 dark:text-slate-300">{{ $reply->body }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400">No comments yet. Be the first to reply.</p>
            @endforelse
        </div>
    </div>
</x-layouts::alumni>
