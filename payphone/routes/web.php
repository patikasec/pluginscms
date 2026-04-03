<?php

use Botble\Base\Facades\Html;
use Illuminate\Support\Facades\Route;
use Botble\Payment\Http\Controllers\PaymentController;

Route::group(['namespace' => 'Botble\Payphone\Http\Controllers'], function () {
    Route::prefix('payments/payphone')->name('payments.payphone.')->group(function () {
        Route::get('callback', [PaymentController::class, 'execute'])->name('callback');
        Route::post('webhook', [PaymentController::class, 'execute'])->name('webhook');
    });
});
