<?php

namespace Towoju\One2OneCalls\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Towoju\One2OneCalls\Models\Call;

abstract class BaseCallEvent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public Call $call;

    public function __construct(Call $call)
    {
        $this->call = $call;
    }

    public function broadcastOn(): Channel
    {
        // notify receiver by default
        return new PrivateChannel(config('one2one-calls.channel_prefix') . $this->call->receiver_id);
    }

    public function broadcastWith(): array
    {
        return [
            'uuid' => $this->call->uuid,
            'caller_id' => $this->call->caller_id,
            'receiver_id' => $this->call->receiver_id,
            'status' => $this->call->status,
            'started_at' => optional($this->call->started_at)->toISOString(),
            'ended_at' => optional($this->call->ended_at)->toISOString(),
            'metadata' => $this->call->metadata,
        ];
    }
}
