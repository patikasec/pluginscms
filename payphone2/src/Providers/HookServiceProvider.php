<?php

namespace Botble\Payphone2\Providers;

use Botble\Base\Facades\Html;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Facades\PaymentMethods;
use Botble\Payment\Supports\PaymentFeeHelper;
use Botble\Payphone2\Forms\Payphone2PaymentMethodForm;
use Botble\Payphone2\Services\Gateways\Payphone2PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // 1. Register payment method in checkout UI
        add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, [$this, 'registerMethod'], 30, 2);

        // 2. Handle checkout processing (must be in booted callback)
        $this->app->booted(function (): void {
            add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, [$this, 'checkoutWithPayphone2'], 30, 2);
        });

        // 3. Add settings form to admin
        add_filter(PAYMENT_METHODS_SETTINGS_PAGE, [$this, 'addPaymentSettings'], 30);

        // 4. Add to PaymentMethodEnum
        add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
            if ($class == PaymentMethodEnum::class) {
                $values['PAYPHONE2'] = PAYPHONE2_PAYMENT_METHOD_NAME;
            }

            return $values;
        }, 30, 2);

        // 5. Set enum display label
        add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == PAYPHONE2_PAYMENT_METHOD_NAME) {
                $value = 'Payphone';
            }

            return $value;
        }, 30, 2);

        // 6. Set enum HTML rendering (admin badge)
        add_filter(BASE_FILTER_ENUM_HTML, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == PAYPHONE2_PAYMENT_METHOD_NAME) {
                $value = Html::tag(
                    'span',
                    PaymentMethodEnum::getLabel($value),
                    ['class' => 'label-success status-label']
                )->toHtml();
            }

            return $value;
        }, 30, 2);

        // 7. Map method to service class
        add_filter(PAYMENT_FILTER_GET_SERVICE_CLASS, function ($data, $value) {
            if ($value == PAYPHONE2_PAYMENT_METHOD_NAME) {
                $data = Payphone2PaymentService::class;
            }

            return $data;
        }, 30, 2);

        // 8. Show payment details in admin order view
        add_filter(PAYMENT_FILTER_PAYMENT_INFO_DETAIL, function ($data, $payment) {
            if ($payment->payment_channel == PAYPHONE2_PAYMENT_METHOD_NAME) {
                $paymentDetail = (new Payphone2PaymentService())->getPaymentDetails($payment->charge_id);

                if ($paymentDetail) {
                    $data .= view('plugins/payphone2::detail', ['payment' => $paymentDetail])->render();
                }
            }

            return $data;
        }, 30, 2);
    }

    public function addPaymentSettings(?string $settings): string
    {
        return $settings . Payphone2PaymentMethodForm::create()->renderForm();
    }

    public function registerMethod(?string $html, array $data): string
    {
        PaymentMethods::method(PAYPHONE2_PAYMENT_METHOD_NAME, [
            'html' => view('plugins/payphone2::methods', $data)->render(),
        ]);

        return $html;
    }

    public function checkoutWithPayphone2(array $data, Request $request): array
    {
        if ($data['type'] !== PAYPHONE2_PAYMENT_METHOD_NAME) {
            return $data;
        }

        $service = $this->app->make(Payphone2PaymentService::class);

        $paymentData = apply_filters(PAYMENT_FILTER_PAYMENT_DATA, [], $request);

        // Calculate payment fee
        $paymentFee = PaymentFeeHelper::calculateFee(PAYPHONE2_PAYMENT_METHOD_NAME, $paymentData['amount'] ?? 0);
        $paymentData['payment_fee'] = $paymentFee;

        if (! isset($paymentData['currency'])) {
            $paymentData['currency'] = get_application_currency()->title;
        }

        // Validate supported currencies
        $supportedCurrencies = $service->supportedCurrencyCodes();
        if (! in_array($paymentData['currency'], $supportedCurrencies)) {
            $data['error'] = true;
            $data['message'] = trans('plugins/payment::payment.currency_not_supported', [
                'name' => 'Payphone',
                'currency' => $paymentData['currency'],
                'currencies' => implode(', ', $supportedCurrencies),
            ]);

            return $data;
        }

        // Execute payment (redirect-based flow)
        $result = $service->execute($paymentData);

        if ($service->getErrorMessage()) {
            $data['error'] = true;
            $data['message'] = $service->getErrorMessage();
        } elseif ($result) {
            // For redirect-based: set checkoutUrl
            $data['checkoutUrl'] = $result;
        }

        return $data;
    }
}
