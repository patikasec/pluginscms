<?php

namespace Botble\Payphone\Providers;

use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\Providers\ServiceProvider;
use Botble\Payment\Facades\Payment;
use Botble\Payphone\Services\PayphoneService;

class PayphoneServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->booted(function () {
            $this->registerConfig();
            
            Payment::registerMethod(new PayphoneService());
            
            PanelSectionManager::default()->beforeRendering('payment', function () {
                add_filter(BASE_FILTER_AFTER_HEADER_CORE, function (?string $html): string {
                    return $html . view('plugins/payphone::partials.payphone-script')->render();
                }, 12);
            });
        });

        $this->loadRoutes();
        $this->loadTranslations();
        $this->loadViews();
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/general.php' => config_path('general.php'),
        ], 'payphone-config');
    }
}
