<?php

namespace Towoju\One2OneCalls\Events;

use Illuminate\Broadcasting\PrivateChannel;

class CallAccepted extends BaseCallEvent
{
    public function broadcastAs(): string
    {
        return 'calls.accepted';
    }

    public function broadcastOn(): PrivateChannel
    {
        // notify caller when accepted
        return new PrivateChannel(config('one2one-calls.channel_prefix') . $this->call->caller_id);
    }
}
