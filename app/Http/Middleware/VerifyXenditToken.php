<?php

namespace App\Http\Middleware;

use Closure;

class VerifyXenditToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->header('x-callback-token') !== env('XENDIT_CALLBACK_TOKEN')) {
            return response()->json(['message' => 'Failed: Token is invalid'], 401);
        }

        return $next($request);
    }
}
