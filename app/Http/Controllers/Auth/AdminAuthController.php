<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminAuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show admin login form.
     */
    public function showLoginForm()
    {
        return view('auth.admin.login');
    }

    /**
     * Handle OTP request for admin login.
     */
    public function requestOtp(Request $request)
    {
        // Get email from request (initial request) or session (resend)
        $email = $request->input('email') ?? $request->session()->get('admin.login.email');

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

        // Check if user exists and has admin role
        $user = \App\Models\User::where('email', $email)->first();
        if (! $user) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'No account found with this email.']);
        }

        if (! $user->hasRole('admin')) {
            Log::warning('Non-admin user attempted to access admin login', [
                'email' => $email,
                'user_id' => $user->id,
            ]);

            return back()
                ->withInput()
                ->withErrors(['email' => 'Access denied. Admin privileges required.']);
        }

        try {
            $result = $this->authService->requestOtp(
                $email,
                'admin'
            );

            // Store email in session for resend support
            $request->session()->put('admin.login.email', $email);
            // Store resend timestamp for countdown timer
            $request->session()->put('admin.login.otp_sent_at', now()->toIso8601String());

            Log::info('Admin OTP requested', [
                'email' => $email,
            ]);

            return redirect()->route('admin.login.verify.show')
                ->with('success', 'OTP has been sent to your email address.');

        } catch (\Exception $e) {
            // Get throttle status for better error messaging
            $throttleStatus = $this->authService->getThrottleStatus($email);

            $errorMessage = $e->getMessage();
            if ($throttleStatus['is_throttled']) {
                // Store throttle info in session for frontend countdown
                $request->session()->put('admin.login.throttle_info', [
                    'remaining_seconds' => $throttleStatus['remaining_seconds'],
                    'remaining_minutes' => $throttleStatus['remaining_minutes'],
                ]);
            }

            Log::error('Admin OTP request failed', [
                'email' => $email,
                'error' => $errorMessage,
            ]);

            return back()
                ->withInput()
                ->withErrors(['email' => $errorMessage]);
        }
    }

    /**
     * Show OTP verification form for admin.
     */
    public function showOtpVerification(Request $request)
    {
        $email = $request->session()->get('admin.login.email');

        if (! $email) {
            return redirect()->route('admin.login');
        }

        // Get resend timestamp and throttle info from session
        $otpSentAt = $request->session()->get('admin.login.otp_sent_at');
        $throttleInfo = $request->session()->get('admin.login.throttle_info');
        $countdownSeconds = config('passwordless.resend.countdown_seconds', 60);

        return view('auth.admin.verify', [
            'email' => $email,
            'otpSentAt' => $otpSentAt,
            'throttleInfo' => $throttleInfo,
            'countdownSeconds' => $countdownSeconds,
        ]);
    }

    /**
     * Verify OTP and authenticate admin user.
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
                $validated['otp'],
                'admin'
            );

            if ($user) {
                // Verify user has admin role
                if (! $user->hasRole('admin')) {
                    Log::warning('Non-admin user attempted to authenticate via admin login', [
                        'email' => $validated['email'],
                        'user_id' => $user->id,
                    ]);

                    return back()
                        ->withInput()
                        ->withErrors(['otp' => 'Access denied. Admin privileges required.']);
                }

                Auth::login($user, true); // Remember user

                Log::info('Admin user authenticated successfully', [
                    'email' => $validated['email'],
                    'user_id' => $user->id,
                ]);

                // Clear session data
                $request->session()->forget([
                    'admin.login.email',
                    'admin.login.otp_sent_at',
                    'admin.login.throttle_info',
                ]);

                // Redirect to admin dashboard
                return redirect()->intended('/admin/dashboard');
            }

            return back()
                ->withInput()
                ->withErrors(['otp' => 'Invalid OTP code.']);

        } catch (\Exception $e) {
            Log::error('Admin OTP verification failed', [
                'email' => $validated['email'],
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['otp' => $e->getMessage()]);
        }
    }

    /**
     * Logout admin user.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        Log::info('Admin user logged out', [
            'user_id' => $user->id ?? null,
            'email' => $user->email ?? null,
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
