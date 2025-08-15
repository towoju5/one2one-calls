<?php

namespace Towoju\One2OneCalls\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class UserAvailabilityUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public int $userId;
    public bool $available;

    public function __construct(int $userId, bool $available)
    {
        $this->userId = $userId;
        $this->available = $available;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel(config('one2one-calls.channel_prefix') . $this->userId);
    }

    public function broadcastAs(): string
    {
        return 'calls.availability';
    }

    public function broadcastWith(): array
    {
        return ['user_id' => $this->userId, 'available' => $this->available];
    }
}
