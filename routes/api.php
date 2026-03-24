<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContributionController;

Route::post('/mpesa/callback', [ContributionController::class, 'handleMpesaCallback'])->name('mpesa.callback');