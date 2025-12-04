<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PasswordlessAuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show login form for user type.
     */
    public function showLoginForm(?string $userType = null)
    {
        $validUserTypes = ['talent', 'employer', 'university'];

        // Validate user type
        if ($userType && ! in_array($userType, $validUserTypes)) {
            abort(404);
        }

        // Map 'university' to 'university_admin' for internal use
        $internalUserType = $userType === 'university' ? 'university_admin' : $userType;

        $view = $userType
            ? "auth.login.{$userType}"
            : 'auth.login.talent'; // Default to talent

        return view($view, [
            'userType' => $internalUserType,
            'displayUserType' => $userType ?? 'talent',
        ]);
    }

    /**
     * Handle OTP request.
     */
    public function requestOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'user_type' => ['nullable', Rule::in(['talent', 'employer', 'university_admin'])],
        ]);

        try {
            $result = $this->authService->requestOtp(
                $validated['email'],
                $validated['user_type'] ?? null
            );

            return redirect()->route('login.verify.show')
                ->with('email', $validated['email'])
                ->with('user_type', $validated['user_type'] ?? null)
                ->with('success', 'OTP has been sent to your email address.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['email' => $e->getMessage()]);
        }
    }

    /**
     * Show OTP verification form.
     */
    public function showOtpVerification(Request $request)
    {
        $email = $request->session()->get('email');
        $userType = $request->session()->get('user_type');

        if (! $email) {
            return redirect()->route('login');
        }

        return view('auth.login.verify', [
            'email' => $email,
            'userType' => $userType,
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
            'user_type' => ['nullable', Rule::in(['talent', 'employer', 'university_admin'])],
        ]);

        try {
            $user = $this->authService->verifyOtp(
                $validated['email'],
                $validated['otp'],
                $validated['user_type'] ?? null
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
        $userType = $user->user_type;

        return match ($userType) {
            'talent' => redirect()->intended('/home'),
            'employer' => redirect()->intended('/home'),
            'university_admin' => redirect()->intended('/home'),
            default => redirect()->intended('/home'),
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
