@if (setting('payment_payphone_status') == 1)
    <x-plugins-payment::payment-method
        :name="PAYPHONE_PAYMENT_METHOD_NAME"
        paymentName="Payphone"
        :supportedCurrencies="(new Botble\Payphone\Services\Gateways\PayphonePaymentService)->supportedCurrencyCodes()"
    >
        <div class="payphone-payment-box-wrapper">
            <div id="pp-button"></div>
        </div>

        @if (!empty($transactionId))
            <div id="payphone-transaction-id" data-value="{{ $transactionId }}"></div>
            <div id="payphone-config" 
                data-token="{{ get_payment_setting('token', PAYPHONE_PAYMENT_METHOD_NAME) }}"
                data-store-id="{{ get_payment_setting('store_id', PAYPHONE_PAYMENT_METHOD_NAME) }}"
                data-amount="{{ isset($paymentData['amount']) ? (int)($paymentData['amount'] * 100) : 0 }}"
                data-currency="USD"
                data-reference="Order #{{ implode(', #', $orderIds ?? []) }}"
            ></div>
        @endif
    </x-plugins-payment::payment-method>
@endif

