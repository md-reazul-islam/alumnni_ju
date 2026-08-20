@php $isMine = $message->user_id === $authId; @endphp
<div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
    <div class="max-w-xs rounded-2xl px-4 py-2.5 text-sm sm:max-w-sm {{ $isMine ? 'bg-navy-800 text-white' : 'bg-white text-slate-700 shadow-soft dark:bg-navy-800 dark:text-slate-200' }}">
        <p class="whitespace-pre-line">{!! $message->body_html !!}</p>
        <p class="mt-1 text-[10px] {{ $isMine ? 'text-navy-300' : 'text-slate-400' }}">{{ $message->created_at->format('g:i A') }}</p>
    </div>
</div>
