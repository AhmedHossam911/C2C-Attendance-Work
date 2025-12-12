<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PreventBackToBackRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
            return $next($request);
        }

        $user = Auth::id() ?? $request->ip();
        $key = 'lock:' . md5($user . $request->method() . $request->fullUrl() . serialize($request->input()));

        $lock = Cache::lock($key, 5); // Lock for 5 seconds max

        if (!$lock->get()) {
            // Log::warning("Double request prevented for key: $key");
            abort(429, 'Too many requests. Please wait a moment.');
        }

        try {
            return $next($request);
        } finally {
            $lock->release();
        }
    }
}
