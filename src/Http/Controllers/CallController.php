<?php

namespace Towoju\One2OneCalls\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\Rule;
use Towoju\One2OneCalls\Models\Call;
use Towoju\One2OneCalls\Events\IncomingCall;
use Towoju\One2OneCalls\Events\CallAccepted;
use Towoju\One2OneCalls\Events\CallDeclined;
use Towoju\One2OneCalls\Events\CallEnded;
use Towoju\One2OneCalls\Events\UserAvailabilityUpdated;

class CallController extends Controller
{
    // availability key in cache
    protected function availabilityKey(int $userId): string
    {
        return "one2one:availability:{$userId}";
    }

    public function setAvailability(Request $request)
    {
        $request->validate([
            'available' => ['required', 'boolean'],
        ]);
        $available = (bool) $request->boolean('available');
        Cache::put($this->availabilityKey($request->user()->id), $available, now()->addHours(12));

        event(new UserAvailabilityUpdated($request->user()->id, $available));

        return response()->json(['ok' => true, 'available' => $available]);
    }

    public function getAvailability(Request $request, int $userId)
    {
        $available = (bool) Cache::get($this->availabilityKey($userId), true);
        return response()->json(['user_id' => $userId, 'available' => $available]);
    }

    public function initiate(Request $request)
    {
        $data = $request->validate([
            'receiver_id' => ['required', 'integer', 'exists:users,id', 'different:' . $request->user()->id],
            'metadata' => ['nullable', 'array'],
        ]);

        // Permission: can initiate?
        if (! (bool) data_get($request->user(), 'can_initiate_call')) {
            return response()->json(['message' => 'Permission denied'], 403);
        }

        // Receiver availability
        $receiverAvailable = (bool) Cache::get($this->availabilityKey($data['receiver_id']), true);
        if (! $receiverAvailable) {
            return response()->json(['message' => 'Receiver not available'], 409);
        }

        // All users can receive calls by default; you can add a per-user receive toggle later if desired.

        $call = Call::create([
            'caller_id' => $request->user()->id,
            'receiver_id' => $data['receiver_id'],
            'status' => 'ringing',
            'metadata' => $data['metadata'] ?? null,
        ]);

        event(new IncomingCall($call));

        return response()->json(['call' => $call], 201);
    }

    public function accept(Request $request, string $uuid)
    {
        $call = Call::where('uuid', $uuid)->firstOrFail();
        $this->authorizeUserOnCall($request->user()->id, $call->receiver_id);

        $call->update([
            'status' => 'accepted',
            'started_at' => now(),
        ]);

        event(new CallAccepted($call));

        return response()->json(['call' => $call]);
    }

    public function decline(Request $request, string $uuid)
    {
        $call = Call::where('uuid', $uuid)->firstOrFail();
        $this->authorizeUserOnCall($request->user()->id, $call->receiver_id);

        $call->update(['status' => 'declined']);

        event(new CallDeclined($call));

        return response()->json(['call' => $call]);
    }

    public function end(Request $request, string $uuid)
    {
        $call = Call::where('uuid', $uuid)->firstOrFail();
        $this->authorizeUserOnCall($request->user()->id, $call->caller_id, $call->receiver_id);

        $call->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        event(new CallEnded($call));

        return response()->json(['call' => $call]);
    }

    protected function authorizeUserOnCall(int $userId, ...$allowed)
    {
        if (! in_array($userId, $allowed, true)) {
            abort(403, 'Not authorized for this call.');
        }
    }
}
