<section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
    <div class="section-container py-5 sm:py-7">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white sm:text-3xl">Your Library</h2>
                <p class="mt-1.5 text-navy-200">Books donated by alumni, available to borrow.</p>
            </div>
            <a href="{{ route('library.index') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
                View library <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>

        @if ($library->isEmpty())
            <x-empty-state icon="book-open" title="No books available yet" class="mt-8" />
        @else
            <div class="mt-8">
                <x-book-grid :books="$library" />
            </div>
        @endif
    </div>
</section>
