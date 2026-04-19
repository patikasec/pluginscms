<?php

use Botble\Payphone2\Http\Controllers\Payphone2Controller;
use Botble\Theme\Facades\Theme;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::prefix('payment/payphone2')
    ->name('payments.payphone2.')
    ->group(function (): void {
        Route::post('webhook', [Payphone2Controller::class, 'webhook'])
            ->withoutMiddleware([VerifyCsrfToken::class])
            ->name('webhook');
    });

Theme::registerRoutes(function (): void {
    Route::prefix('payment/payphone2')
        ->name('payments.payphone2.')
        ->group(function (): void {
            Route::get('success', [Payphone2Controller::class, 'success'])->name('success');
            Route::get('error', [Payphone2Controller::class, 'error'])->name('error');
            Route::get('process/{transaction_id}', [Payphone2Controller::class, 'process'])->name('process');
        });
});
