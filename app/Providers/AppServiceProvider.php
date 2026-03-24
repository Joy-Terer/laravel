<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use App\Models\Loan;
use App\Policies\LoanPolicy;
use Illuminate\Support\Facades\Gate;
 
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
        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
 

        Schema::defaultStringLength(191);
 
        // Register policies
        Gate::policy(Loan::class, LoanPolicy::class);
    }
}