<?php

namespace App\Http\Controllers;

use App\Models\AlumniProfile;
use App\Models\Connection;
use App\Models\User;
use App\Notifications\ConnectionAccepted;
use App\Notifications\ConnectionRequestReceived;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConnectionController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $connections = Connection::accepted()
            ->with(['requester.alumniProfile.degree', 'recipient.alumniProfile.degree'])
            ->where(fn ($q) => $q->where('requester_id', $user->id)->orWhere('recipient_id', $user->id))
            ->latest('responded_at')
            ->paginate(12, ['*'], 'connections_page');

        $received = Connection::pending()
            ->with('requester.alumniProfile.degree')
            ->where('recipient_id', $user->id)
            ->latest()
            ->get();

        $sent = Connection::pending()
            ->with('recipient.alumniProfile.degree')
            ->where('requester_id', $user->id)
            ->latest()
            ->get();

        $connectedIds = Connection::where(fn ($q) => $q->where('requester_id', $user->id)->orWhere('recipient_id', $user->id))
            ->get()
            ->flatMap(fn ($c) => [$c->requester_id, $c->recipient_id])
            ->unique();

        $suggestions = AlumniProfile::with(['user', 'department', 'degree'])
            ->whereNotNull('verified_at')
            ->visibleToAlumni()
            ->where('user_id', '!=', $user->id)
            ->whereNotIn('user_id', $connectedIds)
            ->when($user->alumniProfile?->department_id, fn ($q) => $q->where('department_id', $user->alumniProfile->department_id))
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return view('alumni.network.index', compact('connections', 'received', 'sent', 'suggestions'));
    }

    public function store(Request $request, User $user): JsonResponse
    {
        $requester = $request->user();

        abort_if($requester->id === $user->id, 422, 'You cannot connect with yourself.');

        $existing = Connection::where(fn ($q) => $q->where('requester_id', $requester->id)->where('recipient_id', $user->id))
            ->orWhere(fn ($q) => $q->where('requester_id', $user->id)->where('recipient_id', $requester->id))
            ->first();

        if ($existing) {
            return response()->json(['message' => 'A connection already exists with this alumnus.'], 422);
        }

        $connection = Connection::create([
            'requester_id' => $requester->id,
            'recipient_id' => $user->id,
            'status' => Connection::STATUS_PENDING,
        ]);

        $user->notify(new ConnectionRequestReceived($connection));

        return response()->json(['message' => 'Connection request sent.']);
    }

    public function accept(Request $request, Connection $connection): JsonResponse
    {
        abort_unless($connection->recipient_id === $request->user()->id, 403);

        $connection->update(['status' => Connection::STATUS_ACCEPTED, 'responded_at' => now()]);

        $connection->requester->notify(new ConnectionAccepted($connection));

        return response()->json(['message' => 'Connection accepted.']);
    }

    public function reject(Request $request, Connection $connection): JsonResponse
    {
        abort_unless($connection->recipient_id === $request->user()->id, 403);

        $connection->update(['status' => Connection::STATUS_REJECTED, 'responded_at' => now()]);

        return response()->json(['message' => 'Connection request declined.']);
    }

    public function cancel(Request $request, Connection $connection): JsonResponse
    {
        abort_unless($connection->requester_id === $request->user()->id && $connection->status === Connection::STATUS_PENDING, 403);

        $connection->delete();

        return response()->json(['message' => 'Connection request cancelled.']);
    }

    public function destroy(Request $request, Connection $connection): JsonResponse
    {
        $userId = $request->user()->id;
        abort_unless(in_array($userId, [$connection->requester_id, $connection->recipient_id], true), 403);

        $connection->delete();

        return response()->json(['message' => 'Connection removed.']);
    }
}
