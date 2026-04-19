@if (setting('payment_payphone2_status') == 1)
    <x-plugins-payment::payment-method
        :name="PAYPHONE2_PAYMENT_METHOD_NAME"
        paymentName="Payphone"
        :supportedCurrencies="(new Botble\Payphone2\Services\Gateways\Payphone2PaymentService)->supportedCurrencyCodes()"
    >
        <div class="payphone-payment-info mt-3">
            <p class="text-muted mb-2">
                {{ trans('plugins/payphone2::payphone2.payment_description') }}
            </p>
            <div class="payphone-logos d-flex gap-2 mb-2">
                <img src="{{ url('vendor/core/plugins/payphone2/images/visa.svg') }}" alt="Visa" height="24" style="max-height: 24px;">
                <img src="{{ url('vendor/core/plugins/payphone2/images/mastercard.svg') }}" alt="Mastercard" height="24" style="max-height: 24px;">
                <img src="{{ url('vendor/core/plugins/payphone2/images/diners.svg') }}" alt="Diners Club" height="24" style="max-height: 24px;">
                <img src="{{ url('vendor/core/plugins/payphone2/images/discover.svg') }}" alt="Discover" height="24" style="max-height: 24px;">
                <img src="{{ url('vendor/core/plugins/payphone2/images/payphone.svg') }}" alt="Payphone" height="24" style="max-height: 24px;">
            </div>
            <small class="text-muted">
                <i class="fa fa-info-circle"></i>
                {{ trans('plugins/payphone2::payphone2.redirect_notice') }}
            </small>
        </div>
    </x-plugins-payment::payment-method>
@endif
