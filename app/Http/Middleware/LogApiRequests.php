<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogApiRequests
{
    public function handle(Request $request, Closure $next)
    {
        // تسجيل تفاصيل الطلب
        $payload = $request->except(['password', 'token']); // علشان ميسجلش بيانات حساسة
        Log::info('API Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'data' => $payload,
            'ip' => $request->ip(),
        ]);

        $response = $next($request);

        // تسجيل الرد
        Log::info('API Response', [
            'status' => $response->getStatusCode(),
            'url' => $request->fullUrl(),
        ]);

        return $response;
    }
}
