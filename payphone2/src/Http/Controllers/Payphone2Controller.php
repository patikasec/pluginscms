<?php

namespace Botble\Payphone2\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Payment\Supports\PaymentHelper;
use Botble\Payphone2\Services\Gateways\Payphone2PaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Payphone2Controller extends BaseController
{
    /**
     * Handle payment callback/success page
     * This is called after user returns from Payphone payment box
     */
    public function success(Request $request, Payphone2PaymentService $service, BaseHttpResponse $response)
    {
        try {
            // Get transaction parameters from URL
            $transactionId = $request->input('id');
            $clientTransactionId = $request->input('clientTransactionId');

            if (! $transactionId || ! $clientTransactionId) {
                Log::warning('Payphone2: Missing callback parameters', [
                    'id' => $transactionId,
                    'clientTransactionId' => $clientTransactionId,
                ]);

                return $response
                    ->setError()
                    ->setNextUrl(PaymentHelper::getCancelURL())
                    ->setMessage('Invalid payment response');
            }

            // Process the callback and confirm payment
            $result = $service->processCallback([
                'id' => $transactionId,
                'clientTransactionId' => $clientTransactionId,
            ]);

            if ($result['success'] && $result['status'] === 'approved') {
                // Payment was successful
                return $response
                    ->setNextUrl(PaymentHelper::getRedirectURL() . '?charge_id=' . $transactionId)
                    ->setMessage('Payment completed successfully');
            }

            if ($result['status'] === 'canceled') {
                // Payment was canceled by user
                return $response
                    ->setError()
                    ->setNextUrl(PaymentHelper::getCancelURL())
                    ->setMessage('Payment was canceled');
            }

            // Unknown status or error
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->setMessage($result['error'] ?? 'Payment status could not be confirmed');

        } catch (Exception $exception) {
            Log::error('Payphone2: Callback processing error', [
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->withInput()
                ->setMessage('Payment processing error: ' . $exception->getMessage());
        }
    }

    /**
     * Handle payment error/cancellation
     */
    public function error(BaseHttpResponse $response)
    {
        return $response
            ->setError()
            ->setNextUrl(PaymentHelper::getCancelURL())
            ->withInput()
            ->setMessage('Payment was canceled or failed');
    }

    /**
     * Process payment - intermediate page that renders Payphone Box
     * This is used when we need to render the payment box on our side
     */
    public function process(string $transactionId, Request $request)
    {
        try {
            // Store transaction ID in session for later retrieval
            session(['payphone2_pending_transaction' => $transactionId]);

            // Redirect to success URL with proper parameters
            // In a real implementation, you would render a page with the Payphone Box JS SDK
            $callbackUrl = route('payments.payphone2.success') . '?id=' . $transactionId;

            return redirect()->away($callbackUrl);

        } catch (Exception $exception) {
            Log::error('Payphone2: Process error', [
                'exception' => $exception->getMessage(),
            ]);

            return redirect()->route('payments.payphone2.error');
        }
    }
}
