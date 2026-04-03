<?php

namespace Botble\Payphone\Providers;

use Botble\Base\Facades\Html;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Facades\PaymentMethods;
use Botble\Payment\Supports\PaymentFeeHelper;
use Botble\Payphone\Forms\PayphonePaymentMethodForm;
use Botble\Payphone\Services\Gateways\PayphonePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, [$this, 'registerPayphoneMethod'], 1, 2);

        $this->app->booted(function (): void {
            add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, [$this, 'checkoutWithPayphone'], 1, 2);
        });

        add_filter(PAYMENT_METHODS_SETTINGS_PAGE, [$this, 'addPaymentSettings'], 1);

        add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
            if ($class == PaymentMethodEnum::class) {
                $values['PAYPHONE'] = PAYPHONE_PAYMENT_METHOD_NAME;
            }

            return $values;
        }, 1, 2);

        add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == PAYPHONE_PAYMENT_METHOD_NAME) {
                $value = 'Payphone';
            }

            return $value;
        }, 1, 2);

        add_filter(BASE_FILTER_ENUM_HTML, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == PAYPHONE_PAYMENT_METHOD_NAME) {
                $value = Html::tag(
                    'span',
                    PaymentMethodEnum::getLabel($value),
                    ['class' => 'label-success status-label']
                )
                    ->toHtml();
            }

            return $value;
        }, 1, 2);

        add_filter(PAYMENT_FILTER_GET_SERVICE_CLASS, function ($data, $value) {
            if ($value == PAYPHONE_PAYMENT_METHOD_NAME) {
                $data = PayphonePaymentService::class;
            }

            return $data;
        }, 1, 2);

        add_filter(PAYMENT_FILTER_FOOTER_ASSETS, function ($data) {
            return $data . view('plugins/payphone::assets')->render();
        }, 1);
    }

    public function addPaymentSettings(?string $settings): string
    {
        return $settings . PayphonePaymentMethodForm::create()->renderForm();
    }

    public function registerPayphoneMethod(?string $html, array $data): string
    {
        PaymentMethods::method(PAYPHONE_PAYMENT_METHOD_NAME, [
            'html' => view('plugins/payphone::methods', $data)->render(),
        ]);

        return $html;
    }

    public function checkoutWithPayphone(array $data, Request $request): array
    {
        if ($data['type'] !== PAYPHONE_PAYMENT_METHOD_NAME) {
            return $data;
        }

        $payphonePaymentService = $this->app->make(PayphonePaymentService::class);

        $currentCurrency = get_application_currency();

        $paymentData = apply_filters(PAYMENT_FILTER_PAYMENT_DATA, [], $request);

        $orderAmount = $paymentData['amount'] ?? 0;
        $paymentFee = 0;
        if (is_plugin_active('payment')) {
            $paymentFee = PaymentFeeHelper::calculateFee(PAYPHONE_PAYMENT_METHOD_NAME, $orderAmount);
        }

        $paymentData['payment_fee'] = $paymentFee;

        if (! isset($paymentData['currency'])) {
            $paymentData['currency'] = get_application_currency()->title;
        }

        $supportedCurrencies = $payphonePaymentService->supportedCurrencyCodes();

        // Payphone only supports USD, convert if needed
        if (! in_array($paymentData['currency'], $supportedCurrencies) && $currentCurrency->title !== 'USD') {
            $currencyModel = $currentCurrency->replicate();

            $supportedCurrency = $currencyModel->query()->where('title', 'USD')->first();

            if ($supportedCurrency) {
                $paymentData['currency'] = $supportedCurrency->title;
                if ($currentCurrency->is_default) {
                    $paymentData['amount'] = $paymentData['amount'] * $supportedCurrency->exchange_rate;
                } else {
                    $paymentData['amount'] = format_price(
                        $paymentData['amount'] / $currentCurrency->exchange_rate,
                        $currentCurrency,
                        true
                    );
                }
            }
        }

        if (! in_array($paymentData['currency'], $supportedCurrencies)) {
            $data['error'] = true;
            $data['message'] = trans(
                'plugins/payment::payment.currency_not_supported',
                [
                    'name' => 'Payphone',
                    'currency' => $paymentData['currency'],
                    'currencies' => implode(', ', $supportedCurrencies),
                ]
            );

            return $data;
        }

        $result = $payphonePaymentService->execute($paymentData);

        if ($payphonePaymentService->getErrorMessage()) {
            $data['error'] = true;
            $data['message'] = $payphonePaymentService->getErrorMessage();
        } elseif ($result) {
            // Store the transaction ID for frontend use
            $data['transaction_id'] = $result;
            $data['charge_id'] = $result;
        }

        return $data;
    }
}

