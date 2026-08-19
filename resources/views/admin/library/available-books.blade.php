<x-layouts::admin :title="'Available Books'">
    <x-breadcrumb :items="[['label' => 'Library', 'url' => route('admin.library.index')], ['label' => 'Available Books']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Available Books</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Approved books currently free to borrow &mdash; shown in the public library.</p>

    @if ($books->isEmpty())
        <x-empty-state icon="book-open" title="No books are currently available" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Title</th><th>Author</th><th>Donor</th><th>Approved</th></tr></thead>
            <tbody>
                @foreach ($books as $book)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $book->title }}</td>
                        <td>{{ $book->author ?: '—' }}</td>
                        <td>{{ $book->donor->full_name }}</td>
                        <td>{{ $book->approved_at?->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $books->links() }}</div>
    @endif
</x-layouts::admin>
