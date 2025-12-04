<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private string $apiKey;

    private string $apiUrl;

    private ?string $senderId;

    public function __construct()
    {
        $this->apiKey = config('services.smsonlinegh.api_key', '');
        $this->apiUrl = config('services.smsonlinegh.api_url', 'https://api.smsonlinegh.com/api/v1');
        $this->senderId = config('services.smsonlinegh.sender_id');
    }

    /**
     * Send SMS message via SMSOnlineGH API.
     *
     * @param  string  $phoneNumber  Phone number in international format (e.g., +233241234567)
     * @param  string  $message  Message content
     * @param  string|null  $senderId  Optional sender ID (overrides default)
     * @return array Response array with 'success' and 'message' keys
     *
     * @throws \Exception
     */
    public function send(string $phoneNumber, string $message, ?string $senderId = null): array
    {
        if (empty($this->apiKey)) {
            Log::warning('SMSOnlineGH API key not configured');
            throw new \Exception('SMS service is not configured. Please set SMSONLINEGH_API_KEY in your environment.');
        }

        // Format phone number (ensure it starts with +)
        $formattedPhone = $this->formatPhoneNumber($phoneNumber);

        // Use provided sender ID or default
        $finalSenderId = $senderId ?? $this->senderId;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("{$this->apiUrl}/sms/send", [
                'phone_number' => $formattedPhone,
                'message' => $message,
                'sender_id' => $finalSenderId,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('SMS sent successfully', [
                    'phone' => $formattedPhone,
                    'sender_id' => $finalSenderId,
                ]);

                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'data' => $data,
                ];
            }

            // Handle API error response
            $errorMessage = $response->json()['message'] ?? 'Failed to send SMS';
            Log::error('SMS sending failed', [
                'phone' => $formattedPhone,
                'status' => $response->status(),
                'error' => $errorMessage,
            ]);

            throw new \Exception("SMS sending failed: {$errorMessage}");
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('SMS API request exception', [
                'phone' => $formattedPhone,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception("Failed to send SMS: {$e->getMessage()}");
        } catch (\Exception $e) {
            Log::error('SMS sending error', [
                'phone' => $formattedPhone,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Format phone number to international format.
     * Handles Ghana phone numbers (converts 0241234567 to +233241234567).
     *
     * @param  string  $phoneNumber  Phone number in various formats
     * @return string Formatted phone number with country code
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove all non-digit characters except +
        $cleaned = preg_replace('/[^\d+]/', '', $phoneNumber);

        // If it starts with 0, replace with +233 (Ghana country code)
        if (preg_match('/^0/', $cleaned)) {
            $cleaned = '+233'.substr($cleaned, 1);
        }

        // If it doesn't start with +, add +233
        if (! str_starts_with($cleaned, '+')) {
            // If it starts with 233, add +
            if (str_starts_with($cleaned, '233')) {
                $cleaned = '+'.$cleaned;
            } else {
                // Assume Ghana number
                $cleaned = '+233'.$cleaned;
            }
        }

        return $cleaned;
    }

    /**
     * Validate phone number format.
     *
     * @param  string  $phoneNumber  Phone number to validate
     * @return bool True if valid, false otherwise
     */
    public function validatePhoneNumber(string $phoneNumber): bool
    {
        $formatted = $this->formatPhoneNumber($phoneNumber);

        // Basic validation: should start with + and have 10-15 digits after +
        return preg_match('/^\+\d{10,15}$/', $formatted) === 1;
    }
}
