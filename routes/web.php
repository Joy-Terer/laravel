<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\RegisterController;

// ── Guest-only routes ─────────────────────────────────────────────────────────
Route::get('/', fn() => view('welcome'));
Route::middleware('guest')->group(function () {
    Route::get('/register',  [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

// ── Breeze auth routes (login, logout, forgot/reset password) ─────────────────
require __DIR__ . '/auth.php';

// ── Authenticated routes ──────────────────────────────────────────────────────
// All routes here require: logged in + account active
Route::middleware(['auth', 'role'])->group(function () {

    // Dashboard — shows correct view based on role
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

    Route::prefix('chama')->name('chama.')->middleware('role:admin')->group(function () {
    Route::get('/settings',     [\App\Http\Controllers\ChamaController::class, 'settings'])
        ->name('settings');
    Route::put('/settings',     [\App\Http\Controllers\ChamaController::class, 'updateSettings'])
        ->name('settings.update');
    Route::post('/regenerate-code', [\App\Http\Controllers\ChamaController::class, 'regenerateCode'])
        ->name('regenerate-code');
    });

    // ── Contributions ──────────────────────────────────────────────
    Route::prefix('contributions')->name('contributions.')->group(function () {
        Route::get('/',       [ContributionController::class, 'index'])->name('index');
        Route::get('/create', [ContributionController::class, 'create'])->name('create');
        Route::post('/',      [ContributionController::class, 'store'])->name('store');
    });

    // PayPal redirect return routes
    Route::get('/paypal/success', [ContributionController::class, 'paypalSuccess'])->name('paypal.success');
    Route::get('/paypal/cancel',  [ContributionController::class, 'paypalCancel'])->name('paypal.cancel');

    // ── Loans ──────────────────────────────────────────────────────
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

    // ── Reports — Treasurer + Admin only ───────────────────────────
    Route::prefix('reports')->name('reports.')
        ->middleware('role:treasurer,admin')
        ->group(function () {
            Route::get('/',          [ReportController::class, 'index'])->name('index');
            Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
            Route::get('/pdf',       [ReportController::class, 'pdf'])->name('pdf');
        });

    // ── Profile ────────────────────────────────────────────────────
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',          [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/',        [ProfileController::class, 'update'])->name('update');
        Route::put('/password',  [ProfileController::class, 'updatePassword'])->name('password.update');
        Route::delete('/',       [ProfileController::class, 'destroy'])->name('destroy');
    });

    // ── Admin — Admin only ─────────────────────────────────────────
    Route::prefix('admin')->name('admin.')
        ->middleware('role:admin')
        ->group(function () {
            Route::get('/members',                [AdminController::class, 'members'])->name('members');
            Route::put('/members/{user}/approve', [AdminController::class, 'approveMember'])->name('members.approve');
            Route::put('/members/{user}/reject',  [AdminController::class, 'rejectMember'])->name('members.reject');
            Route::put('/members/{user}/toggle',  [AdminController::class, 'toggleMember'])->name('members.toggle');
            Route::put('/members/{user}/role',    [AdminController::class, 'updateRole'])->name('members.role');
            Route::get('/audit-logs',             [AdminController::class, 'auditLogs'])->name('audit');
            Route::get('/audit-logs/export',      [AdminController::class, 'exportAuditLogs'])->name('audit.export');
        });

        // ── Chama Registration  ──────────────
       // This is where a new group comes to create their chama
    Route::middleware('guest')->group(function () {
      Route::get('/create-chama',  [\App\Http\Controllers\ChamaController::class, 'registerForm'])
        ->name('chama.register');
      Route::post('/create-chama', [\App\Http\Controllers\ChamaController::class, 'register'])
        ->name('chama.register.store');
       });
});