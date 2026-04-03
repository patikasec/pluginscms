<?php

namespace Botble\Payphone\Http\Controllers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Botble\Payment\Supports\PaymentHelper;
use Botble\Payphone\Services\Gateways\PayphonePaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PayphoneController extends BaseController
{
    protected function logWebhook(array $data): void
    {
        PaymentHelper::log(PAYPHONE_PAYMENT_METHOD_NAME, [], $data);
    }

    /**
     * Handle callback from Payphone after payment
     */
    public function callback(Request $request, PayphonePaymentService $payphoneService, BaseHttpResponse $response)
    {
        try {
            // Get all input data (handle both JSON and form data)
            $content = $request->getContent();
            
            // Log raw request details for debugging
            $rawData = [
                'raw_content' => $content,
                'request_method' => $request->method(),
                'content_type' => $request->header('Content-Type'),
                'all_input' => $request->all(),
                'headers' => $request->headers->all(),
            ];
            
            PaymentHelper::log(PAYPHONE_PAYMENT_METHOD_NAME, ['webhook_request' => $rawData]);

            // Try to parse as JSON first
            $callbackData = !empty($content) ? json_decode($content, true) : [];
            
            // If JSON parsing failed or empty, fall back to request input
            if (empty($callbackData) || !is_array($callbackData)) {
                $callbackData = $request->all();
            }

            // Validate required fields according to Payphone documentation
            // Payphone sends: orderId, status, authorization, amount (in cents), currency
            $orderId = Arr::get($callbackData, 'orderId') ?? Arr::get($callbackData, 'clientTransactionId');
            $status = Arr::get($callbackData, 'status');
            
            if (empty($orderId)) {
                PaymentHelper::log(PAYPHONE_PAYMENT_METHOD_NAME, ['error' => 'Missing orderId'], [], 'error');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Missing orderId field'
                ], 400);
            }

            // Process the callback
            $result = $payphoneService->processCallback($callbackData);

            if ($result['success']) {
                // Return success response for Payphone webhook
                return response()->json([
                    'status' => 'success',
                    'message' => 'Webhook processed successfully'
                ], 200);
            }

            // Log error but still return 200 to acknowledge receipt
            PaymentHelper::log(PAYPHONE_PAYMENT_METHOD_NAME, ['processing_error' => $result['message']]);
            return response()->json([
                'status' => 'error',
                'message' => $result['message'] ?? 'Payment processing failed'
            ], 200);
        } catch (Exception $exception) {
            BaseHelper::logError($exception);
            PaymentHelper::log(PAYPHONE_PAYMENT_METHOD_NAME, ['exception' => $exception->getMessage()], [], 'error');

            // Still return 200 to acknowledge receipt and prevent retries
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage() ?: 'Internal server error'
            ], 200);
        }
    }

    /**
     * Success page after payment
     */
    public function success(Request $request, PayphonePaymentService $payphoneService, BaseHttpResponse $response)
    {
        try {
            // Get transaction ID from query parameters
            $transactionId = $request->input('transaction_id') ?? $request->input('orderId');

            if (! $transactionId) {
                return $response
                    ->setError()
                    ->setNextUrl(PaymentHelper::getCancelURL())
                    ->setMessage(trans('plugins/payphone::payphone.no_transaction_id'));
            }

            // Confirm the transaction with Payphone API
            $confirmationData = $payphoneService->confirmTransaction($transactionId);

            if ($confirmationData && isset($confirmationData['status'])) {
                $status = strtoupper($confirmationData['status']);

                if ($status === 'SUCCESS' || $status === 'APPROVED') {
                    // Retrieve stored transaction data
                    $storedData = session('payphone_transaction_' . $transactionId, []);

                    if (! empty($storedData)) {
                        $payphoneService->afterMakePayment($transactionId, $storedData);
                        
                        // Clear session data
                        session()->forget('payphone_transaction_' . $transactionId);

                        return $response
                            ->setNextUrl(PaymentHelper::getRedirectURL() . '?charge_id=' . $transactionId)
                            ->setMessage(trans('plugins/payment::payment.checkout_success'));
                    }
                }
            }

            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->setMessage(trans('plugins/payphone::payphone.payment_failed'));
        } catch (Exception $exception) {
            BaseHelper::logError($exception);

            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->withInput()
                ->setMessage($exception->getMessage() ?: trans('plugins/payphone::payphone.payment_failed'));
        }
    }

    /**
     * Error/cancel page
     */
    public function error(BaseHttpResponse $response)
    {
        return $response
            ->setError()
            ->setNextUrl(PaymentHelper::getCancelURL())
            ->withInput()
            ->setMessage(trans('plugins/payphone::payphone.payment_failed'));
    }
}

