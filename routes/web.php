<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ChamaController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Auth;

//landing page
Route::get('/', function (){
    return view('welcome');
})->name('home');


// M-Pesa callback route (no auth, CSRF, or throttling since it's called by Safaricom's servers)
Route::post('/mpesa/callback', [ContributionController::class, 'mpesaCallback'])->name('mpesa.callback');

// NEW CHAMA REGISTRATION (admin creating a brand new chama)
    Route::get('/create-chama',  [ChamaController::class, 'registerForm'])->name('chama.register');
    Route::post('/create-chama', [ChamaController::class, 'register'])->name('chama.register.store')->middleware('throttle:5,1');

// ── Guest-only routes (redirect to dashboard if already logged in) ─────────────
Route::middleware('guest')->group(function () {

    // Member registration (joining existing chama with a code)
    Route::get('/register',  [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:5,1');

});
// ── Registration pending confirmation page ────────────────────────────────────
// Accessible after registration — shows "waiting for admin approval" screen
Route::get('/register/pending', function () {
    // If someone visits directly without registering, redirect to register
    if (!session('registered_name')) {
        return redirect()->route('register');
    }
    return view('auth.registration_pending');
})->name('register.pending');

// ── Breeze auth routes (login, logout, password reset, email verify) ──────────
require __DIR__.'/auth.php';

// ── Authenticated + Active account routes ─────────────────────────────────────
Route::middleware(['auth','verified', 'role'])->group(function () {

    // ── Dashboard ──────────────────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Contributions ──────────────────────────────────────────────────────────
    Route::prefix('contributions')->name('contributions.')->group(function () {
        Route::get('/',       [ContributionController::class, 'index'])->name('index');
        Route::get('/create', [ContributionController::class, 'create'])->name('create');
        Route::post('/',      [ContributionController::class, 'store'])->name('store');
    });

    // PayPal payment routes for contributions
    Route::get('/paypal/success', [ContributionController::class, 'paypalSuccess'])->name('paypal.success');
    Route::get('/paypal/cancel',  [ContributionController::class, 'paypalCancel'])->name('paypal.cancel');

    // ── Loans ──────────────────────────────────────────────────────────────────
    Route::prefix('loans')->name('loans.')->group(function () {
        Route::get('/',              [LoanController::class, 'index'])->name('index');
        Route::get('/apply',         [LoanController::class, 'apply'])->name('apply');
        Route::post('/',             [LoanController::class, 'store'])->name('store');
        Route::post('/{loan}/repay', [LoanController::class, 'repay'])->name('repay');

        // Treasurer + Admin only
        Route::middleware('role:treasurer,admin')->group(function () {
            Route::put('/{loan}/approve', [LoanController::class, 'approve'])->name('approve');
            Route::put('/{loan}/decline', [LoanController::class, 'decline'])->name('decline');
        });
    });

    // ── Reports — Treasurer + Admin only ──────────────────────────────────────
    Route::middleware('role:treasurer,admin')
        ->prefix('reports')->name('reports.')->group(function () {
            Route::get('/',          [ReportController::class, 'index'])->name('index');
            Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
            Route::get('/pdf',       [ReportController::class, 'pdf'])->name('pdf');
        });

    // ── Profile ────────────────────────────────────────────────────────────────
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',         [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/',       [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        Route::delete('/',      [ProfileController::class, 'destroy'])->name('destroy');
        Route::post('/logout', function () {
            Auth::logout();
            return redirect('/');
        })->name('logout');
    });

    // ── Billing / Plans ────────────────────────────────────────────────────────
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/',                    [BillingController::class, 'plans'])->name('plans');
        Route::post('/select/{plan:slug}', [BillingController::class, 'selectPlan'])->name('select');
        Route::get('/checkout/{plan:slug}',[BillingController::class, 'checkout'])->name('checkout');
        Route::post('/pay/mpesa/{plan:slug}',  [BillingController::class, 'payMpesa'])->name('pay.mpesa')->middleware('throttle:3,1');
        Route::post('/pay/paypal/{plan:slug}', [BillingController::class, 'payPaypal'])->name('pay.paypal');
        Route::get('/paypal/success',          [BillingController::class, 'paypalSuccess'])->name('paypal.success');
        Route::get('/paypal/cancel',           [BillingController::class, 'paypalCancel'])->name('paypal.cancel');
        Route::get('/pending',                 [BillingController::class, 'pending'])->name('pending');
        Route::get('/history',                 [BillingController::class, 'history'])->name('history');
        Route::post('/cancel',                 [BillingController::class, 'cancel'])->name('cancel');
    });

    // ── Chama Settings — Admin only ────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('chama')->name('chama.')->group(function () {
        Route::get('/settings',         [ChamaController::class, 'settings'])->name('settings');
        Route::put('/settings',         [ChamaController::class, 'updateSettings'])->name('settings.update');
        Route::post('/regenerate-code', [ChamaController::class, 'regenerateCode'])->name('regenerate-code');
    });

    // ── Admin panel — Admin only ───────────────────────────────────────────────
    Route::middleware('role:admin')
        ->prefix('admin')->name('admin.')->group(function () {
            Route::get('/members',                [AdminController::class, 'members'])->name('members');
            Route::put('/members/{user}/approve', [AdminController::class, 'approveMember'])->name('members.approve');
            Route::put('/members/{user}/reject',  [AdminController::class, 'rejectMember'])->name('members.reject');
            Route::put('/members/{user}/toggle',  [AdminController::class, 'toggleMember'])->name('members.toggle');
            Route::put('/members/{user}/role',    [AdminController::class, 'updateRole'])->name('members.role');
            Route::get('/audit-logs',             [AdminController::class, 'auditLogs'])->name('audit');
            Route::get('/audit-logs/export',      [AdminController::class, 'exportAuditLogs'])->name('audit.export');
        });
});