<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\NotificationService;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RegistrationController extends Controller
{
    protected $authService;

    protected $registrationService;

    protected $notificationService;

    public function __construct(AuthService $authService, RegistrationService $registrationService, NotificationService $notificationService)
    {
        $this->authService = $authService;
        $this->registrationService = $registrationService;
        $this->notificationService = $notificationService;
        $this->middleware('guest');
    }

    /**
     * Show unified registration form (email entry only, no user type selection).
     */
    public function showRegistrationForm(Request $request)
    {
        // Clear any existing registration session when starting new registration
        $this->clearRegistrationSession($request);

        return view('auth.register.index');
    }

    /**
     * Handle registration OTP request (unified - no user type required).
     */
    public function requestRegistrationOtp(Request $request)
    {
        Log::info('Registration OTP request received');

        // Get email from request (initial request) or session (resend)
        $email = $request->input('email') ?? $request->session()->get('registration.email');

        if (! $email) {
            // If no email in request or session, validate it as required
            $validated = $request->validate([
                'email' => ['required', 'email', 'max:255'],
                'consent_to_privacy_policy' => ['required', 'accepted'],
            ]);
            $email = $validated['email'];
        } else {
            // Validate email format if provided
            $request->validate([
                'email' => ['nullable', 'email', 'max:255'],
                'consent_to_privacy_policy' => ['required', 'accepted'],
            ]);
        }

        try {
            // Request OTP without user type (unified registration)
            $result = $this->authService->requestRegistrationOtp($email);

            // Store email in session for OTP verification step
            $request->session()->put('registration.email', $email);
            // Store resend timestamp for countdown timer
            $request->session()->put('registration.otp_sent_at', now()->toIso8601String());

            Log::info('Registration OTP sent successfully', ['email' => $email]);

            return redirect()->route('register.verify.show')
                ->with('success', 'OTP has been sent to your email address.');

        } catch (\Exception $e) {
            // Get throttle status for better error messaging
            $throttleStatus = $this->authService->getThrottleStatus($email);

            $errorMessage = $e->getMessage();
            if ($throttleStatus['is_throttled']) {
                // Store throttle info in session for frontend countdown
                $request->session()->put('registration.throttle_info', [
                    'remaining_seconds' => $throttleStatus['remaining_seconds'],
                    'remaining_minutes' => $throttleStatus['remaining_minutes'],
                ]);
            }

            Log::error('Registration OTP request failed', [
                'email' => $email,
                'error' => $errorMessage,
            ]);

            return back()
                ->withInput()
                ->withErrors(['email' => $errorMessage]);
        }
    }

    /**
     * Show OTP verification form for registration.
     */
    public function showOtpVerification(Request $request)
    {
        // Retrieve email from session
        $email = $request->session()->get('registration.email');

        if (! $email) {
            return redirect()->route('register');
        }

        // Get resend timestamp and throttle info from session
        $otpSentAt = $request->session()->get('registration.otp_sent_at');
        $throttleInfo = $request->session()->get('registration.throttle_info');
        $countdownSeconds = config('passwordless.resend.countdown_seconds', 60);

        return view('auth.register.verify', [
            'email' => $email,
            'otpSentAt' => $otpSentAt,
            'throttleInfo' => $throttleInfo,
            'countdownSeconds' => $countdownSeconds,
        ]);
    }

    /**
     * Verify registration OTP and redirect to user type selection.
     */
    public function verifyRegistrationOtp(Request $request)
    {
        Log::info('Registration OTP verification request received');

        // Get email from session
        $email = $request->session()->get('registration.email');

        if (! $email) {
            Log::warning('Registration OTP verification failed: No email in session');

            return redirect()->route('register')
                ->withErrors(['otp' => 'Session expired. Please start registration again.']);
        }

        $validated = $request->validate([
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ]);

        try {
            // Verify OTP (doesn't create account yet)
            $this->authService->verifyRegistrationOtp($email, $validated['otp']);

            Log::info('Registration OTP verified successfully', ['email' => $email]);

            // Mark OTP as verified in session
            $request->session()->put('registration.otp_verified', true);

            // Redirect to user type selection
            return redirect()->route('register.select-type')
                ->with('success', 'Email verified! Please select your account type.');

        } catch (\Exception $e) {
            Log::error('Registration OTP verification failed', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['otp' => $e->getMessage()]);
        }
    }

    /**
     * Show user type selection page.
     */
    public function showUserTypeSelection(Request $request)
    {
        // Check if OTP was verified
        $email = $request->session()->get('registration.email');
        $otpVerified = $request->session()->get('registration.otp_verified', false);

        if (! $email || ! $otpVerified) {
            Log::warning('User type selection accessed without verified OTP', [
                'email' => $email,
                'otp_verified' => $otpVerified,
            ]);

            return redirect()->route('register')
                ->withErrors(['error' => 'Please verify your email first.']);
        }

        return view('auth.register.user-type-selection', [
            'email' => $email,
        ]);
    }

    /**
     * Handle user type selection and create account.
     */
    public function selectUserType(Request $request)
    {
        Log::info('User type selection request received');

        // Check if OTP was verified
        $email = $request->session()->get('registration.email');
        $otpVerified = $request->session()->get('registration.otp_verified', false);

        if (! $email || ! $otpVerified) {
            Log::warning('User type selection failed: No verified OTP', [
                'email' => $email,
                'otp_verified' => $otpVerified,
            ]);

            return redirect()->route('register')
                ->withErrors(['error' => 'Please verify your email first.']);
        }

        $validated = $request->validate([
            'user_type' => ['required', 'string', 'in:talent,employer,university'],
        ]);

        // Map 'university' to 'university_admin' for internal use
        $userType = $validated['user_type'] === 'university' ? 'university_admin' : $validated['user_type'];

        try {
            // Create user account with selected type
            $user = $this->authService->completeRegistration($email, $userType);

            Log::info('User account created successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'user_type' => $userType,
            ]);

            // Initialize talent profile if user type is talent
            if ($user->user_type === 'talent') {
                $this->registrationService->initializeTalentProfile($user);
                Log::info('Talent profile initialized', ['user_id' => $user->id]);
            }

            // Clear registration session data after successful registration
            $this->clearRegistrationSession($request);

            // Log the user in
            Auth::login($user, true); // Remember user

            Log::info('User logged in after registration', ['user_id' => $user->id]);

            // Redirect based on user type
            return $this->redirectAfterRegistration($user);

        } catch (\Exception $e) {
            Log::error('User type selection failed', [
                'email' => $email,
                'user_type' => $userType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['user_type' => $e->getMessage()]);
        }
    }

    /**
     * Redirect user after successful registration based on user type.
     */
    protected function redirectAfterRegistration($user)
    {
        $userType = $user->user_type;

        return match ($userType) {
            'talent' => redirect()->intended('/talent/profile/build'),
            'employer' => redirect()->intended('/dashboard'),
            'university_admin' => redirect()->intended('/dashboard'),
            default => redirect()->intended('/dashboard'),
        };
    }

    /**
     * Clear all registration session data.
     */
    protected function clearRegistrationSession(Request $request): void
    {
        $request->session()->forget([
            'registration.email',
            'registration.otp_verified',
            'registration.otp_sent_at',
            'registration.throttle_info',
        ]);
    }
}
