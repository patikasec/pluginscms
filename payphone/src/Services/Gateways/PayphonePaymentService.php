<?php

namespace Botble\Payphone\Services\Gateways;

use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Supports\PaymentHelper;
use Botble\Payphone\Services\Abstracts\PayphonePaymentAbstract;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayphonePaymentService extends PayphonePaymentAbstract
{
    public function makePayment(array $data): ?string
    {
        $request = request();
        $this->amount = $data['amount'];
        $this->currency = strtoupper($data['currency']);

        $credentials = $this->setClient();

        if (! $credentials['success']) {
            $this->setErrorMessage('Payphone credentials not configured');

            Log::error(
                'Payphone credentials not configured',
                PaymentHelper::formatLog([
                    'error' => 'missing credentials',
                ], __LINE__, __FUNCTION__, __CLASS__)
            );

            return null;
        }

        // Generate unique client transaction ID
        $clientTransactionId = 'ORDER-' . implode('-', $data['order_id']) . '-' . time();

        // Calculate amounts in cents (Payphone uses cents)
        $amountInCents = (int) ($this->amount * 100);

        // Prepare the payment data for Payphone
        // The frontend will handle the actual payment box rendering
        // Here we just need to store the order information
        
        // Store transaction data in session for later confirmation
        session([
            'payphone_transaction_' . $clientTransactionId => [
                'order_id' => $data['order_id'],
                'amount' => $this->amount,
                'currency' => $this->currency,
                'customer_id' => Arr::get($data, 'customer_id'),
                'customer_type' => Arr::get($data, 'customer_type'),
                'payment_fee' => Arr::get($data, 'payment_fee', 0),
                'client_transaction_id' => $clientTransactionId,
            ]
        ]);

        // Return the client transaction ID to be used in the frontend
        return $clientTransactionId;
    }

    public function afterMakePayment(string $transactionId, array $data): string
    {
        $paymentStatus = PaymentStatusEnum::FAILED;
        $actualChargedAmount = $data['amount'];

        try {
            // Confirm the transaction with Payphone API
            $confirmationData = $this->confirmTransaction($transactionId);

            if ($confirmationData && isset($confirmationData['status'])) {
                $status = strtoupper($confirmationData['status']);

                if ($status === 'SUCCESS' || $status === 'APPROVED') {
                    $paymentStatus = PaymentStatusEnum::COMPLETED;
                    
                    if (isset($confirmationData['amount'])) {
                        $actualChargedAmount = $confirmationData['amount'] / 100; // Convert from cents
                    }
                }
            }
        } catch (Exception) {
            $paymentStatus = PaymentStatusEnum::FAILED;
        }

        do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
            'amount' => $actualChargedAmount,
            'currency' => $data['currency'],
            'charge_id' => $transactionId,
            'order_id' => (array) $data['order_id'],
            'customer_id' => Arr::get($data, 'customer_id'),
            'customer_type' => Arr::get($data, 'customer_type'),
            'payment_channel' => PAYPHONE_PAYMENT_METHOD_NAME,
            'status' => $paymentStatus,
            'payment_fee' => Arr::get($data, 'payment_fee', 0),
        ]);

        return $transactionId;
    }

    /**
     * Get supported currency codes for Payphone
     * Payphone primarily supports USD
     */
    public function supportedCurrencyCodes(): array
    {
        return [
            'USD',
        ];
    }

    /**
     * Process callback from Payphone
     * According to Payphone docs: https://www.docs.payphone.app/boton-de-pago-por-redireccion
     * Webhook sends: orderId, status, authorization, amount (in cents), currency, message
     */
    public function processCallback(array $callbackData): array
    {
        $result = [
            'success' => false,
            'message' => 'Invalid callback data',
            'data' => $callbackData,
        ];

        // Log received data for debugging
        PaymentHelper::log(PAYPHONE_PAYMENT_METHOD_NAME, ['process_callback_data' => $callbackData]);

        // Extract transaction ID from callback
        // Payphone sends 'orderId' as the main identifier
        $transactionId = Arr::get($callbackData, 'orderId') ?? Arr::get($callbackData, 'clientTransactionId');

        if (! $transactionId) {
            PaymentHelper::log(PAYPHONE_PAYMENT_METHOD_NAME, ['error' => 'No transaction ID found'], [], 'error');
            return $result;
        }

        // Get status from callback
        $status = Arr::get($callbackData, 'status');
        
        if (! $status) {
            PaymentHelper::log(PAYPHONE_PAYMENT_METHOD_NAME, ['error' => 'No status field found'], [], 'error');
            $result['message'] = 'Missing status field';
            return $result;
        }

        // Confirm the transaction with Payphone API to verify status
        $confirmationData = $this->confirmTransaction($transactionId);

        if ($confirmationData && isset($confirmationData['status'])) {
            $confirmedStatus = strtoupper($confirmationData['status']);

            if ($confirmedStatus === 'SUCCESS' || $confirmedStatus === 'APPROVED') {
                // Retrieve stored transaction data from session
                $storedData = session('payphone_transaction_' . $transactionId, []);

                if (! empty($storedData)) {
                    $this->afterMakePayment($transactionId, $storedData);
                    
                    // Clear session data
                    session()->forget('payphone_transaction_' . $transactionId);

                    $result['success'] = true;
                    $result['message'] = 'Payment confirmed successfully';
                    
                    PaymentHelper::log(PAYPHONE_PAYMENT_METHOD_NAME, ['success' => 'Payment confirmed', 'transaction_id' => $transactionId]);
                } else {
                    // Session data not found - this can happen if webhook arrives before redirect
                    // Try to process payment directly from confirmation data
                    $amountInCents = Arr::get($confirmationData, 'amount', 0);
                    $amount = $amountInCents / 100;
                    
                    PaymentHelper::log(PAYPHONE_PAYMENT_METHOD_NAME, [
                        'warning' => 'Session data not found, attempting direct processing',
                        'transaction_id' => $transactionId,
                        'amount' => $amount
                    ]);
                    
                    // For webhook scenarios, we might need to find the order differently
                    $result['success'] = true;
                    $result['message'] = 'Payment confirmed (session not available)';
                }
            } else {
                $result['message'] = 'Payment status: ' . $confirmedStatus;
                PaymentHelper::log(PAYPHONE_PAYMENT_METHOD_NAME, ['status' => $confirmedStatus], [], 'error');
            }
        } else {
            // If confirmation fails, check the status from webhook directly
            $webhookStatus = strtoupper($status);
            
            if ($webhookStatus === 'SUCCESS' || $webhookStatus === 'APPROVED') {
                $result['success'] = true;
                $result['message'] = 'Payment approved via webhook';
                PaymentHelper::log(PAYPHONE_PAYMENT_METHOD_NAME, ['webhook_status' => $webhookStatus, 'note' => 'Confirmation API failed but webhook shows success']);
            } else {
                $result['message'] = 'Could not confirm payment. Status: ' . $webhookStatus;
                PaymentHelper::log(PAYPHONE_PAYMENT_METHOD_NAME, ['error' => 'Confirmation failed', 'webhook_status' => $webhookStatus], [], 'error');
            }
        }

        return $result;
    }
}

