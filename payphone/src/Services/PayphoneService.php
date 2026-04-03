<?php

namespace Botble\Payphone\Services;

use Botble\Payment\Services\AbstractPayment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class PayphoneService extends AbstractPayment
{
    protected string $baseUrl = 'https://api.payphone.app';
    protected string $sandboxUrl = 'https://sandbox-api.payphone.app';

    public function getName(): string
    {
        return 'payphone';
    }

    public function getDisplayName(): string
    {
        return setting('payphone_name', 'Payphone');
    }

    public function getDescription(): string
    {
        return trans('plugins/payphone::payphone.description');
    }

    public function getLogo(): string
    {
        return asset('vendor/core/plugins/payphone/images/payphone-logo.png');
    }

    public function isSupported(): bool
    {
        return setting('payphone_enabled', true) 
            && setting('payphone_token') 
            && setting('payphone_store_id');
    }

    public function getConfigurationFields(): array
    {
        return [
            'payphone_enabled' => [
                'title' => trans('plugins/payphone::payphone.enable'),
                'type' => 'customSelect',
                'value' => setting('payphone_enabled', true),
                'choices' => [
                    ['label' => trans('core/base::base.yes'), 'value' => 1],
                    ['label' => trans('core/base::base.no'), 'value' => 0],
                ],
            ],
            'payphone_name' => [
                'title' => trans('plugins/payphone::payphone.name'),
                'type' => 'text',
                'value' => setting('payphone_name', 'Payphone'),
                'attrs' => ['placeholder' => 'Payphone'],
            ],
            'payphone_token' => [
                'title' => trans('plugins/payphone::payphone.token'),
                'type' => 'password',
                'value' => setting('payphone_token'),
                'attrs' => ['placeholder' => 'Tu token de Payphone'],
            ],
            'payphone_store_id' => [
                'title' => trans('plugins/payphone::payphone.store_id'),
                'type' => 'text',
                'value' => setting('payphone_store_id'),
                'attrs' => ['placeholder' => 'Tu Store ID de Payphone'],
            ],
            'payphone_sandbox' => [
                'title' => trans('plugins/payphone::payphone.sandbox_mode'),
                'type' => 'customSelect',
                'value' => setting('payphone_sandbox', false),
                'choices' => [
                    ['label' => trans('core/base::base.yes'), 'value' => 1],
                    ['label' => trans('core/base::base.no'), 'value' => 0],
                ],
            ],
        ];
    }

    public function getCurrencyCode(): string
    {
        return 'USD';
    }

    public function execute(array $data): array
    {
        try {
            $amount = $data['amount'];
            $orderId = $data['order_id'];
            $currency = $this->getCurrencyCode();
            
            // Convertir a USD si es necesario
            if ($currency !== 'USD') {
                $amount = $this->convertToUsd($amount, $currency);
            }

            $token = setting('payphone_token');
            $storeId = setting('payphone_store_id');
            $isSandbox = setting('payphone_sandbox', false);
            
            $baseUrl = $isSandbox ? $this->sandboxUrl : $this->baseUrl;

            // Obtener datos del cliente (opcionales según configuración de Payphone)
            $clientEmail = $data['client_email'] ?? null;
            $clientName = $data['client_name'] ?? null;
            $clientPhone = $data['client_phone'] ?? null;
            $clientIdNumber = $data['client_id_number'] ?? null; // Cédula/DNI
            $description = $data['description'] ?? 'Pago de pedido #' . $orderId;

            // Construir payload base (campos mínimos requeridos por API Payphone)
            $payload = [
                'external_order_id' => (string) $orderId,
                'amount' => round($amount, 2),
                'currency' => 'USD',
                'return_url' => route('payments.payphone.callback'),
                'webhook_url' => route('payments.payphone.webhook'),
                'metadata' => [
                    'order_id' => $orderId,
                    'description' => $description,
                ],
            ];

            // Agregar datos del cliente si están disponibles
            // Payphone acepta estos campos pero pueden ser opcionales según tu configuración
            if (!empty($clientEmail)) {
                $payload['client_email'] = $clientEmail;
            }
            if (!empty($clientName)) {
                $payload['client_name'] = $clientName;
            }
            if (!empty($clientPhone)) {
                $payload['client_phone'] = $clientPhone;
            }
            if (!empty($clientIdNumber)) {
                $payload['client_id_number'] = $clientIdNumber;
            }

            // Crear orden en Payphone
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("{$baseUrl}/v2/charges", $payload);

            if ($response->successful()) {
                $result = $response->json();
                
                return [
                    'status' => 'success',
                    'checkout_url' => $result['data']['checkout_url'] ?? null,
                    'transaction_id' => $result['data']['id'] ?? null,
                    'message' => trans('plugins/payphone::payphone.payment_initiated'),
                ];
            }

            // Manejar errores específicos de campos requeridos
            $errorMessage = $response->json('message', trans('plugins/payphone::payphone.payment_failed'));
            $errors = $response->json('errors', []);
            
            if (!empty($errors)) {
                // Formatear errores de validación
                $errorMessages = [];
                foreach ($errors as $field => $messages) {
                    if (is_array($messages)) {
                        $errorMessages[] = implode(', ', $messages);
                    } else {
                        $errorMessages[] = $messages;
                    }
                }
                $errorMessage = implode('. ', $errorMessages);
            }

            return [
                'status' => 'error',
                'message' => $errorMessage,
            ];

        } catch (Throwable $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function handleCallback(Request $request): array
    {
        try {
            $status = $request->input('status');
            $orderId = $request->input('external_order_id');
            $transactionId = $request->input('id');

            if ($status === 'approved') {
                return [
                    'status' => 'success',
                    'order_id' => $orderId,
                    'transaction_id' => $transactionId,
                    'message' => trans('plugins/payphone::payphone.payment_success'),
                ];
            }

            return [
                'status' => 'error',
                'order_id' => $orderId,
                'message' => trans('plugins/payphone::payphone.payment_cancelled'),
            ];

        } catch (Throwable $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function refund(array $data): array
    {
        try {
            $transactionId = $data['transaction_id'];
            $amount = $data['amount'] ?? null;

            $token = setting('payphone_token');
            $isSandbox = setting('payphone_sandbox', false);
            $baseUrl = $isSandbox ? $this->sandboxUrl : $this->baseUrl;

            $payload = [];
            if ($amount) {
                $payload['amount'] = round($amount, 2);
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("{$baseUrl}/v2/charges/{$transactionId}/refund", $payload);

            if ($response->successful()) {
                return [
                    'status' => 'success',
                    'message' => trans('plugins/payphone::payphone.refund_success'),
                ];
            }

            return [
                'status' => 'error',
                'message' => $response->json('message', trans('plugins/payphone::payphone.refund_failed')),
            ];

        } catch (Throwable $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    protected function convertToUsd(float $amount, string $currency): float
    {
        // Implementar lógica de conversión según tu sistema
        // Por ahora retorna el mismo valor asumiendo que ya está en USD
        return $amount;
    }
}
