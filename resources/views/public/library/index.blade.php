<x-layouts::app>
    <div>
      <div class="section-container py-8">
        <x-breadcrumb :items="[['label' => 'Your Library']]" onDark class="mb-4" />

        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">Your Library</h1>
                <p class="mt-1.5 text-navy-200">Books donated by alumni, available to borrow.</p>
            </div>
            @auth
                @if (Route::has('library.create'))
                    <x-button :href="route('library.create')" size="sm">Donate a Book</x-button>
                @endif
            @endauth
        </div>

        <form method="GET" class="mt-6">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title" class="form-input max-w-xs">
        </form>

        @if ($books->isEmpty())
            <x-empty-state icon="book-open" title="No books available right now" description="Check back soon, or be the first to donate one." class="mt-8" />
        @else
            <div class="mt-8">
                <x-book-grid :books="$books" />
            </div>

            <div class="mt-8">{{ $books->links('vendor.pagination.tailwind-dark') }}</div>
        @endif
      </div>
    </div>
</x-layouts::app>
