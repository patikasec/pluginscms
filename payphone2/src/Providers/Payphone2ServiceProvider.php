<?php

namespace Botble\Payphone2\Providers;

use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class Payphone2ServiceProvider extends ServiceProvider implements DeferrableProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        if (! is_plugin_active('payment')) {
            return;
        }

        $this->setNamespace('plugins/payphone2')
            ->loadHelpers()
            ->loadRoutes()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->publishAssets();

        $this->app->register(HookServiceProvider::class);
    }
}
