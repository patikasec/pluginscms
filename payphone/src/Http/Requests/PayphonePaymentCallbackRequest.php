<?php

namespace Botble\Payphone\Http\Requests;

use Botble\Support\Http\Requests\Request;

class PayphonePaymentCallbackRequest extends Request
{
    public function rules(): array
    {
        return [
            'orderId' => ['nullable', 'string'],
            'clientTransactionId' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'amount' => ['nullable', 'numeric'],
        ];
    }
}

