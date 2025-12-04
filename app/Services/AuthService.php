<?php

namespace App\Services;

use App\Models\OtpToken;
use App\Models\User;

class AuthService
{
    private NotificationService $notificationService;

    private function getOtpExpiryMinutes(): int
    {
        return config('passwordless.otp.expiry_minutes', 10);
    }

    private function getMaxAttempts(): int
    {
        return config('passwordless.otp.max_attempts', 5);
    }

    private function getThrottleMinutes(): int
    {
        return config('passwordless.throttle.duration_minutes', 15);
    }

    private function getOtpLength(): int
    {
        return config('passwordless.otp.length', 6);
    }

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Generate a secure 6-digit OTP.
     */
    private function generateOtp(): string
    {
        $length = $this->getOtpLength();
        $min = (int) str_pad('1', $length, '0');
        $max = (int) str_repeat('9', $length);

        return str_pad((string) random_int($min, $max), $length, '0', STR_PAD_LEFT);
    }

    /**
     * Request OTP for email and user type.
     *
     * @throws \Exception
     */
    public function requestOtp(string $email, ?string $userType = null): array
    {
        // Check throttling
        if (! $this->throttleCheck($email)) {
            throw new \Exception('Too many OTP requests. Please try again later.');
        }

        // Cleanup expired OTPs for this email
        OtpToken::where('email', $email)
            ->expired()
            ->delete();

        // Generate new OTP
        $otp = $this->generateOtp();
        $expiresAt = now()->addMinutes($this->getOtpExpiryMinutes());

        // Create or update OTP token
        OtpToken::updateOrCreate(
            [
                'email' => $email,
                'user_type' => $userType,
            ],
            [
                'otp_code' => $otp,
                'attempts' => 0,
                'expires_at' => $expiresAt,
                'verified_at' => null,
            ]
        );

        // Send OTP email
        $this->sendOtpEmail($email, $otp, $userType);

        return [
            'success' => true,
            'expires_at' => $expiresAt,
            'expiry_minutes' => $this->getOtpExpiryMinutes(),
        ];
    }

    /**
     * Verify OTP and return authenticated user.
     *
     * @throws \Exception
     */
    public function verifyOtp(string $email, string $otp, ?string $userType = null): ?User
    {
        // Find valid OTP token
        $otpToken = OtpToken::where('email', $email)
            ->where('otp_code', $otp)
            ->where('user_type', $userType)
            ->valid()
            ->first();

        if (! $otpToken) {
            // Increment attempts for existing token if found
            $existingToken = OtpToken::where('email', $email)
                ->where('user_type', $userType)
                ->unverified()
                ->first();

            if ($existingToken) {
                $existingToken->incrementAttempts();

                if ($existingToken->attempts >= $this->getMaxAttempts()) {
                    $existingToken->delete();
                    throw new \Exception('Maximum verification attempts exceeded. Please request a new OTP.');
                }
            }

            throw new \Exception('Invalid or expired OTP code.');
        }

        // Check if max attempts exceeded
        if ($otpToken->attempts >= $this->getMaxAttempts()) {
            $otpToken->delete();
            throw new \Exception('Maximum verification attempts exceeded. Please request a new OTP.');
        }

        // Check if expired
        if ($otpToken->isExpired()) {
            $otpToken->delete();
            throw new \Exception('OTP has expired. Please request a new one.');
        }

        // Mark as verified
        $otpToken->markAsVerified();

        // Find or create user
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $this->extractNameFromEmail($email),
                'user_type' => $userType,
                'password' => null,
            ]
        );

        // Update user type if it was null
        if ($user->user_type === null && $userType !== null) {
            $user->update(['user_type' => $userType]);
        }

        // Cleanup the verified OTP
        $otpToken->delete();

        return $user;
    }

    /**
     * Send OTP email.
     * Optionally sends SMS if user has phone number (for future use).
     */
    public function sendOtpEmail(string $email, string $otp, ?string $userType = null): void
    {
        // Get user's phone number if available (for future SMS support)
        $user = User::where('email', $email)->first();
        $phoneNumber = $user?->phone_number;

        // Send OTP via email (and optionally SMS if phone number exists)
        // For now, keeping email-only as per plan, but infrastructure supports SMS
        $this->notificationService->sendOtp(
            $email,
            $otp,
            $phoneNumber, // Will be null for now, but ready for future use
            $userType,
            $this->getOtpExpiryMinutes()
        );
    }

    /**
     * Check if email is throttled (rate limiting).
     */
    public function throttleCheck(string $email): bool
    {
        $maxRequests = config('passwordless.throttle.max_requests', 3);
        $recentRequests = OtpToken::where('email', $email)
            ->where('created_at', '>=', now()->subMinutes($this->getThrottleMinutes()))
            ->count();

        return $recentRequests < $maxRequests;
    }

    /**
     * Cleanup expired OTPs.
     *
     * @return int Number of deleted tokens
     */
    public function cleanupExpiredOtps(): int
    {
        return OtpToken::expired()->delete();
    }

    /**
     * Extract name from email address.
     */
    private function extractNameFromEmail(string $email): string
    {
        $name = explode('@', $email)[0];

        return ucfirst(str_replace(['.', '_', '-'], ' ', $name));
    }
}
