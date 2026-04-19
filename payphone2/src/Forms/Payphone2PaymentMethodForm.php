<?php

namespace Botble\Payphone2\Forms;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\TextField;
use Botble\Payment\Concerns\Forms\HasAvailableCountriesField;
use Botble\Payment\Forms\PaymentMethodForm;

class Payphone2PaymentMethodForm extends PaymentMethodForm
{
    use HasAvailableCountriesField;

    public function setup(): void
    {
        parent::setup();

        $this
            ->paymentId(PAYPHONE2_PAYMENT_METHOD_NAME)
            ->paymentName('Payphone')
            ->paymentDescription('Accept payments via Payphone Cajita de Pagos (Ecuador)')
            ->paymentLogo(url('vendor/core/plugins/payphone2/images/payphone.svg'))
            ->paymentFeeField(PAYPHONE2_PAYMENT_METHOD_NAME)
            ->paymentUrl('https://payphone.app')
            ->paymentInstructions(view('plugins/payphone2::instructions')->render())
            ->add(
                'payment_payphone2_token',
                TextField::class,
                TextFieldOption::make()
                    ->label('Payphone Token')
                    ->value(BaseHelper::hasDemoModeEnabled() ? '*******************************' : get_payment_setting('token', 'payphone2'))
                    ->placeholder('Your Payphone Bearer Token')
                    ->helperText('Get your token from Payphone Developer Portal')
                    ->maxLength(400)
            )
            ->add(
                'payment_payphone2_store_id',
                TextField::class,
                TextFieldOption::make()
                    ->label('Store ID')
                    ->value(BaseHelper::hasDemoModeEnabled() ? '*******************************' : get_payment_setting('store_id', 'payphone2'))
                    ->placeholder('Your Store ID from Payphone')
                    ->helperText('Store ID configured in Payphone Developer Portal')
                    ->maxLength(100)
            )
            ->addAvailableCountriesField(PAYPHONE2_PAYMENT_METHOD_NAME);
    }
}
