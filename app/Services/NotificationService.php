<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class NotificationService
{
    private SmsService $smsService;

    private EmailService $emailService;

    public function __construct(SmsService $smsService, EmailService $emailService)
    {
        $this->smsService = $smsService;
        $this->emailService = $emailService;
    }

    /**
     * Send email notification.
     *
     * @param  string  $to  Recipient email address
     * @param  string  $subject  Email subject
     * @param  string  $content  Email content (HTML or plain text)
     * @param  string|null  $template  Optional template name
     * @return array Response array with 'success' and 'message' keys
     */
    public function sendEmail(string $to, string $subject, string $content, ?string $template = null): array
    {
        try {
            return $this->emailService->send($to, $subject, $content, $template);
        } catch (\Exception $e) {
            Log::error('NotificationService: Email sending failed', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send SMS notification.
     *
     * @param  string  $phoneNumber  Phone number in international format
     * @param  string  $message  SMS message content
     * @param  string|null  $senderId  Optional sender ID
     * @return array Response array with 'success' and 'message' keys
     */
    public function sendSms(string $phoneNumber, string $message, ?string $senderId = null): array
    {
        try {
            return $this->smsService->send($phoneNumber, $message, $senderId);
        } catch (\Exception $e) {
            Log::error('NotificationService: SMS sending failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send OTP via email (and optionally SMS).
     *
     * @param  string  $email  Recipient email address
     * @param  string  $otp  OTP code
     * @param  string|null  $phoneNumber  Optional phone number for SMS delivery
     * @param  string|null  $userType  Optional user type
     * @param  int  $expiryMinutes  OTP expiry time in minutes
     * @return array Response array with results for each channel
     */
    public function sendOtp(
        string $email,
        string $otp,
        ?string $phoneNumber = null,
        ?string $userType = null,
        int $expiryMinutes = 10
    ): array {
        $results = [
            'email' => null,
            'sms' => null,
        ];

        // Send OTP via email
        try {
            $emailResult = $this->emailService->sendOtp($email, $otp, $userType, $expiryMinutes);
            $results['email'] = $emailResult;
        } catch (\Exception $e) {
            Log::error('NotificationService: OTP email sending failed', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            $results['email'] = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        // Send OTP via SMS if phone number provided
        if ($phoneNumber) {
            try {
                $smsMessage = "Your Looksharp verification code is: {$otp}. This code expires in {$expiryMinutes} minutes.";
                $smsResult = $this->smsService->send($phoneNumber, $smsMessage);
                $results['sms'] = $smsResult;
            } catch (\Exception $e) {
                Log::error('NotificationService: OTP SMS sending failed', [
                    'phone' => $phoneNumber,
                    'error' => $e->getMessage(),
                ]);

                $results['sms'] = [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Send notification via multiple channels.
     *
     * @param  array  $channels  Array of channels ('email', 'sms', or both)
     * @param  string  $recipient  Email address or phone number (depending on channels)
     * @param  string  $message  Message content
     * @param  array|null  $options  Optional array with:
     *                               - 'subject' (for email)
     *                               - 'phone' (if sending SMS)
     *                               - 'sender_id' (for SMS)
     *                               - 'template' (for email)
     * @return array Response array with results for each channel
     */
    public function sendNotification(
        array $channels,
        string $recipient,
        string $message,
        ?array $options = null
    ): array {
        $results = [];

        $options = $options ?? [];
        $subject = $options['subject'] ?? 'Notification from '.config('app.name');
        $phone = $options['phone'] ?? null;
        $senderId = $options['sender_id'] ?? null;
        $template = $options['template'] ?? null;

        // Send via email if requested
        if (in_array('email', $channels)) {
            try {
                $emailResult = $this->emailService->send($recipient, $subject, $message, $template);
                $results['email'] = $emailResult;
            } catch (\Exception $e) {
                Log::error('NotificationService: Multi-channel email sending failed', [
                    'recipient' => $recipient,
                    'error' => $e->getMessage(),
                ]);

                $results['email'] = [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            }
        }

        // Send via SMS if requested
        if (in_array('sms', $channels)) {
            $smsRecipient = $phone ?? $recipient;
            try {
                $smsResult = $this->smsService->send($smsRecipient, $message, $senderId);
                $results['sms'] = $smsResult;
            } catch (\Exception $e) {
                Log::error('NotificationService: Multi-channel SMS sending failed', [
                    'recipient' => $smsRecipient,
                    'error' => $e->getMessage(),
                ]);

                $results['sms'] = [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }
}
