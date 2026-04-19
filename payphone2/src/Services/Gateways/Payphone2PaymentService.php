<?php

namespace Botble\Payphone2\Services\Gateways;

use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Services\Traits\PaymentErrorTrait;
use Botble\Payment\Supports\PaymentHelper;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Payphone2PaymentService
{
    use PaymentErrorTrait;

    protected string $baseUrl = 'https://pay.payphonetodoesposible.com';
    protected string $apiVersion = 'V2';

    /**
     * Execute payment - creates a transaction and returns redirect URL
     */
    public function execute(array $data): ?string
    {
        try {
            // Log the API request
            do_action('payment_before_making_api_request', PAYPHONE2_PAYMENT_METHOD_NAME, $data);

            $token = get_payment_setting('token', PAYPHONE2_PAYMENT_METHOD_NAME);
            $storeId = get_payment_setting('store_id', PAYPHONE2_PAYMENT_METHOD_NAME);

            if (empty($token)) {
                $this->setErrorMessage('Payphone token is not configured');
                Log::error('Payphone2: Token not configured');
                return null;
            }

            // Convert amount to cents (Payphone requires integer in cents)
            $amountInCents = (int) round($data['amount'] * 100);

            // Prepare the payment request for Cajita de Pagos
            // Note: We'll create the transaction via API and get the redirect URL
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($this->getEndpoint('/api/button/V2/Prepare'), [
                'amount' => $amountInCents,
                'clientTransactionId' => $this->generateClientTransactionId($data),
                'currency' => $data['currency'],
                'reference' => $data['description'] ?? 'Order payment #' . implode(',', $data['order_id']),
                'storeId' => $storeId,
                'returnUrl' => PaymentHelper::getRedirectURL($data['checkout_token']),
                'cancelUrl' => PaymentHelper::getCancelURL($data['checkout_token']),
                'phoneNumber' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'documentId' => $data['document_id'] ?? null,
                'identificationType' => $data['identification_type'] ?? 1,
                'optionalParameter' => json_encode([
                    'order_id' => $data['order_id'],
                    'customer_id' => $data['customer_id'],
                    'customer_type' => $data['customer_type'],
                    'payment_fee' => $data['payment_fee'] ?? 0,
                    'amount' => $data['amount'],
                ]),
            ]);

            // Log the API response
            do_action('payment_after_api_response', PAYPHONE2_PAYMENT_METHOD_NAME, $data, $response->json());

            if (! $response->successful()) {
                $errorData = $response->json();
                $this->setErrorMessage($errorData['message'] ?? 'Payment creation failed');
                Log::error('Payphone2: Payment creation failed', ['error' => $errorData]);
                return null;
            }

            $paymentResponse = $response->json();

            // For redirect-based gateways, return the checkout URL
            // The Prepare endpoint should return a URL to redirect the user
            if (isset($paymentResponse['url'])) {
                return $paymentResponse['url'];
            }

            // If no URL is returned, we need to handle it differently
            // Store transaction ID for later confirmation
            if (isset($paymentResponse['transactionId'])) {
                // Store the transaction ID temporarily (in session or cache)
                session(['payphone2_transaction_' . $data['checkout_token'] => $paymentResponse['transactionId']]);
                // Return the Payphone box URL with transaction ID
                return $this->buildPayphoneBoxUrl($paymentResponse['transactionId'], $token);
            }

            $this->setErrorMessage('Invalid response from Payphone API');
            return null;

        } catch (Exception $exception) {
            $this->setErrorMessage('Payment execution failed: ' . $exception->getMessage());
            Log::error('Payphone2: Payment execution error', [
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Confirm payment status via API Button/Confirm endpoint
     */
    public function confirmPayment(int $transactionId, string $clientTransactionId): array
    {
        try {
            $token = get_payment_setting('token', PAYPHONE2_PAYMENT_METHOD_NAME);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($this->getEndpoint('/api/button/V2/Confirm'), [
                'id' => $transactionId,
                'clientTxId' => $clientTransactionId,
            ]);

            if (! $response->successful()) {
                return [
                    'success' => false,
                    'error' => $response->json()['message'] ?? 'Confirmation failed',
                    'errorCode' => $response->json()['errorCode'] ?? null,
                ];
            }

            $result = $response->json();

            return [
                'success' => true,
                'data' => $result,
                'status' => $result['transactionStatus'] ?? 'Unknown',
                'statusCode' => $result['statusCode'] ?? 0,
            ];

        } catch (Exception $exception) {
            Log::error('Payphone2: Confirmation error', [
                'exception' => $exception->getMessage(),
            ]);
            return [
                'success' => false,
                'error' => 'Confirmation failed: ' . $exception->getMessage(),
            ];
        }
    }

    /**
     * Get payment details from stored transaction data
     */
    public function getPaymentDetails(string $chargeId): ?array
    {
        try {
            // Retrieve stored transaction details from database or cache
            // In a real implementation, you would fetch from your transactions table
            $transactionData = cache()->get('payphone2_transaction_' . $chargeId);

            if (! $transactionData) {
                return null;
            }

            return $transactionData;

        } catch (Exception $exception) {
            Log::error('Payphone2: Get payment details error', [
                'exception' => $exception->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Supported currency codes (Payphone primarily supports USD)
     */
    public function supportedCurrencyCodes(): array
    {
        return ['USD'];
    }

    /**
     * Generate unique client transaction ID
     */
    protected function generateClientTransactionId(array $data): string
    {
        // Max 50 characters as per Payphone documentation
        $prefix = 'ORDER';
        $orderIds = is_array($data['order_id']) ? implode('-', $data['order_id']) : $data['order_id'];
        $timestamp = time();
        $random = substr(md5(uniqid()), 0, 6);

        $txId = sprintf('%s-%s-%s-%s', $prefix, $orderIds, $timestamp, $random);

        return substr($txId, 0, 50);
    }

    /**
     * Build Payphone Box URL for redirect
     */
    protected function buildPayphoneBoxUrl(string $transactionId, string $token): string
    {
        // This would typically be the URL where your frontend renders the Payphone Box
        // For now, we'll return a route to our callback handler
        return route('payments.payphone2.process', ['transaction_id' => $transactionId]);
    }

    /**
     * Get full API endpoint URL
     */
    protected function getEndpoint(string $path): string
    {
        return rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
    }

    /**
     * Process webhook or callback notification
     */
    public function processCallback(array $callbackData): array
    {
        $transactionId = $callbackData['id'] ?? null;
        $clientTransactionId = $callbackData['clientTransactionId'] ?? null;

        if (! $transactionId || ! $clientTransactionId) {
            return [
                'success' => false,
                'error' => 'Missing transaction parameters',
            ];
        }

        // Confirm the payment status via API
        $confirmationResult = $this->confirmPayment((int) $transactionId, $clientTransactionId);

        if (! $confirmationResult['success']) {
            return $confirmationResult;
        }

        $data = $confirmationResult['data'];

        // Check if payment was approved
        if (($data['statusCode'] ?? 0) === 3 && ($data['transactionStatus'] ?? '') === 'Approved') {
            // Payment approved - trigger payment processed action
            $optionalParams = json_decode($data['optionalParameter'] ?? '[]', true);

            do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
                'amount' => ($data['amount'] ?? 0) / 100, // Convert from cents
                'currency' => $data['currency'] ?? 'USD',
                'charge_id' => (string) $transactionId,
                'order_id' => $optionalParams['order_id'] ?? [],
                'customer_id' => $optionalParams['customer_id'] ?? null,
                'customer_type' => $optionalParams['customer_type'] ?? null,
                'payment_channel' => PAYPHONE2_PAYMENT_METHOD_NAME,
                'status' => PaymentStatusEnum::COMPLETED,
                'payment_fee' => $optionalParams['payment_fee'] ?? 0,
                'metadata' => $data, // Store full response for reference
            ]);

            // Cache transaction details for future reference
            cache()->set('payphone2_transaction_' . $transactionId, $data, 3600 * 24 * 30); // 30 days

            return [
                'success' => true,
                'status' => 'approved',
                'data' => $data,
            ];
        }

        if (($data['statusCode'] ?? 0) === 2 || ($data['transactionStatus'] ?? '') === 'Canceled') {
            return [
                'success' => false,
                'status' => 'canceled',
                'data' => $data,
            ];
        }

        return [
            'success' => false,
            'status' => 'unknown',
            'data' => $data,
        ];
    }
}
