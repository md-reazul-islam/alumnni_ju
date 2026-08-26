@props(['books', 'compact' => false])

@if ($compact)
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-6">
        @foreach ($books as $book)
            <a href="{{ route('library.show', $book) }}" class="card overflow-hidden transition hover:shadow-popover">
                <div class="flex h-20 items-center justify-center bg-gold-50 text-gold-500 dark:bg-navy-800 sm:h-24">
                    @if ($book->cover_url)
                        <img src="{{ $book->cover_url }}" class="h-full w-full object-cover" alt="{{ $book->title }}">
                    @else
                        <x-icon name="book-open" class="h-6 w-6" />
                    @endif
                </div>
                <div class="p-2.5">
                    <p class="w-full truncate text-xs font-semibold text-slate-900 dark:text-white sm:text-sm">{{ $book->title }}</p>
                    <p class="mt-1 w-full truncate text-[10px] text-slate-500 dark:text-slate-400 sm:text-xs">{{ $book->author ?: 'Donated by ' . $book->donor->full_name }}</p>
                </div>
            </a>
        @endforeach
    </div>
@else
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        @foreach ($books as $book)
            <a href="{{ route('library.show', $book) }}" class="card overflow-hidden transition hover:shadow-popover">
                <div class="flex h-40 items-center justify-center bg-gold-50 text-gold-500 dark:bg-navy-800">
                    @if ($book->cover_url)
                        <img src="{{ $book->cover_url }}" class="h-full w-full object-cover" alt="{{ $book->title }}">
                    @else
                        <x-icon name="book-open" class="h-10 w-10" />
                    @endif
                </div>
                <div class="card-body">
                    <p class="line-clamp-2 font-semibold text-slate-900 dark:text-white">{{ $book->title }}</p>
                    @if ($book->author)
                        <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">{{ $book->author }}</p>
                    @endif
                    <p class="mt-1.5 text-xs font-medium text-navy-600 dark:text-navy-300">Donated by {{ $book->donor->full_name }}</p>
                </div>
            </a>
        @endforeach
    </div>
@endif
