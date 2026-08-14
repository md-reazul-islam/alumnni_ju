@php
    $isLiked = $post->relationLoaded('likes') && $post->likes->isNotEmpty();
    $totalVotes = $post->poll?->votes->count() ?? 0;
    $myVoteOptionId = $post->poll ? $post->poll->votes->firstWhere('user_id', auth()->id())?->poll_option_id : null;
@endphp

<div class="card card-body" id="post-{{ $post->id }}">
    <div class="flex items-start justify-between">
        <a href="{{ Route::has('alumni.profile.show') ? route('alumni.profile.show', $post->user) : '#' }}" class="flex items-center gap-3">
            <x-avatar :src="$post->user->avatar_url" :name="$post->user->full_name" size="sm" />
            <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $post->user->full_name }}</p>
                <p class="text-xs text-slate-400">{{ $post->created_at->diffForHumans() }} &middot; <x-badge variant="neutral">{{ ucfirst($post->category) }}</x-badge></p>
            </div>
        </a>

        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="text-slate-400 hover:text-slate-600"><x-icon name="ellipsis" class="h-4 w-4" /></button>
            <div x-show="open" x-cloak @click.outside="open = false" class="absolute right-0 z-10 mt-1 w-40 rounded-lg border border-slate-200 bg-white py-1 shadow-popover dark:border-navy-700 dark:bg-navy-900">
                @if (auth()->id() === $post->user_id || auth()->user()->hasPermission('moderate-community'))
                    <form method="POST" action="{{ route('community.destroy', $post) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Delete this post?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Delete'}).then(r=>r.isConfirmed&&this.submit())">
                        @csrf @method('DELETE')
                        <button type="submit" class="block w-full px-3 py-1.5 text-left text-sm text-red-600 hover:bg-red-50">Delete</button>
                    </form>
                @else
                    <button @click="open=false; reportContent('post', {{ $post->id }})" class="block w-full px-3 py-1.5 text-left text-sm text-slate-600 hover:bg-slate-50">Report</button>
                @endif
            </div>
        </div>
    </div>

    @if ($post->title)
        <p class="mt-3 font-semibold text-slate-900 dark:text-white">{{ $post->title }}</p>
    @endif
    <p class="mt-2 whitespace-pre-line text-sm text-slate-700 dark:text-slate-300">{{ $post->body }}</p>

    @if ($post->image_url)
        <img src="{{ $post->image_url }}" class="mt-3 max-h-96 w-full rounded-xl object-cover">
    @endif

    @if ($post->poll)
        <div class="mt-4 space-y-2 rounded-xl border border-slate-200 p-4 dark:border-navy-700">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $post->poll->question }}</p>
            @foreach ($post->poll->options as $option)
                @php $optionVotes = $option->votes->count(); $pct = $totalVotes > 0 ? round(($optionVotes / $totalVotes) * 100) : 0; @endphp
                <form method="POST" action="{{ route('polls.vote', $post->poll) }}">
                    @csrf
                    <input type="hidden" name="option_id" value="{{ $option->id }}">
                    <button type="submit" class="relative block w-full overflow-hidden rounded-lg border border-slate-200 p-2 text-left text-sm dark:border-navy-700" {{ $post->poll->hasExpired() ? 'disabled' : '' }}>
                        <span class="absolute inset-y-0 left-0 bg-navy-50 dark:bg-navy-800" style="width: {{ $pct }}%"></span>
                        <span class="relative flex justify-between">
                            <span class="{{ $myVoteOptionId === $option->id ? 'font-semibold text-navy-800 dark:text-white' : 'text-slate-600 dark:text-slate-300' }}">{{ $option->option_text }}</span>
                            <span class="text-slate-400">{{ $pct }}%</span>
                        </span>
                    </button>
                </form>
            @endforeach
            <p class="text-xs text-slate-400">{{ $totalVotes }} votes @if($post->poll->expires_at) &middot; closes {{ $post->poll->expires_at->diffForHumans() }} @endif</p>
        </div>
    @endif

    <div class="mt-4 flex items-center gap-4 border-t border-slate-100 pt-3 dark:border-navy-800">
        <button onclick="AlumniCommunity.toggleLike('post', {{ $post->id }}, this)" data-liked="{{ $isLiked ? '1' : '0' }}" class="flex items-center gap-1.5 text-sm {{ $isLiked ? 'text-red-600' : 'text-slate-500 dark:text-slate-400' }}">
            <x-icon name="heart" class="h-4 w-4" /> <span class="like-count">{{ $post->likes_count ?? $post->likes->count() }}</span>
        </button>
        <a href="{{ route('community.show', $post) }}" class="flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400">
            <x-icon name="message-circle" class="h-4 w-4" /> {{ $post->comments_count ?? $post->comments->count() }}
        </a>
    </div>
</div>
