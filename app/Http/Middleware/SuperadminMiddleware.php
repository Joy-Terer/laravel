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
        $allowedIps = explode(',', env('SUPERADMIN_ALLOWED_IPS', ''));
        if (!empty($allowedIps) && !in_array($request->ip(), $allowedIps)) {
            abort(403, 'Access denied!');
        }
    

        return $next($request);
    }
}