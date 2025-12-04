<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
    public function showRegistrationForm(?string $userType = null)
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
        $userType = $request->get('user_type', 'talent');
        $validUserTypes = ['talent', 'employer', 'university_admin'];

        if (! in_array($userType, $validUserTypes)) {
            $userType = 'talent';
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
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'user_type' => ['required', Rule::in(['talent', 'employer', 'university_admin'])],
        ]);

        try {
            $result = $this->authService->requestRegistrationOtp(
                $validated['email'],
                $validated['user_type']
            );

            return redirect()->route('register.verify.show')
                ->with('email', $validated['email'])
                ->with('user_type', $validated['user_type'])
                ->with('success', 'OTP has been sent to your email address.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['email' => $e->getMessage()]);
        }
    }

    /**
     * Show OTP verification form for registration.
     */
    public function showOtpVerification(Request $request)
    {
        $email = $request->session()->get('email');
        $userType = $request->session()->get('user_type');

        if (! $email || ! $userType) {
            return redirect()->route('register');
        }

        // Map 'university_admin' to 'university' for display
        $displayUserType = $userType === 'university_admin' ? 'university' : $userType;

        return view('auth.register.verify', [
            'email' => $email,
            'userType' => $userType,
            'displayUserType' => $displayUserType,
        ]);
    }

    /**
     * Verify registration OTP and create account.
     */
    public function verifyRegistrationOtp(Request $request)
    {
        $validated = $request->validate([
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
            'email' => ['required', 'email'],
            'user_type' => ['required', Rule::in(['talent', 'employer', 'university_admin'])],
        ]);

        try {
            $user = $this->authService->verifyRegistrationOtp(
                $validated['email'],
                $validated['otp'],
                $validated['user_type']
            );

            logger()->info('User verified', ['user' => $user]);
            // Initialize talent profile if user type is talent
            if ($user->user_type === 'talent') {
                $this->registrationService->initializeTalentProfile($user);
            }

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
            'talent' => redirect()->intended('/profile/build'),
            'employer' => redirect()->intended('/dashboard'),
            'university_admin' => redirect()->intended('/dashboard'),
            default => redirect()->intended('/dashboard'),
        };
    }
}
