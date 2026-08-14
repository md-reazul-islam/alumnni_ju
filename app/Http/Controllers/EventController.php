<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Notifications\EventRegistrationConfirmed;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $events = Event::query()
            ->published()
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
            ->orderBy('event_date')
            ->paginate(12)
            ->withQueryString();

        return view('public.events.index', compact('events'));
    }

    public function show(Event $event): View
    {
        abort_unless($event->status === Event::STATUS_PUBLISHED, 404);

        $isRegistered = auth()->check()
            ? $event->registrations()->where('user_id', auth()->id())->where('status', 'registered')->exists()
            : false;

        return view('public.events.show', compact('event', 'isRegistered'));
    }

    public function register(Request $request, Event $event): RedirectResponse
    {
        abort_unless($event->isRegistrationOpen(), 422, 'Registration for this event is not currently open.');

        $user = $request->user();

        $registration = $event->registrations()->where('user_id', $user->id)->first();

        if ($registration) {
            $registration->update(['status' => 'registered', 'registered_at' => now(), 'cancelled_at' => null]);
        } else {
            $event->registrations()->create([
                'user_id' => $user->id,
                'status' => 'registered',
                'registered_at' => now(),
            ]);
        }

        $user->notify(new EventRegistrationConfirmed($event));

        return back()->with('status', 'You are registered for ' . $event->title . '.');
    }

    public function cancelRegistration(Request $request, Event $event): RedirectResponse
    {
        $registration = $event->registrations()->where('user_id', $request->user()->id)->where('status', 'registered')->first();

        abort_unless($registration, 404);

        $registration->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        return back()->with('status', 'Your registration has been cancelled.');
    }
}
