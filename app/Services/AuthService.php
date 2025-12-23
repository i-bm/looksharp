<?php

namespace App\Services;

use App\Models\OtpToken;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     * Request OTP for email (user type auto-detected from user record).
     *
     * @throws \Exception
     */
    public function requestOtp(string $email, ?string $userType = null): array
    {
        // Check if user exists (login requires existing account)
        $user = User::where('email', $email)->first();
        if (! $user) {
            throw new \Exception('No account found with this email. Please register first.');
        }

        // Auto-detect user type from user record if not provided
        if ($userType === null && $user->user_type !== null) {
            $userType = $user->user_type;
            Log::info('Auto-detected user type from user record', [
                'email' => $email,
                'user_type' => $userType,
            ]);
        }

        // Check throttling with detailed status
        $throttleStatus = $this->getThrottleStatus($email);
        if ($throttleStatus['is_throttled']) {
            throw new \Exception($throttleStatus['message']);
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
     * Verify OTP and return authenticated user (user type auto-detected from user record).
     *
     * @throws \Exception
     */
    public function verifyOtp(string $email, string $otp, ?string $userType = null): ?User
    {
        // Find existing user to get user type if not provided
        $user = User::where('email', $email)->first();

        if (! $user) {
            throw new \Exception('No account found with this email. Please register first.');
        }

        // Auto-detect user type from user record if not provided
        if ($userType === null && $user->user_type !== null) {
            $userType = $user->user_type;
            Log::info('Auto-detected user type from user record during OTP verification', [
                'email' => $email,
                'user_type' => $userType,
            ]);
        }

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

        // Update user type if it was null (shouldn't happen, but safety check)
        if ($user->user_type === null && $userType !== null) {
            $user->update(['user_type' => $userType]);
            Log::info('Updated user type from null during OTP verification', [
                'email' => $email,
                'user_type' => $userType,
            ]);
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
     * Get throttle status with remaining wait time.
     *
     * @return array{is_throttled: bool, remaining_seconds: int, remaining_minutes: int, message: string}
     */
    public function getThrottleStatus(string $email): array
    {
        $maxRequests = config('passwordless.throttle.max_requests', 3);
        $throttleMinutes = $this->getThrottleMinutes();

        $recentRequests = OtpToken::where('email', $email)
            ->where('created_at', '>=', now()->subMinutes($throttleMinutes))
            ->orderBy('created_at', 'asc')
            ->get();

        $isThrottled = $recentRequests->count() >= $maxRequests;

        if (! $isThrottled) {
            return [
                'is_throttled' => false,
                'remaining_seconds' => 0,
                'remaining_minutes' => 0,
                'message' => '',
            ];
        }

        // Calculate remaining time based on oldest request in the throttle window
        $oldestRequest = $recentRequests->first();
        $throttleExpiresAt = $oldestRequest->created_at->addMinutes($throttleMinutes);
        $remainingSeconds = max(0, now()->diffInSeconds($throttleExpiresAt, false));
        $remainingMinutes = (int) ceil($remainingSeconds / 60);

        return [
            'is_throttled' => true,
            'remaining_seconds' => $remainingSeconds,
            'remaining_minutes' => $remainingMinutes,
            'message' => "Too many OTP requests. Please try again in {$remainingMinutes} minute(s).",
        ];
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
     * Request OTP for registration (new user signup).
     * For unified registration, userType is optional and will be selected after OTP verification.
     *
     * @throws \Exception
     */
    public function requestRegistrationOtp(string $email, ?string $userType = null): array
    {
        Log::info('Requesting registration OTP', [
            'email' => $email,
            'user_type' => $userType,
        ]);

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            Log::warning('Registration OTP request failed: User already exists', ['email' => $email]);
            throw new \Exception('An account with this email already exists. Please login instead.');
        }

        // Check throttling with detailed status
        $throttleStatus = $this->getThrottleStatus($email);
        if ($throttleStatus['is_throttled']) {
            throw new \Exception($throttleStatus['message']);
        }

        // Cleanup expired OTPs for this email
        OtpToken::where('email', $email)
            ->expired()
            ->delete();

        // Generate new OTP
        $otp = $this->generateOtp();
        $expiresAt = now()->addMinutes($this->getOtpExpiryMinutes());

        // Create or update OTP token (user_type can be null for unified registration)
        // For unified registration (null user_type), match only on email
        // For specific user_type, match on both email and user_type
        if ($userType === null) {
            // For unified registration, update/create based on email only
            $existingToken = OtpToken::where('email', $email)
                ->whereNull('user_type')
                ->first();

            if ($existingToken) {
                $existingToken->update([
                    'otp_code' => $otp,
                    'attempts' => 0,
                    'expires_at' => $expiresAt,
                    'verified_at' => null,
                ]);
            } else {
                OtpToken::create([
                    'email' => $email,
                    'user_type' => null,
                    'otp_code' => $otp,
                    'attempts' => 0,
                    'expires_at' => $expiresAt,
                    'verified_at' => null,
                ]);
            }
        } else {
            // For specific user_type, use updateOrCreate with both email and user_type
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
        }

        // Send OTP email
        $this->sendOtpEmail($email, $otp, $userType);

        Log::info('Registration OTP sent successfully', ['email' => $email]);

        return [
            'success' => true,
            'expires_at' => $expiresAt,
            'expiry_minutes' => $this->getOtpExpiryMinutes(),
        ];
    }

    /**
     * Verify registration OTP (without creating account).
     * Account creation happens after user type selection.
     *
     * @throws \Exception
     */
    public function verifyRegistrationOtp(string $email, string $otp): bool
    {
        Log::info('Verifying registration OTP', ['email' => $email]);

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            Log::warning('Registration OTP verification failed: User already exists', ['email' => $email]);
            throw new \Exception('An account with this email already exists. Please login instead.');
        }

        // Find valid OTP token (user_type can be any for registration)
        $otpToken = OtpToken::where('email', $email)
            ->where('otp_code', $otp)
            ->valid()
            ->first();

        if (! $otpToken) {
            // Increment attempts for existing token if found
            $existingToken = OtpToken::where('email', $email)
                ->unverified()
                ->first();

            if ($existingToken) {
                $existingToken->incrementAttempts();

                if ($existingToken->attempts >= $this->getMaxAttempts()) {
                    $existingToken->delete();
                    Log::warning('Registration OTP verification failed: Max attempts exceeded', ['email' => $email]);
                    throw new \Exception('Maximum verification attempts exceeded. Please request a new OTP.');
                }
            }

            Log::warning('Registration OTP verification failed: Invalid or expired OTP', ['email' => $email]);
            throw new \Exception('Invalid or expired OTP code.');
        }

        // Check if max attempts exceeded
        if ($otpToken->attempts >= $this->getMaxAttempts()) {
            $otpToken->delete();
            Log::warning('Registration OTP verification failed: Max attempts exceeded', ['email' => $email]);
            throw new \Exception('Maximum verification attempts exceeded. Please request a new OTP.');
        }

        // Check if expired
        if ($otpToken->isExpired()) {
            $otpToken->delete();
            Log::warning('Registration OTP verification failed: OTP expired', ['email' => $email]);
            throw new \Exception('OTP has expired. Please request a new one.');
        }

        // Mark as verified (but don't delete yet - we'll use it to track the verified email)
        $otpToken->markAsVerified();

        Log::info('Registration OTP verified successfully', ['email' => $email]);

        return true;
    }

    /**
     * Complete registration by creating user account after user type selection.
     *
     * @throws \Exception
     */
    public function completeRegistration(string $email, string $userType): User
    {
        Log::info('Completing registration', [
            'email' => $email,
            'user_type' => $userType,
        ]);

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            Log::warning('Registration completion failed: User already exists', ['email' => $email]);
            throw new \Exception('An account with this email already exists. Please login instead.');
        }

        // Verify that OTP was verified for this email
        $otpToken = OtpToken::where('email', $email)
            ->whereNotNull('verified_at')
            ->first();

        if (! $otpToken) {
            Log::warning('Registration completion failed: No verified OTP found', ['email' => $email]);
            throw new \Exception('OTP verification required. Please verify your email first.');
        }

        try {
            $user = DB::transaction(function () use ($email, $userType, $otpToken) {
                // Create new user (registration - user should not exist)
                $extractedName = $this->extractNameFromEmail($email);
                $nameParts = explode(' ', $extractedName, 2);

                $user = User::create([
                    'email' => $email,
                    'user_type' => $userType,
                    'user_type_checked' => true,
                    'password' => null,
                ]);

                Log::info('User created successfully', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'user_type' => $userType,
                ]);

                // Map 'university_admin' back to 'university' for role assignment (enum uses 'university')
                $roleName = $userType === 'university_admin' ? 'university' : $userType;
                $user->assignRole($roleName);

                // Cleanup the verified OTP
                $otpToken->delete();

                Log::info('Registration completed successfully', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);

                return $user;
            });

            // Send welcome email after user creation (outside transaction to not fail registration if email fails)
            try {
                $this->notificationService->sendWelcomeEmail($user);
            } catch (\Exception $e) {
                // Log error but don't fail registration if welcome email fails
                Log::error('Failed to send welcome email after registration: '.$e->getMessage(), [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            }

            return $user;
        } catch (\Exception $e) {
            Log::error('Failed to complete registration: '.$e->getMessage(), [
                'email' => $email,
                'user_type' => $userType,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to create account. Please try again.');
        }
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
