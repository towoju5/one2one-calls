<?php

namespace Towoju\One2OneCalls\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCanInitiateCall
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! (bool) data_get($user, 'can_initiate_call')) {
            abort(403, 'You are not permitted to initiate calls.');
        }
        return $next($request);
    }
}
