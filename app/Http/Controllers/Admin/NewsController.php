<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNewsRequest;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', News::class);

        $news = News::query()
            ->with(['author', 'category'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%' . $request->string('search') . '%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.news.index', compact('news'));
    }

    public function create(): View
    {
        $this->authorize('create', News::class);

        $categories = NewsCategory::orderBy('name')->get();

        return view('admin.news.create', compact('categories'));
    }

    public function store(StoreNewsRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['author_id'] = $request->user()->id;
        $data['slug'] = $this->uniqueSlug($data['title']);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('news', 'public');
        }

        if ($data['status'] === News::STATUS_PUBLISHED && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $tags = $this->tagNames($request->input('tags'));
        unset($data['tags']);

        $article = News::create($data);
        $article->tags()->sync($tags);

        if ($article->status === News::STATUS_PUBLISHED) {
            Cache::forget('homepage.content');
        }

        return redirect()->route('admin.news.index')->with('status', 'Article saved.');
    }

    public function edit(News $news): View
    {
        $this->authorize('update', $news);

        $categories = NewsCategory::orderBy('name')->get();
        $news->load('tags');

        return view('admin.news.edit', compact('news', 'categories'));
    }

    public function update(StoreNewsRequest $request, News $news): RedirectResponse
    {
        $data = $request->validated();

        if ($data['title'] !== $news->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $news->id);
        }

        if ($request->hasFile('featured_image')) {
            if ($news->featured_image) {
                Storage::disk('public')->delete($news->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('news', 'public');
        }

        if ($data['status'] === News::STATUS_PUBLISHED && $news->status !== News::STATUS_PUBLISHED && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $tags = $this->tagNames($request->input('tags'));
        unset($data['tags']);

        $news->update($data);
        $news->tags()->sync($tags);

        if ($news->status === News::STATUS_PUBLISHED) {
            Cache::forget('homepage.content');
        }

        return redirect()->route('admin.news.index')->with('status', 'Article updated.');
    }

    public function destroy(News $news): RedirectResponse
    {
        $this->authorize('delete', $news);

        $news->delete();

        return back()->with('status', 'Article deleted.');
    }

    protected function tagNames(?string $tags): array
    {
        if (! $tags) {
            return [];
        }

        return collect(explode(',', $tags))
            ->map(fn ($name) => trim($name))
            ->filter()
            ->map(fn ($name) => Tag::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name])->id)
            ->all();
    }

    protected function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 1;

        while (News::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}
