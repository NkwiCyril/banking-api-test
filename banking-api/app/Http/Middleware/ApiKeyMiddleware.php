<?php

namespace App\Http\Middleware;

use App\Models\APIKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
// app/Http/Middleware/ApiKeyMiddleware.php
public function handle($request, Closure $next)
{
    $apiKey = $request->header('X-API-KEY');

    if (!$apiKey || !APIKey::where('key', $apiKey)->where('active', true)->exists()) {
        return response()->json(['message' => 'Invalid or missing API key'], 401);
    }

    return $next($request);
}

}
