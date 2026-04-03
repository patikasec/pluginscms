<?php

namespace Botble\Payphone\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Payment\Http\Controllers\PaymentController as BasePaymentController;
use Illuminate\Http\Request;

class PayphoneController extends BaseController
{
    public function execute(Request $request)
    {
        return app(BasePaymentController::class)->execute($request);
    }
}
