<?php

namespace Botble\Payphone2;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Facades\Setting;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Setting::delete([
            'payment_payphone2_payment_type',
            'payment_payphone2_name',
            'payment_payphone2_description',
            'payment_payphone2_token',
            'payment_payphone2_store_id',
            'payment_payphone2_status',
        ]);
    }
}
