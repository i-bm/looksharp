<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PendingRegistration;
use App\Models\User;
use App\Services\AuthService;
use App\Services\NotificationService;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        // Apply guest middleware only to methods that should be guest-only
        // User type selection routes must be accessible to authenticated users
        // who need to complete their user type selection
        $this->middleware('guest')->only([
            'showRegistrationForm',
            'requestRegistrationOtp',
            'showOtpVerification',
            'verifyRegistrationOtp',
        ]);
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

        // Check if this is a resend (email already in session)
        $emailFromSession = $request->session()->get('registration.email');
        $isResend = ! empty($emailFromSession);

        if ($isResend) {
            // For resend, validate only consent
            $request->validate([
                'consent_to_privacy_policy' => ['required', 'accepted'],
            ]);
            $email = $emailFromSession;
        } else {
            // For initial request, validate all fields
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'phone_number' => ['nullable', 'string', 'max:20'],
                'email' => ['required', 'email', 'max:255'],
                'consent_to_privacy_policy' => ['required', 'accepted'],
            ]);
            $email = $validated['email'];

            // Store basic info in session
            $request->session()->put('registration.first_name', $validated['first_name']);
            $request->session()->put('registration.last_name', $validated['last_name']);
            $request->session()->put('registration.phone_number', $validated['phone_number'] ?? null);
        }

        try {
            // Request OTP without user type (unified registration)
            $result = $this->authService->requestRegistrationOtp($email);

            // Store email in session for OTP verification step
            $request->session()->put('registration.email', $email);
            // Store resend timestamp for countdown timer
            $request->session()->put('registration.otp_sent_at', now()->toIso8601String());

            Log::info('Registration OTP sent successfully', [
                'email' => $email,
                'first_name' => $request->session()->get('registration.first_name'),
            ]);

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

            // Create pending registration record for follow-up if user abandons
            try {
                $firstName = $request->session()->get('registration.first_name');
                $lastName = $request->session()->get('registration.last_name');
                $phoneNumber = $request->session()->get('registration.phone_number');

                PendingRegistration::updateOrCreate(
                    ['email' => $email],
                    [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'phone_number' => $phoneNumber,
                        'otp_verified_at' => now(),
                        'expires_at' => now()->addDays(7),
                    ]
                );

                Log::info('Pending registration record created', ['email' => $email]);
            } catch (\Exception $e) {
                // Log but don't fail registration if pending record creation fails
                Log::warning('Failed to create pending registration record', [
                    'email' => $email,
                    'error' => $e->getMessage(),
                ]);
            }

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
     * Accessible to both:
     * - Guest users during registration (with verified OTP in session)
     * - Authenticated users who need to complete their user type selection
     */
    public function showUserTypeSelection(Request $request)
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // If user already has user_type_checked, redirect to dashboard
            if ($user->user_type_checked) {
                Log::info('Authenticated user already has user_type_checked, redirecting to dashboard', [
                    'user_id' => $user->id,
                ]);

                return redirect()->route('dashboard');
            }

            Log::info('Showing user type selection to authenticated user', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return view('auth.register.user-type-selection', [
                'email' => $user->email,
                'is_authenticated' => true,
            ]);
        }

        // Guest user flow: Check if OTP was verified
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
            'is_authenticated' => false,
        ]);
    }

    /**
     * Handle user type selection and create account.
     * Handles both:
     * - Guest users during registration (creates new account)
     * - Authenticated users who need to complete their user type selection (updates existing account)
     */
    public function selectUserType(Request $request)
    {
        Log::info('User type selection request received');

        $validated = $request->validate([
            'user_type' => ['required', 'string', 'in:talent,employer,university'],
        ]);

        // Map 'university' to 'university_admin' for internal use
        $userType = $validated['user_type'] === 'university' ? 'university_admin' : $validated['user_type'];

        // Check if user is authenticated (completing user type selection for existing account)
        if (Auth::check()) {
            return $this->handleAuthenticatedUserTypeSelection($request, $userType);
        }

        // Guest user flow: Create new account
        return $this->handleGuestUserTypeSelection($request, $userType);
    }

    /**
     * Handle user type selection for authenticated users (updating existing account).
     */
    protected function handleAuthenticatedUserTypeSelection(Request $request, string $userType)
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            Log::error('Authenticated user not found in handleAuthenticatedUserTypeSelection');

            return redirect()->route('login')
                ->withErrors(['error' => 'Authentication required.']);
        }

        Log::info('Handling user type selection for authenticated user', [
            'user_id' => $user->id,
            'email' => $user->email,
            'user_type' => $userType,
        ]);

        try {
            return DB::transaction(function () use ($user, $userType) {
                // Update user type if not already set
                if (empty($user->user_type)) {
                    $user->user_type = $userType;
                    Log::info('User type updated', [
                        'user_id' => $user->id,
                        'user_type' => $userType,
                    ]);
                }

                // Mark user type as checked
                $user->user_type_checked = true;
                $user->save();

                Log::info('User type selection completed for authenticated user', [
                    'user_id' => $user->id,
                    'user_type' => $user->user_type,
                ]);

                // Assign role if user doesn't have any roles yet
                if ($user->roles->isEmpty()) {
                    // Map 'university_admin' back to 'university' for role assignment
                    // Use $user->user_type instead of $userType to ensure role matches actual user type
                    $roleName = $user->user_type === 'university_admin' ? 'university' : $user->user_type;
                    $user->assignRole($roleName);
                    Log::info('Role assigned to user', [
                        'user_id' => $user->id,
                        'role' => $roleName,
                    ]);
                }

                // Initialize talent profile if user type is talent and profile doesn't exist
                if ($user->user_type === 'talent' && ! $user->talentProfile) {
                    $this->registrationService->initializeTalentProfile($user);
                    Log::info('Talent profile initialized', ['user_id' => $user->id]);
                }

                Log::info('User type selection completed successfully', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);

                return redirect()->route('dashboard')
                    ->with('success', 'Account type selected successfully!');
            });
        } catch (\Exception $e) {
            Log::error('User type selection failed for authenticated user', [
                'user_id' => $user->id,
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
     * Handle user type selection for guest users (creating new account).
     */
    protected function handleGuestUserTypeSelection(Request $request, string $userType)
    {
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

        try {
            // Retrieve basic info from session
            $firstName = $request->session()->get('registration.first_name');
            $lastName = $request->session()->get('registration.last_name');
            $phoneNumber = $request->session()->get('registration.phone_number');

            // Create user account with selected type and basic info
            $user = $this->authService->completeRegistration($email, $userType, $firstName, $lastName, $phoneNumber);

            Log::info('User account created successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'user_type' => $userType,
                'first_name' => $firstName,
            ]);

            // Initialize talent profile if user type is talent
            if ($user->user_type === 'talent') {
                $this->registrationService->initializeTalentProfile($user);
                Log::info('Talent profile initialized', ['user_id' => $user->id]);
            }

            // Delete pending registration record since account was created
            try {
                PendingRegistration::where('email', $email)->delete();
                Log::info('Pending registration record deleted after successful registration', ['email' => $email]);
            } catch (\Exception $e) {
                // Log but don't fail if deletion fails
                Log::warning('Failed to delete pending registration record', [
                    'email' => $email,
                    'error' => $e->getMessage(),
                ]);
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
        // Redirect all users to dashboard after registration
        // Middleware will handle redirecting to profile edit if completion < 70%
        return redirect()->intended('/dashboard')
            ->with('success', 'Welcome to Looksharp!');
    }

    /**
     * Clear all registration session data.
     */
    protected function clearRegistrationSession(Request $request): void
    {
        $request->session()->forget([
            'registration.email',
            'registration.first_name',
            'registration.last_name',
            'registration.phone_number',
            'registration.otp_verified',
            'registration.otp_sent_at',
            'registration.throttle_info',
        ]);
    }
}
