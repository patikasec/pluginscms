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
     */
    public function processCallback(array $callbackData): array
    {
        $result = [
            'success' => false,
            'message' => 'Invalid callback data',
            'data' => $callbackData,
        ];

        // Extract transaction ID from callback
        $transactionId = Arr::get($callbackData, 'orderId') ?? Arr::get($callbackData, 'clientTransactionId');

        if (! $transactionId) {
            return $result;
        }

        // Confirm the transaction
        $confirmationData = $this->confirmTransaction($transactionId);

        if ($confirmationData && isset($confirmationData['status'])) {
            $status = strtoupper($confirmationData['status']);

            if ($status === 'SUCCESS' || $status === 'APPROVED') {
                // Retrieve stored transaction data
                $storedData = session('payphone_transaction_' . $transactionId, []);

                if (! empty($storedData)) {
                    $this->afterMakePayment($transactionId, $storedData);
                    
                    // Clear session data
                    session()->forget('payphone_transaction_' . $transactionId);

                    $result['success'] = true;
                    $result['message'] = 'Payment confirmed successfully';
                } else {
                    $result['message'] = 'Transaction data not found in session';
                }
            } else {
                $result['message'] = 'Payment status: ' . $status;
            }
        }

        return $result;
    }
}

