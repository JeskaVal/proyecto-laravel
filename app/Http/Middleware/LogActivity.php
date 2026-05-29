<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('delete')) {
            \Log::info('API Activity', [
                'method' => $request->getMethod(),
                'path' => $request->path(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'timestamp' => now(),
            ]);
        }

        return $response;
    }
}