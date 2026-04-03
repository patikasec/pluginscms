<?php

use Botble\Payphone\Http\Controllers\PayphoneController;
use Botble\Theme\Facades\Theme;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::prefix('payment/payphone')
    ->name('payments.payphone.')
    ->group(function (): void {
        Route::post('webhook', [PayphoneController::class, 'callback'])
            ->withoutMiddleware([VerifyCsrfToken::class])
            ->name('webhook');
    });

Theme::registerRoutes(function (): void {
    Route::prefix('payment/payphone')
        ->name('payments.payphone.')
        ->group(function (): void {
            Route::get('success', [PayphoneController::class, 'success'])->name('success');
            Route::get('error', [PayphoneController::class, 'error'])->name('error');
            Route::any('callback', [PayphoneController::class, 'callback'])->name('callback');
        });
});

