<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/saas.php'));
        },
        
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn () => route('register')); 
        $middleware->redirectUsersTo(fn () => route('dashboard'));

        $middleware->alias([
            'role'           => \App\Http\Middleware\RoleMiddleware::class,
            'plan'           => \App\Http\Middleware\PlanMiddleware::class,
            'auth.superadmin'=> \App\Http\Middleware\SuperadminMiddleware::class,
            

            
        ]);
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Exclude M-Pesa callbacks from CSRF
        $middleware->validateCsrfTokens(except: [
            'mpesa/callback',
            'api/mpesa/subscription/callback',
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        
    })
    ->create();
