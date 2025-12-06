<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    protected $authService;

    protected $registrationService;

    public function __construct(AuthService $authService, RegistrationService $registrationService)
    {
        $this->authService = $authService;
        $this->registrationService = $registrationService;
        $this->middleware('guest');
    }

    /**
     * Show registration form for user type.
     */
    public function showRegistrationForm(Request $request, ?string $userType = null)
    {
        $validUserTypes = [UserRoleEnum::TALENT->value, UserRoleEnum::EMPLOYER->value, UserRoleEnum::UNIVERSITY->value];

        // Validate user type
        if ($userType && ! in_array($userType, $validUserTypes)) {
            abort(404);
        }

        // Map 'university' to 'university_admin' for internal use
        $internalUserType = $userType === 'university' ? 'university_admin' : $userType;

        // Default to talent if no user type specified
        $userType = $userType ?? UserRoleEnum::TALENT->value;
        $internalUserType = $internalUserType ?? UserRoleEnum::TALENT->value;

        // Clear any existing registration session when starting new registration
        $this->clearRegistrationSession($request);

        // Store user_type in session for secure multi-step flow
        $request->session()->put('registration.user_type', $internalUserType);

        $view = "auth.register.{$userType}";

        return view($view, [
            'userType' => $internalUserType,
            'displayUserType' => $userType,
        ]);
    }

    /**
     * Show email registration form.
     */
    public function showEmailRegistration(Request $request)
    {
        // Retrieve user_type from session (secure, server-side only)
        $userType = $this->validateRegistrationSession($request);

        if (! $userType) {
            // If no valid session, redirect to registration selection
            return redirect()->route('register');
        }

        // Map 'university_admin' to 'university' for display
        $displayUserType = $userType === 'university_admin' ? 'university' : $userType;

        return view('auth.register.email', [
            'userType' => $userType,
            'displayUserType' => $displayUserType,
        ]);
    }

    /**
     * Handle registration OTP request.
     */
    public function requestRegistrationOtp(Request $request)
    {
        // Get user_type from session (secure, server-side only)
        $userType = $this->validateRegistrationSession($request);

        if (! $userType) {
            return redirect()->route('register')
                ->withErrors(['email' => 'Please select a registration type first.']);
        }

        // Get email from request (initial request) or session (resend)
        $email = $request->input('email') ?? $request->session()->get('registration.email');

        if (! $email) {
            // If no email in request or session, validate it as required
            $validated = $request->validate([
                'email' => ['required', 'email', 'max:255'],
            ]);
            $email = $validated['email'];
        } else {
            // Validate email format if provided
            $request->validate([
                'email' => ['nullable', 'email', 'max:255'],
            ]);
        }

        try {
            $result = $this->authService->requestRegistrationOtp(
                $email,
                $userType
            );

            // Store email in session for OTP verification step
            $request->session()->put('registration.email', $email);
            // Store resend timestamp for countdown timer
            $request->session()->put('registration.otp_sent_at', now()->toIso8601String());

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
        // Retrieve email and user_type from session (secure, server-side only)
        $email = $request->session()->get('registration.email');
        $userType = $this->validateRegistrationSession($request);

        if (! $email || ! $userType) {
            return redirect()->route('register');
        }

        // Map 'university_admin' to 'university' for display
        $displayUserType = $userType === 'university_admin' ? 'university' : $userType;

        // Get resend timestamp and throttle info from session
        $otpSentAt = $request->session()->get('registration.otp_sent_at');
        $throttleInfo = $request->session()->get('registration.throttle_info');
        $countdownSeconds = config('passwordless.resend.countdown_seconds', 60);

        return view('auth.register.verify', [
            'email' => $email,
            'userType' => $userType,
            'displayUserType' => $displayUserType,
            'otpSentAt' => $otpSentAt,
            'throttleInfo' => $throttleInfo,
            'countdownSeconds' => $countdownSeconds,
        ]);
    }

    /**
     * Verify registration OTP and create account.
     */
    public function verifyRegistrationOtp(Request $request)
    {
        // Get user_type and email from session (secure, server-side only)
        $userType = $this->validateRegistrationSession($request);
        $email = $request->session()->get('registration.email');

        if (! $userType || ! $email) {
            return redirect()->route('register')
                ->withErrors(['otp' => 'Session expired. Please start registration again.']);
        }

        $validated = $request->validate([
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ]);

        try {
            $user = $this->authService->verifyRegistrationOtp(
                $email,
                $validated['otp'],
                $userType
            );

            logger()->info('User verified', ['user' => $user]);
            // Initialize talent profile if user type is talent
            if ($user->user_type === 'talent') {
                $this->registrationService->initializeTalentProfile($user);
            }

            // Clear registration session data after successful registration
            $this->clearRegistrationSession($request);

            // Log the user in
            Auth::login($user, true); // Remember user

            // Redirect based on user type
            return $this->redirectAfterRegistration($user);

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['otp' => $e->getMessage()]);
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
     * Validate registration session and return user_type if valid.
     *
     * @return string|null Returns user_type if valid, null otherwise
     */
    protected function validateRegistrationSession(Request $request): ?string
    {
        $userType = $request->session()->get('registration.user_type');
        $validUserTypes = ['talent', 'employer', 'university_admin'];

        if (! $userType || ! in_array($userType, $validUserTypes)) {
            return null;
        }

        return $userType;
    }

    /**
     * Clear all registration session data.
     */
    protected function clearRegistrationSession(Request $request): void
    {
        $request->session()->forget([
            'registration.user_type',
            'registration.email',
        ]);
    }
}
