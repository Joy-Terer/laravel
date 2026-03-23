<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperadminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth('superadmin')->check()) {
            return redirect()->route('superadmin.login');
        }

        return $next($request);
    }
}