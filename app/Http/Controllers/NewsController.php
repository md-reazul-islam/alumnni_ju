<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(Request $request): View
    {
        $articles = News::published()
            ->with(['author', 'category'])
            ->when($request->filled('category'), fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $request->string('category'))))
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('public.news.index', compact('articles'));
    }

    public function show(News $news): View
    {
        abort_unless($news->status === News::STATUS_PUBLISHED, 404);

        $news->load(['author', 'category', 'tags']);
        $news->increment('views');

        return view('public.news.show', ['article' => $news]);
    }
}
