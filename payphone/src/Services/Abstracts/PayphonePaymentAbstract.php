<?php

namespace Botble\Payphone\Services\Abstracts;

use Botble\Payment\Services\Traits\PaymentErrorTrait;
use Exception;
use Illuminate\Support\Facades\Http;

abstract class PayphonePaymentAbstract
{
    use PaymentErrorTrait;

    protected float $amount;

    protected string $currency;

    protected string $transactionId;

    protected bool $supportRefundOnline = true;

    public function getSupportRefundOnline(): bool
    {
        return $this->supportRefundOnline;
    }

    public function execute(array $data): ?string
    {
        $transactionId = null;

        try {
            $transactionId = $this->makePayment($data);
        } catch (Exception $exception) {
            $this->setErrorMessageAndLogging($exception, 1);
        }

        return $transactionId;
    }

    abstract public function makePayment(array $data): ?string;

    abstract public function afterMakePayment(string $transactionId, array $data);

    public function setClient(): array
    {
        $token = get_payment_setting('token', 'payphone');
        $storeId = get_payment_setting('store_id', 'payphone');

        if (! $token || ! $storeId) {
            return ['success' => false, 'message' => 'Missing credentials'];
        }

        return [
            'success' => true,
            'token' => $token,
            'store_id' => $storeId,
        ];
    }

    public function setCurrency($currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Confirm transaction with Payphone API
     */
    public function confirmTransaction(string $transactionId): ?array
    {
        $credentials = $this->setClient();

        if (! $credentials['success']) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $credentials['token'],
                'Content-Type' => 'application/json',
            ])->post('https://pay-api.payphonetodoesposible.com/api/sales/v2/confirm', [
                'orderId' => $transactionId,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (Exception) {
            return null;
        }
    }

    /**
     * Refund a transaction
     */
    public function refundOrder(string $paymentId, float|string $totalAmount, array $options = []): array
    {
        $credentials = $this->setClient();

        if (! $credentials['success']) {
            return [
                'error' => true,
                'message' => trans('plugins/payment::payment.invalid_settings', ['name' => 'Payphone']),
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $credentials['token'],
                'Content-Type' => 'application/json',
            ])->post('https://pay-api.payphonetodoesposible.com/api/sales/v2/reverse', [
                'orderId' => $paymentId,
                'amount' => (int) ($totalAmount * 100), // Convert to cents
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['status']) && $responseData['status'] === 'SUCCESS') {
                    return [
                        'error' => false,
                        'message' => 'Refund successful',
                        'data' => $responseData,
                    ];
                }
            }

            return [
                'error' => true,
                'message' => trans('plugins/payment::payment.refund_failed'),
            ];
        } catch (Exception $exception) {
            return [
                'error' => true,
                'message' => $exception->getMessage(),
            ];
        }
    }
}

