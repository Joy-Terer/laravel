<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class SecurityHeaders

{
    
    public function handle(Request $request, Closure $next): Response
{
    $response = $next($request);

    // Added spaces and localhost:5173 for Vite development
    $csp = "default-src 'self'; " .
       "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.googleapis.com https://cdn.jsdelivr.net http://localhost:5173; " .
       "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net http://localhost:5173; " .
       "font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net http://localhost:5173; " .
       "img-src 'self' data: http://localhost:8000 http://127.0.0.1:8000; " .
       "connect-src 'self' ws://localhost:5173 http://localhost:5173; " .
       "frame-ancestors 'none';";
    $response->headers->set('Content-Security-Policy', $csp);
    
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-Frame-Options', 'DENY');
    $response->headers->set('X-XSS-Protection', '1; mode=block');
    $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=()');

    if (app()->environment('production')) {
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
    }

    return $response;
}
}