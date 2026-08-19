<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BorrowRequest;
use App\Notifications\BookDonationApproved;
use App\Notifications\BorrowDueReminder;
use App\Notifications\BorrowRequestApproved;
use App\Notifications\BorrowRequestRejected;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LibraryController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-library'), 403);
    }

    // Book donation management

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $books = Book::with('donor')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.library.index', compact('books'));
    }

    public function create(Request $request): View
    {
        $this->ensurePermission($request);

        return view('admin.library.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:150'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['donor_id'] = $request->user()->id;
        $data['status'] = Book::STATUS_APPROVED;
        $data['approved_by'] = $request->user()->id;
        $data['approved_at'] = now();

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('library', 'public');
        }

        $book = Book::create($data);

        AuditLogger::log('created_book', $book, "Added a library book \"{$book->title}\".");
        Cache::forget('homepage.content');

        return redirect()->route('admin.library.index')->with('status', 'Book added.');
    }

    public function edit(Request $request, Book $book): View
    {
        $this->authorize('update', $book);

        return view('admin.library.edit', compact('book'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $this->authorize('update', $book);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:150'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($request->hasFile('cover')) {
            if ($book->cover) {
                Storage::disk('public')->delete($book->cover);
            }
            $data['cover'] = $request->file('cover')->store('library', 'public');
        }

        $book->update($data);

        AuditLogger::log('updated_book', $book, "Updated library book \"{$book->title}\".");
        Cache::forget('homepage.content');

        return redirect()->route('admin.library.index')->with('status', 'Book updated.');
    }

    public function approve(Request $request, Book $book): RedirectResponse
    {
        $this->authorize('review', $book);

        $book->update([
            'status' => Book::STATUS_APPROVED,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        $book->donor->notify(new BookDonationApproved($book));

        AuditLogger::log('approved_book', $book, "Approved library book \"{$book->title}\".");
        Cache::forget('homepage.content');

        return back()->with('status', 'Book approved.');
    }

    public function reject(Request $request, Book $book): RedirectResponse
    {
        $this->authorize('review', $book);

        $book->update(['status' => Book::STATUS_REJECTED]);

        AuditLogger::log('rejected_book', $book, "Rejected library book \"{$book->title}\".");

        return back()->with('status', 'Book rejected.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $this->authorize('delete', $book);

        if ($book->cover) {
            Storage::disk('public')->delete($book->cover);
        }
        $book->delete();

        Cache::forget('homepage.content');

        return back()->with('status', 'Book deleted.');
    }

    // Borrow request reports

    public function pendingRequests(Request $request): View
    {
        $this->ensurePermission($request);

        $borrowRequests = BorrowRequest::pending()->with(['book', 'borrower'])->latest()->paginate(15);

        return view('admin.library.pending-requests', compact('borrowRequests'));
    }

    public function rejectedRequests(Request $request): View
    {
        $this->ensurePermission($request);

        $borrowRequests = BorrowRequest::rejected()->with(['book', 'borrower'])->latest('reviewed_at')->paginate(15);

        return view('admin.library.rejected-requests', compact('borrowRequests'));
    }

    public function acceptedRequests(Request $request): View
    {
        $this->ensurePermission($request);

        $borrowRequests = BorrowRequest::approved()->with(['book', 'borrower'])->latest('reviewed_at')->paginate(15);

        return view('admin.library.accepted-requests', compact('borrowRequests'));
    }

    public function availableBooks(Request $request): View
    {
        $this->ensurePermission($request);

        $books = Book::available()->with('donor')->latest('approved_at')->paginate(15);

        return view('admin.library.available-books', compact('books'));
    }

    public function borrowedReport(Request $request): View
    {
        $this->ensurePermission($request);

        $borrowRequests = BorrowRequest::handedOver()->with(['book', 'borrower'])->orderBy('due_date')->paginate(15);

        return view('admin.library.borrowed-report', compact('borrowRequests'));
    }

    // Borrow request actions

    public function approveRequest(Request $request, BorrowRequest $borrowRequest): RedirectResponse
    {
        $this->ensurePermission($request);
        abort_unless($borrowRequest->status === BorrowRequest::STATUS_PENDING, 403);

        $borrowRequest->update([
            'status' => BorrowRequest::STATUS_APPROVED,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $borrowRequest->borrower->notify(new BorrowRequestApproved($borrowRequest));

        AuditLogger::log('approved_borrow_request', $borrowRequest, "Approved borrow request for \"{$borrowRequest->book->title}\".");
        Cache::forget('homepage.content');

        return back()->with('status', 'Borrow request approved.');
    }

    public function rejectRequest(Request $request, BorrowRequest $borrowRequest): RedirectResponse
    {
        $this->ensurePermission($request);
        abort_unless($borrowRequest->status === BorrowRequest::STATUS_PENDING, 403);

        $borrowRequest->update([
            'status' => BorrowRequest::STATUS_REJECTED,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $borrowRequest->borrower->notify(new BorrowRequestRejected($borrowRequest));

        AuditLogger::log('rejected_borrow_request', $borrowRequest, "Rejected borrow request for \"{$borrowRequest->book->title}\".");

        return back()->with('status', 'Borrow request rejected.');
    }

    public function handover(Request $request, BorrowRequest $borrowRequest): RedirectResponse
    {
        $this->ensurePermission($request);
        abort_unless($borrowRequest->status === BorrowRequest::STATUS_APPROVED, 403);

        $borrowRequest->update([
            'status' => BorrowRequest::STATUS_HANDED_OVER,
            'handed_over_at' => now(),
            'due_date' => now()->addMonths($borrowRequest->duration_months),
        ]);

        AuditLogger::log('handed_over_book', $borrowRequest, "Handed over \"{$borrowRequest->book->title}\" to {$borrowRequest->borrower->full_name}.");
        Cache::forget('homepage.content');

        return back()->with('status', 'Book marked as handed over.');
    }

    public function markReturned(Request $request, BorrowRequest $borrowRequest): RedirectResponse
    {
        $this->ensurePermission($request);
        abort_unless($borrowRequest->status === BorrowRequest::STATUS_HANDED_OVER, 403);

        $borrowRequest->update([
            'status' => BorrowRequest::STATUS_RETURNED,
            'returned_at' => now(),
        ]);

        AuditLogger::log('returned_book', $borrowRequest, "Marked \"{$borrowRequest->book->title}\" as returned by {$borrowRequest->borrower->full_name}.");
        Cache::forget('homepage.content');

        return back()->with('status', 'Book marked as returned.');
    }

    public function sendReminder(Request $request, BorrowRequest $borrowRequest): RedirectResponse
    {
        $this->ensurePermission($request);
        abort_unless($borrowRequest->status === BorrowRequest::STATUS_HANDED_OVER, 403);

        $borrowRequest->borrower->notify(new BorrowDueReminder($borrowRequest, $borrowRequest->isOverdue()));
        $borrowRequest->update(['last_reminder_sent_at' => now()]);

        AuditLogger::log('sent_borrow_reminder', $borrowRequest, "Sent a return reminder for \"{$borrowRequest->book->title}\".");

        return back()->with('status', 'Reminder sent.');
    }
}
