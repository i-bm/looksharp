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

    /**
     * Send welcome email to newly registered user.
     *
     * @param  \App\Models\User  $user  The user to send welcome email to
     * @return array Response array with 'success' and 'message' keys
     */
    public function sendWelcomeEmail(\App\Models\User $user): array
    {
        try {
            return $this->emailService->sendWelcomeEmail($user);
        } catch (\Exception $e) {
            Log::error('NotificationService: Welcome email sending failed', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send notification when company is verified.
     *
     * @param  \App\Models\EmployerCompany  $company  The company that was verified
     * @return array Response array with 'success' and 'message' keys
     */
    public function notifyCompanyVerified(\App\Models\EmployerCompany $company): array
    {
        try {
            $email = $company->primary_contact_email ?? $company->official_email;

            if (empty($email)) {
                Log::warning('NotificationService: Cannot send verification email, no email address', [
                    'company_id' => $company->id,
                ]);

                return [
                    'success' => false,
                    'message' => 'No email address found for company',
                ];
            }

            $subject = 'Company Verification Approved - '.config('app.name');
            $content = view('emails.company-verified', [
                'company' => $company,
            ])->render();

            $result = $this->sendEmail($email, $subject, $content);

            Log::info('NotificationService: Company verification email sent', [
                'company_id' => $company->id,
                'email' => $email,
                'success' => $result['success'] ?? false,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('NotificationService: Failed to send company verification notification', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send notification when company verification is rejected.
     *
     * @param  \App\Models\EmployerCompany  $company  The company that was rejected
     * @param  string  $reason  Reason for rejection
     * @return array Response array with 'success' and 'message' keys
     */
    public function notifyCompanyVerificationRejected(\App\Models\EmployerCompany $company, string $reason): array
    {
        try {
            $email = $company->primary_contact_email ?? $company->official_email;

            if (empty($email)) {
                Log::warning('NotificationService: Cannot send verification rejection email, no email address', [
                    'company_id' => $company->id,
                ]);

                return [
                    'success' => false,
                    'message' => 'No email address found for company',
                ];
            }

            $subject = 'Company Verification Rejected - '.config('app.name');
            $content = view('emails.company-verification-rejected', [
                'company' => $company,
                'reason' => $reason,
            ])->render();

            $result = $this->sendEmail($email, $subject, $content);

            Log::info('NotificationService: Company verification rejection email sent', [
                'company_id' => $company->id,
                'email' => $email,
                'success' => $result['success'] ?? false,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('NotificationService: Failed to send company verification rejection notification', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send notification when talent is verified.
     *
     * @param  \App\Models\TalentProfile  $profile  The talent profile that was verified
     * @return array Response array with 'success' and 'message' keys
     */
    public function notifyTalentVerified(\App\Models\TalentProfile $profile): array
    {
        try {
            $user = $profile->user;

            if (! $user || empty($user->email)) {
                Log::warning('NotificationService: Cannot send verification email, no user or email address', [
                    'profile_id' => $profile->id,
                ]);

                return [
                    'success' => false,
                    'message' => 'No email address found for talent',
                ];
            }

            $subject = 'Profile Verification Approved - '.config('app.name');
            $content = view('emails.talent-verified', [
                'profile' => $profile,
                'user' => $user,
            ])->render();

            $result = $this->sendEmail($user->email, $subject, $content);

            Log::info('NotificationService: Talent verification email sent', [
                'profile_id' => $profile->id,
                'email' => $user->email,
                'success' => $result['success'] ?? false,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('NotificationService: Failed to send talent verification notification', [
                'profile_id' => $profile->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send notification when talent verification is rejected.
     *
     * @param  \App\Models\TalentProfile  $profile  The talent profile that was rejected
     * @param  string  $reason  Reason for rejection
     * @return array Response array with 'success' and 'message' keys
     */
    public function notifyTalentVerificationRejected(\App\Models\TalentProfile $profile, string $reason): array
    {
        try {
            $user = $profile->user;

            if (! $user || empty($user->email)) {
                Log::warning('NotificationService: Cannot send verification rejection email, no user or email address', [
                    'profile_id' => $profile->id,
                ]);

                return [
                    'success' => false,
                    'message' => 'No email address found for talent',
                ];
            }

            $subject = 'Profile Verification Rejected - '.config('app.name');
            $content = view('emails.talent-verification-rejected', [
                'profile' => $profile,
                'user' => $user,
                'reason' => $reason,
            ])->render();

            $result = $this->sendEmail($user->email, $subject, $content);

            Log::info('NotificationService: Talent verification rejection email sent', [
                'profile_id' => $profile->id,
                'email' => $user->email,
                'success' => $result['success'] ?? false,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('NotificationService: Failed to send talent verification rejection notification', [
                'profile_id' => $profile->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
