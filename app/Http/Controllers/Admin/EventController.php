<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEventRequest;
use App\Models\Event;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Event::class);

        $events = Event::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%' . $request->string('search') . '%'))
            ->withCount(['registrations' => fn ($q) => $q->where('status', 'registered')])
            ->latest('event_date')
            ->paginate(15)
            ->withQueryString();

        return view('admin.events.index', compact('events'));
    }

    public function create(): View
    {
        $this->authorize('create', Event::class);

        return view('admin.events.create');
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['organizer_id'] = $request->user()->id;
        $data['slug'] = $this->uniqueSlug($data['title']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        if ($data['status'] === Event::STATUS_PUBLISHED) {
            $data['published_at'] = now();
        }

        $event = Event::create($data);

        if ($event->status === Event::STATUS_PUBLISHED) {
            AuditLogger::log('published_event', $event, "Published event \"{$event->title}\".");
            Cache::forget('homepage.content');
        }

        return redirect()->route('admin.events.index')->with('status', 'Event created successfully.');
    }

    public function edit(Event $event): View
    {
        $this->authorize('update', $event);

        return view('admin.events.edit', compact('event'));
    }

    public function update(StoreEventRequest $request, Event $event): RedirectResponse
    {
        $data = $request->validated();

        if ($data['title'] !== $event->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $event->id);
        }

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $wasPublished = $event->status === Event::STATUS_PUBLISHED;

        if ($data['status'] === Event::STATUS_PUBLISHED && ! $wasPublished) {
            $data['published_at'] = now();
        }

        $event->update($data);

        if ($event->status === Event::STATUS_PUBLISHED && ! $wasPublished) {
            AuditLogger::log('published_event', $event, "Published event \"{$event->title}\".");
            Cache::forget('homepage.content');
        }

        return redirect()->route('admin.events.index')->with('status', 'Event updated successfully.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);

        $event->delete();

        return back()->with('status', 'Event deleted.');
    }

    public function registrations(Request $request): View
    {
        $this->authorize('viewAny', Event::class);

        $registrations = \App\Models\EventRegistration::query()
            ->with(['user', 'event'])
            ->when($request->filled('event_id'), fn ($q) => $q->where('event_id', $request->integer('event_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest('registered_at')
            ->paginate(30)
            ->withQueryString();

        $events = Event::orderByDesc('event_date')->get(['id', 'title']);

        return view('admin.events.registrations', compact('registrations', 'events'));
    }

    public function attendees(Event $event): View
    {
        $this->authorize('update', $event);

        $attendees = $event->registrations()->with('user')->where('status', 'registered')->latest('registered_at')->paginate(30);

        return view('admin.events.attendees', compact('event', 'attendees'));
    }

    public function exportRegistrations(Event $event): StreamedResponse
    {
        $this->authorize('update', $event);

        $filename = Str::slug($event->title) . '-registrations.csv';

        return response()->streamDownload(function () use ($event) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Status', 'Registered At']);

            $event->registrations()->with('user')->orderBy('registered_at')->chunk(200, function ($chunk) use ($handle) {
                foreach ($chunk as $registration) {
                    fputcsv($handle, [
                        $registration->user->full_name,
                        $registration->user->email,
                        $registration->status,
                        $registration->registered_at?->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    protected function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 1;

        while (Event::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}
