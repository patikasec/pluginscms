<?php

namespace Botble\Payphone;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Facades\Setting;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Setting::delete([
            'payment_payphone_payment_type',
            'payment_payphone_name',
            'payment_payphone_description',
            'payment_payphone_token',
            'payment_payphone_store_id',
            'payment_payphone_status',
        ]);
    }
}

