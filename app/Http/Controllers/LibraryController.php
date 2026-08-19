<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LibraryController extends Controller
{
    public function index(Request $request): View
    {
        $books = Book::available()
            ->with('donor')
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%' . $request->string('search') . '%'))
            ->latest('approved_at')
            ->paginate(24)
            ->withQueryString();

        return view('public.library.index', compact('books'));
    }

    public function show(Book $book): View
    {
        abort_unless($book->status === Book::STATUS_APPROVED, 404);

        $book->load('donor');
        $isAvailable = Book::available()->whereKey($book->id)->exists();
        $myPendingRequest = auth()->check()
            ? $book->borrowRequests()->where('user_id', auth()->id())->where('status', 'pending')->exists()
            : false;

        return view('public.library.show', compact('book', 'isAvailable', 'myPendingRequest'));
    }
}
