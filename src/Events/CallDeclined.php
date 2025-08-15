<?php

namespace Towoju\One2OneCalls\Events;

use Illuminate\Broadcasting\PrivateChannel;

class CallDeclined extends BaseCallEvent
{
    public function broadcastAs(): string
    {
        return 'calls.declined';
    }

    public function broadcastOn(): PrivateChannel
    {
        // notify caller
        return new PrivateChannel(config('one2one-calls.channel_prefix') . $this->call->caller_id);
    }
}
