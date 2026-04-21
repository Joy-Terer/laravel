<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use App\Models\Loan;
use App\Policies\LoanPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
 
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind services as singletons
        $this->app->singleton(\App\Services\MpesaService::class);
        $this->app->singleton(\App\Services\PayPalService::class);
        $this->app->singleton(\App\Services\WaveService::class);
        $this->app->singleton(\App\Services\NotificationService::class);
    }
 
    public function boot(): void
    {
        
            RateLimiter::for('registration', function (Request $request) {
                return Limit::perHour(5)->by($request->user()?->id ?: $request->ip());
            });

            RateLimiter::for('mpesa-stk', function (Request $request) {
                return Limit::perMinute(3)->by($request->user()?->id ?: $request->ip());
            });

            RateLimiter::for('mpesa-callback', function (Request $request) {
                return Limit::perMinute(60)->by($request->ip());
            });

        
        
        //fixed paswords default
        password::default (function () {
            return Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised();
    });
        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
 

        Schema::defaultStringLength(191);
 
        // Register policies
        Gate::policy(Loan::class, LoanPolicy::class);
    }
}