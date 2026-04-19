# 📄 Documentación Completa: Payment Gateway Integration - Botble Shofy

> **Fuente:** https://docs.botble.com/shofy/developer-payment-gateway.html  
> **Formato:** Markdown (.md)  
> **Nota:** Todos los códigos están completos y sin recortar

---

## Tabla de Contenidos

1. [Introducción](#introducción)
2. [Antes de Empezar: Verificar Marketplace](#antes-de-empezar-verificar-marketplace)
3. [Ejemplos de Referencia en el Codebase](#ejemplos-de-referencia-en-el-codebase)
4. [Descripción General](#descripción-general)
5. [Requisitos Previos](#requisitos-previos)
6. [Estructura del Directorio del Plugin](#estructura-del-directorio-del-plugin)
7. [Implementación Paso a Paso](#implementación-paso-a-paso)
8. [Referencia de Hooks](#referencia-de-hooks)
9. [Funciones Helper](#funciones-helper)
10. [Base de Datos](#base-de-datos)
11. [Lista de Verificación de Testing](#lista-de-verificación-de-testing)
12. [Solución de Problemas](#solución-de-problemas)
13. [Recursos Adicionales](#recursos-adicionales)

---

## Introducción

Integrar una nueva pasarela de pago que no está incluida por defecto requiere desarrollar un plugin personalizado. Esta guía explica las opciones disponibles y lo guía a través de la construcción de uno desde cero.

---

## Antes de Empezar: Verificar Marketplace

Antes de escribir cualquier código, verifique si alguien ya ha construido un plugin para su pasarela de pago.

Tenemos muchos **plugins de pago gratuitos** en Botble Marketplace construidos por autores de nuestra comunidad. Para instalar uno:

1. Vaya a **Admin Panel** → **Plugins** → **Add new plugin**
2. Busque el nombre de su pasarela de pago
3. Haga clic en **Install** y luego en **Activate**

Si su pasarela está disponible en el marketplace, puede comenzar a aceptar pagos en minutos sin ninguna codificación.

> 💡 **TIP:** Navegue por todos los plugins de pago disponibles en [marketplace.botble.com/products?q=payment](https://marketplace.botble.com/products?q=payment). Nuevos plugins son agregados regularmente por la comunidad.

---

## Ejemplos de Referencia en el Codebase

Si necesita desarrollar un plugin personalizado, la mejor manera de aprender la estructura es estudiando los plugins de pasarela de pago existentes incluidos en su proyecto. Buenos puntos de partida son `platform/plugins/razorpay` o `platform/plugins/paystack` — son los ejemplos más simples y limpios.

Necesitará hacer la misma estructura y manejar eventos durante el checkout para implementar una nueva pasarela de pago. Los archivos clave para estudiar (usando Stripe como ejemplo):

| Archivo | Propósito |
|---------|-----------|
| **`src/Providers/HookServiceProvider.php`** | Archivo principal de integración. Registra el plugin con el flujo de checkout (`PAYMENT_FILTER_AFTER_POST_CHECKOUT`), agrega el método de pago al enum, renderiza el formulario de configuración y muestra detalles de pago en admin. |
| **`src/Forms/StripePaymentMethodForm.php`** | Formulario de configuración de admin mostrado bajo **Settings → Payment → Payment methods**. Muestra cómo construir la UI de configuración para claves API, secretos webhook, etc. usando la clase base `PaymentMethodForm`. |
| **`resources/views/methods.blade.php`** | Opción de pago mostrada en la página de checkout. Muestra cómo se renderiza el radio button y la descripción del método de pago para que el cliente lo seleccione. |
| **`src/Services/Gateways/StripePaymentService.php`** | Clase de servicio principal que llama a la API de la pasarela para procesar pagos, verificar transacciones y manejar reembolsos. Reemplace esto con la lógica de API de su pasarela. |
| **`src/Http/Controllers/StripeController.php`** | Maneja webhooks de la pasarela (pago exitoso, fallido, reembolsado) y rutas de callback (páginas de éxito/error después de redirección). |
| **`helpers/constants.php`** | Define la constante `STRIPE_PAYMENT_METHOD_NAME` usada en todo el plugin. |
| **`routes/web.php`** | Ruta de webhook (con bypass de CSRF) y rutas de callback frontend. |
| **`src/Plugin.php`** | Lógica de limpieza que elimina todas las configuraciones cuando se desinstala el plugin. |

> ℹ️ **INFO:** Puede copiar cualquier carpeta de plugin de pago (ej. `platform/plugins/razorpay`), renombrarla y modificarla para que funcione con la API de su pasarela.

> 📚 Para una visión general del proceso de integración, vea el tutorial de la comunidad en los foros.

---

## Descripción General

Integrar una nueva pasarela de pago requiere construir un plugin que se enganche al sistema de pago principal. Cada plugin de pasarela sigue el mismo patrón:

1. **HookServiceProvider** — registra 8-9 filtros para integrarse con checkout, configuración de admin y visualización de pedidos
2. **PaymentService** — maneja el procesamiento real de pagos vía API de la pasarela
3. **PaymentMethodForm** — proporciona la UI de configuración de admin (claves API, toggle de modo, etc.)
4. **Controller** — maneja webhooks y callbacks de la pasarela
5. **Plugin.php** — limpia configuraciones cuando se desinstala el plugin

El flujo de pago se ve así:

```
Checkout Form → PAYMENT_FILTER_PAYMENT_DATA → PAYMENT_FILTER_AFTER_POST_CHECKOUT
    → YourPaymentService::execute() → Gateway API
    → Webhook/Callback → PAYMENT_ACTION_PAYMENT_PROCESSED → Payment record saved
```

### Tipos de Pasarela

| Tipo | Flujo | Ejemplos |
|------|-------|----------|
| **Direct Charge** | Detalles de tarjeta → Llamada API → resultado instantáneo | Stripe API Charge, Razorpay |
| **Redirect-Based** | Crear sesión → redirigir a pasarela → callback webhook | PayPal, Mollie |
| **Hybrid** | Soporta flujos directos y de redirección | Stripe (API Charge + Checkout) |

---

## Requisitos Previos

- El plugin principal **Payment** debe estar activado (`platform/plugins/payment/`)
- PHP 8.3+, Laravel 13.x
- Familiaridad con service providers de Laravel y vistas Blade

---

## Estructura del Directorio del Plugin

```
platform/plugins/my-gateway/
├── plugin.json
├── helpers/
│   └── constants.php
├── resources/
│   ├── lang/en/
│   │   └── my-gateway.php
│   └── views/
│       ├── methods.blade.php
│       ├── detail.blade.php
│       └── instructions.blade.php
├── routes/
│   └── web.php
├── src/
│   ├── Forms/
│   │   └── MyGatewayPaymentMethodForm.php
│   ├── Http/
│   │   └── Controllers/
│   │       └── MyGatewayController.php
│   ├── Providers/
│   │   ├── MyGatewayServiceProvider.php
│   │   └── HookServiceProvider.php
│   ├── Services/
│   │   └── Gateways/
│   │       └── MyGatewayPaymentService.php
│   └── Plugin.php
└── public/
    └── images/
        └── my-gateway.svg
```

---

## Implementación Paso a Paso

### Paso 1: Metadatos del Plugin

Crear `plugin.json`:

```json
{
    "id": "botble/my-gateway",
    "name": "My Gateway Payment",
    "namespace": "BotbleMyGateway",
    "provider": "BotbleMyGatewayProvidersMyGatewayServiceProvider",
    "author": "Your Name",
    "url": "https://yoursite.com",
    "version": "1.0.0",
    "description": "My Gateway payment integration",
    "minimum_core_version": "7.3.0",
    "require": [
        "botble/payment"
    ]
}
```

> ⚠️ **WARNING:** El array `require` **debe** incluir `"botble/payment"`. Su plugin no funcionará sin el plugin de pago principal.

---

### Paso 2: Constante de la Pasarela

Crear `helpers/constants.php`:

```php
<?php

if (! defined('MY_GATEWAY_PAYMENT_METHOD_NAME')) {
    define('MY_GATEWAY_PAYMENT_METHOD_NAME', 'my_gateway');
}
```

Esta constante identifica su pasarela en todo el sistema (claves de configuración, valores enum, registros de pago).

---

### Paso 3: Service Provider

Crear `src/Providers/MyGatewayServiceProvider.php`:

```php
<?php

namespace Botble\MyGateway\Providers;

use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Support\ServiceProvider;

class MyGatewayServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        if (! is_plugin_active('payment')) {
            return;
        }

        $this->setNamespace('plugins/my-gateway')
            ->loadHelpers()
            ->loadRoutes()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->publishAssets();

        $this->app->register(HookServiceProvider::class);
    }
}
```

---

### Paso 4: Hook Service Provider

Crear `src/Providers/HookServiceProvider.php`. Este es el archivo más importante: integra su pasarela con el sistema de pago a través de hooks.

```php
<?php

namespace Botble\MyGateway\Providers;

use Botble\Base\Facades\Html;
use Botble\MyGateway\Forms\MyGatewayPaymentMethodForm;
use Botble\MyGateway\Services\Gateways\MyGatewayPaymentService;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Facades\PaymentMethods;
use Botble\Payment\Supports\PaymentFeeHelper;
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
            add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, [$this, 'checkoutWithMyGateway'], 30, 2);
        });

        // 3. Add settings form to admin
        add_filter(PAYMENT_METHODS_SETTINGS_PAGE, [$this, 'addPaymentSettings'], 30);

        // 4. Add to PaymentMethodEnum
        add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
            if ($class == PaymentMethodEnum::class) {
                $values['MY_GATEWAY'] = MY_GATEWAY_PAYMENT_METHOD_NAME;
            }

            return $values;
        }, 30, 2);

        // 5. Set enum display label
        add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == MY_GATEWAY_PAYMENT_METHOD_NAME) {
                $value = 'My Gateway';
            }

            return $value;
        }, 30, 2);

        // 6. Set enum HTML rendering (admin badge)
        add_filter(BASE_FILTER_ENUM_HTML, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == MY_GATEWAY_PAYMENT_METHOD_NAME) {
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
            if ($value == MY_GATEWAY_PAYMENT_METHOD_NAME) {
                $data = MyGatewayPaymentService::class;
            }

            return $data;
        }, 30, 2);

        // 8. Show payment details in admin order view
        add_filter(PAYMENT_FILTER_PAYMENT_INFO_DETAIL, function ($data, $payment) {
            if ($payment->payment_channel == MY_GATEWAY_PAYMENT_METHOD_NAME) {
                $paymentDetail = (new MyGatewayPaymentService())->getPaymentDetails($payment->charge_id);

                if ($paymentDetail) {
                    $data .= view('plugins/my-gateway::detail', ['payment' => $paymentDetail])->render();
                }
            }

            return $data;
        }, 30, 2);
    }

    public function addPaymentSettings(?string $settings): string
    {
        return $settings . MyGatewayPaymentMethodForm::create()->renderForm();
    }

    public function registerMethod(?string $html, array $data): string
    {
        PaymentMethods::method(MY_GATEWAY_PAYMENT_METHOD_NAME, [
            'html' => view('plugins/my-gateway::methods', $data)->render(),
        ]);

        return $html;
    }

    public function checkoutWithMyGateway(array $data, Request $request): array
    {
        if ($data['type'] !== MY_GATEWAY_PAYMENT_METHOD_NAME) {
            return $data;
        }

        $service = $this->app->make(MyGatewayPaymentService::class);

        $paymentData = apply_filters(PAYMENT_FILTER_PAYMENT_DATA, [], $request);

        // Calculate payment fee
        $paymentFee = PaymentFeeHelper::calculateFee(MY_GATEWAY_PAYMENT_METHOD_NAME, $paymentData['amount'] ?? 0);
        $paymentData['payment_fee'] = $paymentFee;

        if (! isset($paymentData['currency'])) {
            $paymentData['currency'] = get_application_currency()->title;
        }

        // Validate supported currencies
        $supportedCurrencies = $service->supportedCurrencyCodes();
        if (! in_array($paymentData['currency'], $supportedCurrencies)) {
            $data['error'] = true;
            $data['message'] = trans('plugins/payment::payment.currency_not_supported', [
                'name' => 'My Gateway',
                'currency' => $paymentData['currency'],
                'currencies' => implode(', ', $supportedCurrencies),
            ]);

            return $data;
        }

        // Execute payment
        $result = $service->execute($paymentData);

        if ($service->getErrorMessage()) {
            $data['error'] = true;
            $data['message'] = $service->getErrorMessage();
        } elseif ($result) {
            // For direct charge: set charge_id
            // $data['charge_id'] = $result;

            // For redirect-based: set checkoutUrl
            $data['checkoutUrl'] = $result;
        }

        return $data;
    }
}
```

> 📌 **Hook priority**: Use un número único (ej. `30`) que no entre en conflicto con pasarelas existentes. Prioridades integradas: Stripe=1, PayPal=2, Razorpay=11, Mollie=17. Número más bajo = mayor prioridad en orden de visualización del checkout.

---

### Paso 5: Payment Service

Crear `src/Services/Gateways/MyGatewayPaymentService.php`:

```php
<?php

namespace Botble\MyGateway\Services\Gateways;

use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Services\Traits\PaymentErrorTrait;
use Botble\Payment\Supports\PaymentHelper;
use Exception;
use Illuminate\Support\Facades\Http;

class MyGatewayPaymentService
{
    use PaymentErrorTrait;

    public function execute(array $data): ?string
    {
        try {
            // Log the API request
            do_action('payment_before_making_api_request', MY_GATEWAY_PAYMENT_METHOD_NAME, $data);

            // Call your gateway's API to create a payment
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . get_payment_setting('secret_key', MY_GATEWAY_PAYMENT_METHOD_NAME),
            ])->post('https://api.mygateway.com/payments', [
                'amount' => (int) ($data['amount'] * 100), // amount in cents
                'currency' => $data['currency'],
                'description' => $data['description'] ?? 'Order payment',
                'return_url' => PaymentHelper::getRedirectURL($data['checkout_token']),
                'cancel_url' => PaymentHelper::getCancelURL($data['checkout_token']),
                'webhook_url' => route('payments.my-gateway.webhook'),
                'metadata' => [
                    'order_id' => json_encode($data['order_id']),
                    'customer_id' => $data['customer_id'],
                    'customer_type' => $data['customer_type'],
                    'payment_fee' => $data['payment_fee'] ?? 0,
                    'amount' => $data['amount'],
                ],
            ]);

            // Log the API response
            do_action('payment_after_api_response', MY_GATEWAY_PAYMENT_METHOD_NAME, $data, $response->json());

            if (! $response->successful()) {
                $this->setErrorMessage($response->json('error.message', 'Payment creation failed'));

                return null;
            }

            $paymentResponse = $response->json();

            // For redirect-based gateways, return the checkout URL
            return $paymentResponse['checkout_url'];

            // For direct charge gateways, store payment and return charge ID:
            // do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
            //     'amount' => $data['amount'],
            //     'currency' => $data['currency'],
            //     'charge_id' => $paymentResponse['transaction_id'],
            //     'order_id' => $data['order_id'],
            //     'customer_id' => $data['customer_id'],
            //     'customer_type' => $data['customer_type'],
            //     'payment_channel' => MY_GATEWAY_PAYMENT_METHOD_NAME,
            //     'status' => PaymentStatusEnum::COMPLETED,
            //     'payment_fee' => $data['payment_fee'] ?? 0,
            // ]);
            // return $paymentResponse['transaction_id'];
        } catch (Exception $exception) {
            $this->setErrorMessage($exception->getMessage());

            return null;
        }
    }

    public function getPaymentDetails(string $chargeId): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . get_payment_setting('secret_key', MY_GATEWAY_PAYMENT_METHOD_NAME),
            ])->get("https://api.mygateway.com/payments/{$chargeId}");

            return $response->successful() ? $response->json() : null;
        } catch (Exception) {
            return null;
        }
    }

    public function supportedCurrencyCodes(): array
    {
        return ['USD', 'EUR', 'GBP'];
    }

    /**
     * Optional: Support online refunds.
     */
    public function refundOrder(string $paymentId, float|string $totalAmount, array $options = []): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . get_payment_setting('secret_key', MY_GATEWAY_PAYMENT_METHOD_NAME),
            ])->post("https://api.mygateway.com/payments/{$paymentId}/refunds", [
                'amount' => (int) ($totalAmount * 100),
            ]);

            if ($response->successful()) {
                return [
                    'error' => false,
                    'message' => 'succeeded',
                    'data' => $response->json(),
                ];
            }

            return [
                'error' => true,
                'message' => $response->json('error.message', 'Refund failed'),
            ];
        } catch (Exception $exception) {
            return [
                'error' => true,
                'message' => $exception->getMessage(),
            ];
        }
    }

    public function getSupportRefundOnline(): bool
    {
        return true;
    }
}
```

---

### Paso 6: Settings Form

Crear `src/Forms/MyGatewayPaymentMethodForm.php`:

```php
<?php

namespace Botble\MyGateway\Forms;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\TextField;
use Botble\Payment\Concerns\Forms\HasAvailableCountriesField;
use Botble\Payment\Forms\PaymentMethodForm;

class MyGatewayPaymentMethodForm extends PaymentMethodForm
{
    use HasAvailableCountriesField;

    public function setup(): void
    {
        parent::setup();

        $this
            ->paymentId(MY_GATEWAY_PAYMENT_METHOD_NAME)
            ->paymentName('My Gateway')
            ->paymentDescription(trans('plugins/my-gateway::my-gateway.description'))
            ->paymentLogo(url('vendor/core/plugins/my-gateway/images/my-gateway.svg'))
            ->paymentFeeField(MY_GATEWAY_PAYMENT_METHOD_NAME)
            ->paymentUrl('https://mygateway.com')
            ->paymentInstructions(view('plugins/my-gateway::instructions')->render())
            ->add(
                'payment_' . MY_GATEWAY_PAYMENT_METHOD_NAME . '_public_key',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/my-gateway::my-gateway.public_key'))
                    ->value(BaseHelper::hasDemoModeEnabled() ? '***' : get_payment_setting('public_key', MY_GATEWAY_PAYMENT_METHOD_NAME))
                    ->placeholder('pk_*************')
            )
            ->add(
                'payment_' . MY_GATEWAY_PAYMENT_METHOD_NAME . '_secret_key',
                'password',
                TextFieldOption::make()
                    ->label(trans('plugins/my-gateway::my-gateway.secret_key'))
                    ->value(BaseHelper::hasDemoModeEnabled() ? '***' : get_payment_setting('secret_key', MY_GATEWAY_PAYMENT_METHOD_NAME))
                    ->placeholder('sk_*************')
            )
            ->add(
                'payment_' . MY_GATEWAY_PAYMENT_METHOD_NAME . '_webhook_secret',
                'password',
                TextFieldOption::make()
                    ->label(trans('plugins/my-gateway::my-gateway.webhook_secret'))
                    ->value(BaseHelper::hasDemoModeEnabled() ? '***' : get_payment_setting('webhook_secret', MY_GATEWAY_PAYMENT_METHOD_NAME))
                    ->placeholder('whsec_*************')
            )
            ->addAvailableCountriesField(MY_GATEWAY_PAYMENT_METHOD_NAME);
    }
}
```

> 📌 **Convención de claves de configuración**: Todos los campos de configuración deben usar el patrón de nombre `payment_{gateway}_{key}`. El formulario base maneja el guardado automáticamente.

---

### Paso 7: Routes

Crear `routes/web.php`:

```php
<?php

use Botble\MyGateway\Http\Controllers\MyGatewayController;
use Botble\Theme\Facades\Theme;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

// Webhook route - must bypass CSRF
Route::prefix('payment/my-gateway')
    ->name('payments.my-gateway.')
    ->group(function (): void {
        Route::post('webhook', [MyGatewayController::class, 'webhook'])
            ->withoutMiddleware([VerifyCsrfToken::class])
            ->name('webhook');
    });

// Frontend callback routes (success/error pages)
Theme::registerRoutes(function (): void {
    Route::prefix('payment/my-gateway')
        ->name('payments.my-gateway.')
        ->group(function (): void {
            Route::get('success', [MyGatewayController::class, 'success'])->name('success');
            Route::get('error', [MyGatewayController::class, 'error'])->name('error');
        });
});
```

> ⚠️ **WARNING:** Las rutas de webhook **deben** usar `->withoutMiddleware([VerifyCsrfToken::class])` ya que los servidores de pasarela no pueden proporcionar un token CSRF.

---

### Paso 8: Webhook Controller

Crear `src/Http/Controllers/MyGatewayController.php`:

```php
<?php

namespace Botble\MyGateway\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Botble\Payment\Supports\PaymentHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MyGatewayController extends BaseController
{
    public function webhook(Request $request): Response
    {
        // 1. Verify webhook signature
        $signature = $request->header('X-MyGateway-Signature');
        $webhookSecret = get_payment_setting('webhook_secret', MY_GATEWAY_PAYMENT_METHOD_NAME);
        $payload = $request->getContent();

        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

        if (! hash_equals($expectedSignature, $signature)) {
            return response('Invalid signature', 403);
        }

        // 2. Log the webhook
        do_action('payment_before_making_api_request', MY_GATEWAY_PAYMENT_METHOD_NAME, ['webhook' => $payload]);

        $event = $request->json()->all();

        do_action('payment_after_api_response', MY_GATEWAY_PAYMENT_METHOD_NAME, ['webhook' => $payload], $event);

        // 3. Handle event types
        match ($event['type'] ?? null) {
            'payment.completed' => $this->handlePaymentCompleted($event['data']),
            'payment.failed' => $this->handlePaymentFailed($event['data']),
            'payment.refunded' => $this->handlePaymentRefunded($event['data']),
            default => null,
        };

        return response('OK', 200);
    }

    protected function handlePaymentCompleted(array $data): void
    {
        $chargeId = $data['transaction_id'];

        // Prevent duplicate processing
        $existingPayment = Payment::query()->where('charge_id', $chargeId)->first();

        if ($existingPayment && $existingPayment->status === PaymentStatusEnum::COMPLETED) {
            return;
        }

        $metadata = $data['metadata'] ?? [];

        do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
            'amount' => $metadata['amount'] ?? $data['amount'],
            'currency' => strtoupper($data['currency']),
            'charge_id' => $chargeId,
            'order_id' => json_decode($metadata['order_id'] ?? '[]', true),
            'customer_id' => $metadata['customer_id'] ?? null,
            'customer_type' => $metadata['customer_type'] ?? null,
            'payment_channel' => MY_GATEWAY_PAYMENT_METHOD_NAME,
            'status' => PaymentStatusEnum::COMPLETED,
            'payment_fee' => $metadata['payment_fee'] ?? 0,
        ]);
    }

    protected function handlePaymentFailed(array $data): void
    {
        $chargeId = $data['transaction_id'];
        $payment = Payment::query()->where('charge_id', $chargeId)->first();

        if ($payment) {
            $payment->update(['status' => PaymentStatusEnum::FAILED]);
        }
    }

    protected function handlePaymentRefunded(array $data): void
    {
        $chargeId = $data['transaction_id'];
        $payment = Payment::query()->where('charge_id', $chargeId)->first();

        if ($payment) {
            $payment->update([
                'status' => PaymentStatusEnum::REFUNDED,
                'refunded_amount' => $data['refunded_amount'] ?? $payment->amount,
            ]);
        }
    }

    public function success(Request $request)
    {
        return PaymentHelper::handleAfterPaymentSuccess($request);
    }

    public function error(Request $request)
    {
        return PaymentHelper::handleAfterPaymentError($request);
    }
}
```

---

### Paso 9: Blade Views

#### `resources/views/methods.blade.php` — UI de Checkout:

```blade
@if (get_payment_setting('status', MY_GATEWAY_PAYMENT_METHOD_NAME) == 1)
    <li class="list-group-item">
        <input
            class="magic-radio js_payment_method"
            type="radio"
            name="payment_method"
            id="payment_{{ MY_GATEWAY_PAYMENT_METHOD_NAME }}"
            value="{{ MY_GATEWAY_PAYMENT_METHOD_NAME }}"
            @if ($selecting == MY_GATEWAY_PAYMENT_METHOD_NAME) checked @endif
        >
        <label for="payment_{{ MY_GATEWAY_PAYMENT_METHOD_NAME }}">
            {{ get_payment_setting('name', MY_GATEWAY_PAYMENT_METHOD_NAME, 'My Gateway') }}
        </label>
        <div
            class="payment_{{ MY_GATEWAY_PAYMENT_METHOD_NAME }}_wrap payment_collapse_wrap"
            style="display: {{ $selecting == MY_GATEWAY_PAYMENT_METHOD_NAME ? 'block' : 'none' }};"
        >
            <p>{!! BaseHelper::clean(get_payment_setting('description', MY_GATEWAY_PAYMENT_METHOD_NAME)) !!}</p>
        </div>
    </li>
@endif
```

#### `resources/views/detail.blade.php` — Detalle de pago en Admin:

```blade
@if ($payment)
    <p>
        <strong>{{ trans('plugins/my-gateway::my-gateway.transaction_id') }}:</strong>
        {{ $payment['id'] ?? 'N/A' }}
    </p>
    <p>
        <strong>{{ trans('plugins/my-gateway::my-gateway.status') }}:</strong>
        {{ $payment['status'] ?? 'N/A' }}
    </p>
@endif
```

#### `resources/views/instructions.blade.php` — Guía de configuración en Admin:

```blade
<ol>
    <li>
        <p>{{ trans('plugins/my-gateway::my-gateway.instructions.register') }}</p>
    </li>
    <li>
        <p>{{ trans('plugins/my-gateway::my-gateway.instructions.get_credentials') }}</p>
    </li>
    <li>
        <p>{{ trans('plugins/my-gateway::my-gateway.instructions.enter_credentials') }}</p>
    </li>
</ol>
```

---

### Paso 10: Translations

Crear `resources/lang/en/my-gateway.php`:

```php
<?php

return [
    'description' => 'Pay with My Gateway',
    'public_key' => 'Public Key',
    'secret_key' => 'Secret Key',
    'webhook_secret' => 'Webhook Secret',
    'transaction_id' => 'Transaction ID',
    'status' => 'Status',
    'instructions' => [
        'register' => 'Register for an account at <a href="https://mygateway.com" target="_blank">mygateway.com</a>.',
        'get_credentials' => 'Get your API keys from the dashboard.',
        'enter_credentials' => 'Enter your Public Key and Secret Key below.',
    ],
];
```

---

### Paso 11: Plugin Lifecycle

Crear `src/Plugin.php`:

```php
<?php

namespace Botble\MyGateway;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Facades\Setting;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Setting::delete([
            'payment_' . MY_GATEWAY_PAYMENT_METHOD_NAME . '_name',
            'payment_' . MY_GATEWAY_PAYMENT_METHOD_NAME . '_description',
            'payment_' . MY_GATEWAY_PAYMENT_METHOD_NAME . '_public_key',
            'payment_' . MY_GATEWAY_PAYMENT_METHOD_NAME . '_secret_key',
            'payment_' . MY_GATEWAY_PAYMENT_METHOD_NAME . '_webhook_secret',
            'payment_' . MY_GATEWAY_PAYMENT_METHOD_NAME . '_status',
        ]);
    }
}
```

---

### Paso 12: Publicar Assets

Después de crear su plugin, publique los assets y actívelo:

```bash
php artisan cms:publish:assets
php artisan cms:plugin:activate my-gateway
```

---

## Referencia de Hooks

Estos hooks están definidos en `platform/plugins/payment/helpers/constants.php`.

### Filters (retornan valor modificado)

| Hook | Propósito | Args |
|------|-----------|------|
| `PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS` | Registrar método en checkout | `$html`, `$data` |
| `PAYMENT_FILTER_AFTER_POST_CHECKOUT` | Procesar pago al enviar checkout | `$data`, `$request` |
| `PAYMENT_METHODS_SETTINGS_PAGE` | Agregar formulario de configuración a admin | `$settings` |
| `PAYMENT_FILTER_GET_SERVICE_CLASS` | Mapear nombre de método a clase de servicio | `$data`, `$value` |
| `PAYMENT_FILTER_PAYMENT_INFO_DETAIL` | Mostrar detalle de pago en admin | `$data`, `$payment` |
| `PAYMENT_FILTER_PAYMENT_DATA` | Recopilar datos de pago antes del checkout | `$data`, `$request` |
| `PAYMENT_FILTER_HEADER_ASSETS` | Incluir CSS en head de página de checkout | `$data` |
| `PAYMENT_FILTER_FOOTER_ASSETS` | Incluir JS en footer de página de checkout | `$data` |
| `BASE_FILTER_ENUM_ARRAY` | Agregar valor a PaymentMethodEnum | `$values`, `$class` |
| `BASE_FILTER_ENUM_LABEL` | Establecer etiqueta de visualización para valor enum | `$value`, `$class` |
| `BASE_FILTER_ENUM_HTML` | Renderizar enum como badge HTML | `$value`, `$class` |

### Actions (fire-and-forget)

| Hook | Propósito | Data |
|------|-----------|------|
| `PAYMENT_ACTION_PAYMENT_PROCESSED` | Registrar pago en base de datos | Ver Payment Data |

### Payment Processed Data

Array de datos pasado a `PAYMENT_ACTION_PAYMENT_PROCESSED`:

```php
[
    'amount' => 99.99,                                    // Monto del pago
    'currency' => 'USD',                                  // Código de moneda de 3 letras
    'charge_id' => 'txn_abc123',                          // ID de transacción de la pasarela
    'order_id' => [1, 2],                                 // Array de IDs de pedidos
    'customer_id' => 123,                                 // ID del cliente
    'customer_type' => 'Botble\Ecommerce\Models\Customer', // FQCN del modelo Customer
    'payment_channel' => 'my_gateway',                    // Su constante de pasarela
    'status' => PaymentStatusEnum::COMPLETED,             // Estado del pago
    'payment_fee' => 5.00,                                // Monto de comisión
]
```

---

## Funciones Helper

```php
// Obtener un valor de configuración de pago
get_payment_setting('secret_key', 'my_gateway');
get_payment_setting('secret_key', 'my_gateway', 'default_value');

// Obtener la clave de configuración completa (para campos de formulario)
get_payment_setting_key('secret_key', 'my_gateway');
// Retorna: 'payment_my_gateway_secret_key'

// Verificar si un método de pago soporta reembolsos en línea
get_payment_is_support_refund_online($payment);

// Obtener URLs de redirección/cancelación para flujo de checkout
PaymentHelper::getRedirectURL($checkoutToken);
PaymentHelper::getCancelURL($checkoutToken);

// Calcular comisión de pago
PaymentFeeHelper::calculateFee('my_gateway', $orderAmount);
```

---

## Base de Datos

Las pasarelas de pago **no** crean sus propias tablas. Todos los datos se almacenan en las tablas principales `payments` y `payment_logs`.

### Tabla `payments`

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `charge_id` | string(60) | ID de transacción de la pasarela |
| `payment_channel` | string(60) | Nombre de su método de pasarela |
| `amount` | decimal(15) | Monto del pago |
| `payment_fee` | decimal(15) | Comisión cobrada |
| `currency` | string(120) | Código de moneda |
| `status` | string(60) | pending, completed, failed, refunded |
| `order_id` | foreignId | Pedido asociado |
| `customer_id` | foreignId | Cliente (polimórfico) |
| `customer_type` | string | FQCN del modelo Customer |
| `metadata` | json | Datos específicos de la pasarela |

---

## Lista de Verificación de Testing

Antes de enviar su plugin:

- [ ] El método de pago aparece en checkout cuando está habilitado
- [ ] El formulario de configuración guarda y carga correctamente en admin
- [ ] El flujo de pago exitoso se completa y crea registro de pago
- [ ] El pago fallido muestra mensaje de error al cliente
- [ ] El webhook procesa actualizaciones de estado de pago correctamente
- [ ] El webhook verifica firmas y rechaza solicitudes inválidas
- [ ] Los webhooks duplicados se manejan (idempotencia)
- [ ] La validación de moneda funciona para monedas no soportadas
- [ ] Los detalles de pago se muestran correctamente en vista de pedido de admin
- [ ] El reembolso se procesa correctamente (si es soportado)
- [ ] La desinstalación del plugin limpia todas las configuraciones
- [ ] Los filtros de restricción de país funcionan correctamente

---

## Solución de Problemas

### El método de pago no aparece en checkout

1. Verifique que el plugin `payment` esté activo: `is_plugin_active('payment')`
2. Verifique que la configuración `payment_{gateway}_status` sea igual a `1`
3. Asegúrese de que el hook `PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS` esté registrado
4. Verifique las restricciones de país mediante la configuración `Available countries`

### Webhook no procesa

1. La ruta debe excluir CSRF: `->withoutMiddleware([VerifyCsrfToken::class])`
2. La URL del webhook debe ser públicamente accesible (no detrás de autenticación)
3. Verifique que la lógica de validación de firma coincida con la documentación de la pasarela
4. Revise `storage/logs/laravel.log` para errores
5. Use hooks de logging (`payment_before_making_api_request`, `payment_after_api_response`) para depuración

### Formulario de configuración no se muestra

1. Asegúrese de que el filtro `PAYMENT_METHODS_SETTINGS_PAGE` esté registrado
2. La clase del formulario debe extender `Botble\Payment\Forms\PaymentMethodForm`
3. Los nombres de campos de configuración deben seguir la convención `payment_{gateway}_{key}`
4. `HookServiceProvider` debe estar registrado en el `ServiceProvider` principal

---

## Recursos Adicionales

- 🔗 **Plugins del Marketplace**: [marketplace.botble.com/products?q=payment](https://marketplace.botble.com/products?q=payment) — Plugins de pago gratuitos construidos por la comunidad que puede instalar directamente
- 💬 **Tutorial del Foro**: [forums.botble.com/d/1-tutorial-integrate-a-new-payment-gateway](https://forums.botble.com/d/1-tutorial-integrate-a-new-payment-gateway) — Discusión y consejos de la comunidad
- 📦 **Plugins de referencia**: Estudie `platform/plugins/razorpay` o `platform/plugins/paystack` como puntos de partida — son los ejemplos más simples y limpios para seguir
- 📚 **Documentación oficial**: [docs.botble.com](https://docs.botble.com) — Documentación general del CMS

---
