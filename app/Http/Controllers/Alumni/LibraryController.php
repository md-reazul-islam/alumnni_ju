<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\StoreBorrowRequestRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\BorrowRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LibraryController extends Controller
{
    public function create(): View
    {
        $this->authorize('create', Book::class);

        return view('alumni.library.create');
    }

    public function store(StoreBookRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['donor_id'] = $request->user()->id;
        $data['status'] = Book::STATUS_PENDING;

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('library', 'public');
        }

        Book::create($data);

        return redirect()->route('library.donations')->with('status', 'Your book has been submitted for review.');
    }

    public function donations(Request $request): View
    {
        $books = $request->user()->donatedBooks()
            ->with(['borrowRequests' => fn ($q) => $q->with('borrower')->latest()])
            ->latest()
            ->paginate(12);

        return view('alumni.library.donations', compact('books'));
    }

    public function edit(Book $book): View
    {
        $this->authorize('update', $book);

        return view('alumni.library.edit', ['book' => $book]);
    }

    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cover')) {
            if ($book->cover) {
                Storage::disk('public')->delete($book->cover);
            }
            $data['cover'] = $request->file('cover')->store('library', 'public');
        }

        $data['status'] = Book::STATUS_PENDING;
        $data['approved_by'] = null;
        $data['approved_at'] = null;

        $book->update($data);

        return redirect()->route('library.donations')->with('status', 'Your changes have been submitted for review.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $this->authorize('delete', $book);

        if ($book->cover) {
            Storage::disk('public')->delete($book->cover);
        }
        $book->delete();

        return redirect()->route('library.donations')->with('status', 'Book removed.');
    }

    public function borrow(StoreBorrowRequestRequest $request, Book $book): RedirectResponse
    {
        abort_unless(Book::available()->whereKey($book->id)->exists(), 404);

        BorrowRequest::create([
            'book_id' => $book->id,
            'user_id' => $request->user()->id,
            'duration_months' => $request->validated()['duration_months'],
            'status' => BorrowRequest::STATUS_PENDING,
        ]);

        return redirect()->route('library.mine')->with('status', 'Your borrow request has been submitted for review.');
    }

    public function mine(Request $request): View
    {
        $borrowRequests = $request->user()->borrowRequests()
            ->with('book')
            ->latest()
            ->paginate(12);

        return view('alumni.library.mine', compact('borrowRequests'));
    }

    public function cancel(BorrowRequest $borrowRequest): RedirectResponse
    {
        abort_unless($borrowRequest->user_id === auth()->id(), 403);
        abort_unless($borrowRequest->status === BorrowRequest::STATUS_PENDING, 403);

        $borrowRequest->delete();

        return redirect()->route('library.mine')->with('status', 'Borrow request cancelled.');
    }
}
