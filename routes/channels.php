<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel(config('one2one-calls.channel_prefix') . '{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
