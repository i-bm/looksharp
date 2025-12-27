<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Subscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Initiate a Paystack payment and return payment authorization URL.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function initiatePayment(array $data): array
    {
        Log::info('PaymentService: Initiating Paystack payment', [
            'amount' => $data['amount'] ?? null,
            'email' => $data['email'] ?? null,
            'reference' => $data['reference'] ?? null,
        ]);

        try {
            $secretKey = config('services.paystack.secret_key');

            if (empty($secretKey)) {
                Log::error('PaymentService: Paystack secret key not configured');
                throw new \Exception('Payment gateway not configured. Please contact support.');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$secretKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.paystack.co/transaction/initialize', [
                'email' => $data['email'],
                'amount' => $data['amount'] * 100, // Convert to kobo/pesewas
                'reference' => $data['reference'],
                'callback_url' => $data['callback_url'] ?? null,
                'metadata' => $data['metadata'] ?? [],
                'channels' => $data['channels'] ?? ['card', 'bank', 'ussd', 'qr', 'mobile_money', 'bank_transfer'],
            ]);

            $responseData = $response->json();

            if (! $response->successful() || ! isset($responseData['status']) || ! $responseData['status']) {
                Log::error('PaymentService: Paystack payment initiation failed', [
                    'response' => $responseData,
                    'status_code' => $response->status(),
                ]);
                throw new \Exception('Failed to initiate payment. Please try again.');
            }

            Log::info('PaymentService: Paystack payment initiated successfully', [
                'reference' => $data['reference'],
                'authorization_url' => $responseData['data']['authorization_url'] ?? null,
            ]);

            return [
                'success' => true,
                'authorization_url' => $responseData['data']['authorization_url'],
                'access_code' => $responseData['data']['access_code'],
                'reference' => $responseData['data']['reference'],
            ];
        } catch (\Exception $e) {
            Log::error('PaymentService: Payment initiation exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Payment initiation failed: '.$e->getMessage());
        }
    }

    /**
     * Verify a Paystack payment transaction.
     *
     * @return array<string, mixed>
     */
    public function verifyPayment(string $reference): array
    {
        Log::info('PaymentService: Verifying Paystack payment', [
            'reference' => $reference,
        ]);

        try {
            $secretKey = config('services.paystack.secret_key');

            if (empty($secretKey)) {
                Log::error('PaymentService: Paystack secret key not configured');
                throw new \Exception('Payment gateway not configured.');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$secretKey,
                'Content-Type' => 'application/json',
            ])->get("https://api.paystack.co/transaction/verify/{$reference}");

            $responseData = $response->json();

            if (! $response->successful() || ! isset($responseData['status']) || ! $responseData['status']) {
                Log::warning('PaymentService: Paystack payment verification failed', [
                    'reference' => $reference,
                    'response' => $responseData,
                ]);

                return [
                    'success' => false,
                    'message' => $responseData['message'] ?? 'Payment verification failed',
                ];
            }

            $transaction = $responseData['data'];

            Log::info('PaymentService: Paystack payment verified', [
                'reference' => $reference,
                'status' => $transaction['status'] ?? null,
                'amount' => $transaction['amount'] ?? null,
            ]);

            return [
                'success' => true,
                'status' => $transaction['status'],
                'amount' => $transaction['amount'] / 100, // Convert from kobo/pesewas
                'currency' => $transaction['currency'] ?? 'GHS',
                'paid_at' => $transaction['paid_at'] ?? null,
                'gateway_response' => $transaction['gateway_response'] ?? null,
                'customer' => $transaction['customer'] ?? null,
                'metadata' => $transaction['metadata'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('PaymentService: Payment verification exception', [
                'reference' => $reference,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Payment verification failed: '.$e->getMessage());
        }
    }

    /**
     * Handle Paystack webhook payload.
     *
     * @param  array<string, mixed>  $payload
     */
    public function handleWebhook(array $payload, ?SubscriptionService $subscriptionService = null): void
    {
        Log::info('PaymentService: Processing Paystack webhook', [
            'event' => $payload['event'] ?? null,
            'reference' => $payload['data']['reference'] ?? null,
        ]);

        try {
            $event = $payload['event'] ?? null;
            $data = $payload['data'] ?? [];
            $reference = $data['reference'] ?? null;

            switch ($event) {
                case 'charge.success':
                    Log::info('PaymentService: Payment successful via webhook', [
                        'reference' => $reference,
                        'amount' => $data['amount'] ?? null,
                    ]);

                    // Handle subscription activation if subscription service is available
                    if ($subscriptionService && $reference) {
                        $this->handlePaymentSuccess($reference, $subscriptionService);
                    }
                    break;

                case 'charge.failed':
                    Log::warning('PaymentService: Payment failed via webhook', [
                        'reference' => $reference,
                        'message' => $data['gateway_response'] ?? null,
                    ]);

                    // Handle payment failure if subscription service is available
                    if ($subscriptionService && $reference) {
                        $this->handlePaymentFailure($reference, $data['gateway_response'] ?? 'Payment failed');
                    }
                    break;

                default:
                    Log::info('PaymentService: Unhandled webhook event', [
                        'event' => $event,
                        'reference' => $reference,
                    ]);
                    break;
            }
        } catch (\Exception $e) {
            Log::error('PaymentService: Webhook processing exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle successful payment for subscription.
     */
    private function handlePaymentSuccess(string $reference, SubscriptionService $subscriptionService): void
    {
        Log::info('PaymentService: Handling payment success for subscription', [
            'reference' => $reference,
        ]);

        try {
            $subscription = Subscription::where('payment_reference', $reference)->first();

            if (! $subscription) {
                Log::warning('PaymentService: Subscription not found for payment reference', [
                    'reference' => $reference,
                ]);

                return;
            }

            // Only activate if subscription is in pending_payment status
            if ($subscription->status === 'pending_payment') {
                $subscriptionService->activateSubscription($subscription);

                Log::info('PaymentService: Subscription activated via webhook', [
                    'subscription_id' => $subscription->id,
                    'reference' => $reference,
                ]);
            } else {
                Log::info('PaymentService: Subscription already processed, skipping activation', [
                    'subscription_id' => $subscription->id,
                    'current_status' => $subscription->status,
                    'reference' => $reference,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('PaymentService: Failed to handle payment success', [
                'reference' => $reference,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Don't throw - log error but don't break webhook processing
        }
    }

    /**
     * Handle failed payment for subscription.
     */
    private function handlePaymentFailure(string $reference, string $failureMessage): void
    {
        Log::info('PaymentService: Handling payment failure for subscription', [
            'reference' => $reference,
            'failure_message' => $failureMessage,
        ]);

        try {
            $subscription = Subscription::where('payment_reference', $reference)->first();

            if (! $subscription) {
                Log::warning('PaymentService: Subscription not found for payment reference', [
                    'reference' => $reference,
                ]);

                return;
            }

            // Update subscription payment status to failed
            if ($subscription->status === 'pending_payment') {
                $subscription->update([
                    'payment_status' => 'failed',
                ]);

                Log::info('PaymentService: Subscription payment marked as failed', [
                    'subscription_id' => $subscription->id,
                    'reference' => $reference,
                    'failure_message' => $failureMessage,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('PaymentService: Failed to handle payment failure', [
                'reference' => $reference,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Don't throw - log error but don't break webhook processing
        }
    }

    /**
     * Verify webhook signature from Paystack.
     *
     * This method verifies that incoming webhook requests are authentic by comparing
     * the provided signature with a computed HMAC-SHA512 hash of the payload using
     * the Paystack secret key. Paystack uses the same secret key for API calls and
     * webhook signature verification. Signature verification is mandatory - webhooks
     * will be rejected if the secret key is not configured.
     *
     * @param  string  $signature  The signature from the X-Paystack-Signature header
     * @param  string  $payload  The raw request body payload
     * @return bool Returns true if signature is valid, false otherwise
     */
    public function verifyWebhookSignature(string $signature, string $payload): bool
    {
        $secretKey = config('services.paystack.secret_key');

        if (empty($secretKey)) {
            Log::error('PaymentService: Paystack secret key not configured, rejecting webhook for security');

            return false; // Reject if not configured - signature verification is required
        }

        $computedSignature = hash_hmac('sha512', $payload, $secretKey);

        return hash_equals($computedSignature, $signature);
    }
}
