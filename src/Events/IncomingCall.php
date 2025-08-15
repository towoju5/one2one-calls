<?php

namespace Towoju\One2OneCalls\Events;

class IncomingCall extends BaseCallEvent
{
    public function broadcastAs(): string
    {
        return 'calls.incoming';
    }
}
