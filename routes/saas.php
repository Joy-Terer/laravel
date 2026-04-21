<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillingController;
use App\Http\Middleware\SuperadminMiddleware;
use App\Http\Controllers\Superadmin\SuperadminController;

// ── Billing routes (authenticated chama users) ─────────────────────────────
Route::middleware(['auth', 'role'])->prefix('billing')->name('billing.')->group(function () {
    Route::get('/',              [BillingController::class, 'plans'])->name('plans');
    Route::post('/select/{plan}',[BillingController::class, 'selectPlan'])->name('select');
    Route::get('/checkout/{plan}',[BillingController::class, 'checkout'])->name('checkout');
    Route::post('/pay/mpesa/{plan}', [BillingController::class, 'payMpesa'])->name('pay.mpesa')->middleware('throttle:3,1'); // Limit to 3 attempts per minute
    Route::post('/pay/paypal/{plan}',[BillingController::class, 'payPaypal'])->name('pay.paypal')->middleware('throttle:3,1'); // Limit to 3 attempts per minute
    Route::get('/paypal/success',    [BillingController::class, 'paypalSuccess'])->name('paypal.success');
    Route::get('/paypal/cancel',     [BillingController::class, 'paypalCancel'])->name('paypal.cancel');
    Route::get('/pending',           [BillingController::class, 'pending'])->name('pending');
    Route::get('/history',           [BillingController::class, 'history'])->name('history');
    Route::post('/cancel',           [BillingController::class, 'cancel'])->name('cancel');
});

// M-Pesa subscription callback (public — no auth)
Route::post('/api/mpesa/subscription/callback', [BillingController::class, 'mpesaCallback'])
    ->name('billing.mpesa.callback');
    

// ── Superadmin routes ──────────────────────────────────────────────────────
Route::prefix('superadmin')->name('superadmin.')->group(function () {

    // Guest superadmin routes
    Route::middleware('guest:superadmin')->group(function () {
        Route::get('/login',  [SuperadminController::class, 'showLogin'])->name('login');
        Route::post('/login', [SuperadminController::class, 'login'])->name('login.post')->middleware('throttle:5,1'); // Limit to 5 attempts per minute
    });
    

    // Authenticated superadmin routes
    Route::middleware('auth.superadmin')->group(function () {
        Route::post('/logout',  [SuperadminController::class, 'logout'])->name('logout');
        Route::get('/',         [SuperadminController::class, 'dashboard'])->name('dashboard');
        Route::get('/chamas',   [SuperadminController::class, 'chamas'])->name('chamas');
        Route::get('/subscriptions', [SuperadminController::class, 'subscriptions'])->name('subscriptions');
        Route::get('/revenue',  [SuperadminController::class, 'revenue'])->name('revenue');
        Route::get('/plans',    [SuperadminController::class, 'plans'])->name('plans');
        Route::put('/chamas/{chama}/toggle', [SuperadminController::class, 'toggleChama'])->name('chamas.toggle');
        Route::put('/chamas/{chama}/plan',   [SuperadminController::class, 'assignPlan'])->name('chamas.plan');
    });
});