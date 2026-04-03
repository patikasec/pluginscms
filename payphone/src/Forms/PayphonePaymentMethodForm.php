<?php

namespace Botble\Payphone\Forms;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\TextField;
use Botble\Payment\Concerns\Forms\HasAvailableCountriesField;
use Botble\Payment\Forms\PaymentMethodForm;

class PayphonePaymentMethodForm extends PaymentMethodForm
{
    use HasAvailableCountriesField;

    public function setup(): void
    {
        parent::setup();

        $this
            ->paymentId(PAYPHONE_PAYMENT_METHOD_NAME)
            ->paymentName('Payphone')
            ->paymentDescription('Payphone payment gateway integration')
            ->paymentLogo(url('vendor/core/plugins/payphone/images/payphone-logo.png'))
            ->paymentFeeField(PAYPHONE_PAYMENT_METHOD_NAME)
            ->paymentUrl('https://payphone.app')
            ->paymentInstructions(view('plugins/payphone::instructions')->render())
            ->add(
                'payment_payphone_token',
                TextField::class,
                TextFieldOption::make()
                    ->label('Payphone Token')
                    ->value(BaseHelper::hasDemoModeEnabled() ? '*******************************' : get_payment_setting('token', 'payphone'))
                    ->placeholder('Your Payphone Bearer Token')
                    ->helperText('Get your token from Payphone Developer Dashboard')
                    ->maxLength(400)
            )
            ->add(
                'payment_payphone_store_id',
                TextField::class,
                TextFieldOption::make()
                    ->label('Store ID')
                    ->value(BaseHelper::hasDemoModeEnabled() ? '*******************************' : get_payment_setting('store_id', 'payphone'))
                    ->placeholder('Your Store ID')
                    ->helperText('Get your Store ID from Payphone Developer Dashboard')
            )
            ->addAvailableCountriesField(PAYPHONE_PAYMENT_METHOD_NAME);
    }
}

