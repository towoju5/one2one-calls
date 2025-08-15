<?php

namespace Towoju\One2OneCalls\Events;

use Illuminate\Broadcasting\PrivateChannel;

class CallEnded extends BaseCallEvent
{
    public function broadcastAs(): string
    {
        return 'calls.ended';
    }

    public function broadcastOn(): PrivateChannel
    {
        // notify both parties? We send to caller by default; frontend can also listen on both
        return new PrivateChannel(config('one2one-calls.channel_prefix') . $this->call->caller_id);
    }
}
