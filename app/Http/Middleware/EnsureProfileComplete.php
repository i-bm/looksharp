<?php

namespace App\Http\Middleware;

use App\Enums\UserRoleEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    /**
     * Routes that should always be accessible (never blocked).
     *
     * @var list<string>
     */
    protected array $alwaysAllowedRoutes = [
        'talent.profile.show',
        'talent.profile.edit',
        'talent.profile.update',
        'employer.company.show',
        'employer.company.update',
        'employer.company.store',
        'employer.company.submit',
        'employer.company.basic-info.update',
        'employer.company.contact-location.update',
        'employer.company.registration.update',
        'employer.company.primary-contact.update',
        'employer.company.branding.update',
        'logout',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check authenticated users
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $routeName = $request->route()?->getName();

        // Always allow access to profile edit routes, logout, and other essential routes
        if ($this->isAlwaysAllowedRoute($routeName)) {
            return $next($request);
        }

        // Check profile completion based on user role
        if ($user->hasRole(UserRoleEnum::TALENT->value)) {
            return $this->handleTalentProfile($request, $next, $user);
        }

        if ($user->hasRole(UserRoleEnum::EMPLOYER->value)) {
            return $this->handleEmployerProfile($request, $next, $user);
        }

        // For other user types (university, admin), allow access
        return $next($request);
    }

    /**
     * Handle talent profile completion check.
     */
    protected function handleTalentProfile(Request $request, Closure $next, $user): Response
    {
        $profile = $user->talentProfile;

        // If no profile exists, allow access (profile creation will be handled elsewhere)
        if (! $profile) {
            return $next($request);
        }

        $completionScore = $profile->profile_completeness_score ?? 0;
        $threshold = 70;

        // If profile is 70%+ complete, clear session flag and allow access
        if ($completionScore >= $threshold) {
            $request->session()->forget('profile_completion_prompted');

            return $next($request);
        }

        // If profile is below 70%, check if we've already prompted the user
        $hasBeenPrompted = $request->session()->get('profile_completion_prompted', false);

        if (! $hasBeenPrompted) {
            // First time - set flag and redirect to profile edit
            $request->session()->put('profile_completion_prompted', true);

            Log::info('EnsureProfileComplete: redirecting talent to profile edit (first time)', [
                'user_id' => $user->id,
                'completion_score' => $completionScore,
                'route' => $request->route()?->getName(),
            ]);

            return redirect()->route('talent.profile.edit')
                ->with('info', 'Please complete your profile to get the most out of Looksharp.');
        }

        // User has already been prompted - allow free navigation
        return $next($request);
    }

    /**
     * Handle employer profile completion check.
     */
    protected function handleEmployerProfile(Request $request, Closure $next, $user): Response
    {
        $company = $user->employerCompany();

        // If no company exists, redirect to show page (which will auto-create draft company)
        if (! $company) {
            $hasBeenPrompted = $request->session()->get('profile_completion_prompted', false);

            if (! $hasBeenPrompted) {
                // First time - set flag and redirect to company show
                $request->session()->put('profile_completion_prompted', true);

                Log::info('EnsureProfileComplete: redirecting employer to company show (no company exists)', [
                    'user_id' => $user->id,
                    'route' => $request->route()?->getName(),
                ]);

                return redirect()->route('employer.company.show')
                    ->with('info', 'Please create your company profile to get started.');
            }

            // User has already been prompted - allow free navigation
            return $next($request);
        }

        $completionScore = $company->profile_completeness_score ?? 0;
        $threshold = 70;

        // If profile is 70%+ complete, clear session flag and allow access
        if ($completionScore >= $threshold) {
            $request->session()->forget('profile_completion_prompted');

            return $next($request);
        }

        // If profile is below 70%, check if we've already prompted the user
        $hasBeenPrompted = $request->session()->get('profile_completion_prompted', false);

        if (! $hasBeenPrompted) {
            // First time - set flag and redirect to company show
            $request->session()->put('profile_completion_prompted', true);

            Log::info('EnsureProfileComplete: redirecting employer to company show (first time)', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'completion_score' => $completionScore,
                'route' => $request->route()?->getName(),
            ]);

            return redirect()->route('employer.company.show')
                ->with('info', 'Please complete your company profile to get the most out of Looksharp.');
        }

        // User has already been prompted - allow free navigation
        return $next($request);
    }

    /**
     * Check if the current route is always allowed (never blocked).
     */
    protected function isAlwaysAllowedRoute(?string $routeName): bool
    {
        if (! $routeName) {
            return false;
        }

        // Check exact route name match
        if (in_array($routeName, $this->alwaysAllowedRoutes, true)) {
            return true;
        }

        // Allow profile view and edit routes with prefixes
        if (str_starts_with($routeName, 'talent.profile.show') ||
            str_starts_with($routeName, 'talent.profile.edit') ||
            str_starts_with($routeName, 'talent.profile.update')) {
            return true;
        }

        if (str_starts_with($routeName, 'employer.company.show') ||
            str_starts_with($routeName, 'employer.company.update') ||
            str_starts_with($routeName, 'employer.company.store') ||
            str_starts_with($routeName, 'employer.company.submit') ||
            str_starts_with($routeName, 'employer.company.basic-info.update') ||
            str_starts_with($routeName, 'employer.company.contact-location.update') ||
            str_starts_with($routeName, 'employer.company.registration.update') ||
            str_starts_with($routeName, 'employer.company.primary-contact.update') ||
            str_starts_with($routeName, 'employer.company.branding.update')) {
            return true;
        }

        return false;
    }
}
