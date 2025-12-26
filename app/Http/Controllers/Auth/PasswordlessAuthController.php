<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasswordlessAuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show unified login form.
     */
    public function showLoginForm()
    {
        return view('auth.login.index');
    }

    /**
     * Handle OTP request.
     */
    public function requestOtp(Request $request)
    {
        // Get email from request (initial request) or session (resend)
        $email = $request->input('email') ?? $request->session()->get('login.email');

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
            $result = $this->authService->requestOtp($email);

            // Store email in session for resend support
            $request->session()->put('login.email', $email);
            // Store resend timestamp for countdown timer
            $request->session()->put('login.otp_sent_at', now()->toIso8601String());

            return redirect()->route('login.verify.show')
                ->with('success', 'OTP has been sent to your email address.');

        } catch (\Exception $e) {
            // Get throttle status for better error messaging
            $throttleStatus = $this->authService->getThrottleStatus($email);

            $errorMessage = $e->getMessage();
            if ($throttleStatus['is_throttled']) {
                // Store throttle info in session for frontend countdown
                $request->session()->put('login.throttle_info', [
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
     * Show OTP verification form.
     */
    public function showOtpVerification(Request $request)
    {
        $email = $request->session()->get('login.email');

        if (! $email) {
            return redirect()->route('login');
        }

        // Get resend timestamp and throttle info from session
        $otpSentAt = $request->session()->get('login.otp_sent_at');
        $throttleInfo = $request->session()->get('login.throttle_info');
        $countdownSeconds = config('passwordless.resend.countdown_seconds', 60);

        return view('auth.login.verify', [
            'email' => $email,
            'otpSentAt' => $otpSentAt,
            'throttleInfo' => $throttleInfo,
            'countdownSeconds' => $countdownSeconds,
        ]);
    }

    /**
     * Verify OTP and authenticate user.
     */
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
            'email' => ['required', 'email'],
        ]);

        try {
            $user = $this->authService->verifyOtp(
                $validated['email'],
                $validated['otp']
            );

            if ($user) {
                Auth::login($user, true); // Remember user

                // Redirect based on user type
                return $this->redirectAfterLogin($user);
            }

            return back()
                ->withInput()
                ->withErrors(['otp' => 'Invalid OTP code.']);

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['otp' => $e->getMessage()]);
        }
    }

    /**
     * Redirect user after successful login based on user type.
     */
    protected function redirectAfterLogin($user)
    {
        // Check if user has admin role and redirect to admin dashboard
        if ($user->hasRole('admin')) {
            return redirect()->intended('/admin/dashboard');
        }

        $userType = $user->user_type;

        return match ($userType) {
            'talent' => redirect()->intended('/dashboard'),
            'employer' => redirect()->intended('/dashboard'),
            'university_admin' => redirect()->intended('/dashboard'),
            default => redirect()->intended('/dashboard'),
        };
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
