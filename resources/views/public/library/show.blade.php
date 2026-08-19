<x-layouts::app>
  <div class="overflow-hidden rounded-3xl bg-white shadow-xl dark:bg-navy-900">
    <div class="section-container max-w-3xl py-8">
        <x-breadcrumb :items="[['label' => 'Your Library', 'url' => route('library.index')], ['label' => $book->title]]" class="mb-6" />

        <div class="card overflow-hidden sm:flex sm:items-start">
            <div class="flex h-64 items-center justify-center bg-gold-50 text-gold-500 dark:bg-navy-800 sm:h-auto sm:w-56 sm:flex-shrink-0">
                @if ($book->cover_url)
                    <img src="{{ $book->cover_url }}" class="h-full w-full object-cover" alt="{{ $book->title }}">
                @else
                    <x-icon name="book-open" class="h-14 w-14" />
                @endif
            </div>

            <div class="card-body flex-1">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $book->title }}</h1>
                @if ($book->author)
                    <p class="mt-1 text-slate-500 dark:text-slate-400">by {{ $book->author }}</p>
                @endif
                <p class="mt-2 text-sm text-navy-600 dark:text-navy-300">Donated by {{ $book->donor->full_name }}</p>

                @if ($book->description)
                    <div class="prose prose-slate mt-4 max-w-none dark:prose-invert">
                        <p class="whitespace-pre-line">{{ $book->description }}</p>
                    </div>
                @endif

                <div class="mt-6 border-t border-slate-100 pt-6 dark:border-navy-800">
                    @if (! $isAvailable)
                        <x-badge variant="warning" class="text-sm">Currently borrowed &mdash; not available</x-badge>
                    @elseif ($myPendingRequest)
                        <x-badge variant="info" class="text-sm">Your borrow request is pending review</x-badge>
                    @elseif (auth()->check())
                        <form method="POST" action="{{ route('library.borrow', $book) }}" class="flex flex-wrap items-end gap-3">
                            @csrf
                            <x-select
                                label="Borrow duration"
                                name="duration_months"
                                required
                                :placeholder="null"
                                :options="[1 => '1 month', 2 => '2 months', 3 => '3 months', 6 => '6 months', 10 => '10 months', 12 => '12 months']"
                            />
                            <x-button type="submit">Request to Borrow</x-button>
                        </form>
                    @else
                        <x-button :href="route('login')">Log In to Borrow</x-button>
                    @endif
                </div>
            </div>
        </div>
    </div>
  </div>
</x-layouts::app>
