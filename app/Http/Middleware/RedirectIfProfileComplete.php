<?php

namespace App\Http\Middleware;

use App\Enums\UserRoleEnum;
use App\Services\ProfileService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfProfileComplete
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Handle an incoming request.
     * Redirects users with complete profiles away from profile building pages to their profile page.
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

        // Only apply to talent users
        if (! $user->hasRole(UserRoleEnum::TALENT->value)) {
            return $next($request);
        }

        // Only apply to profile building routes
        $routeName = $request->route()?->getName();
        if (! $routeName || ! $this->isProfileBuildRoute($routeName)) {
            return $next($request);
        }

        // Check if profile exists
        $profile = $user->talentProfile;
        if (! $profile) {
            return $next($request);
        }

        // Check if profile is complete
        if ($this->isProfileComplete($profile)) {
            // Redirect to profile page if trying to access build pages
            return redirect()->route('talent.profile.show')
                ->with('info', 'Your profile is already complete. You can edit it from your profile page.');
        }

        return $next($request);
    }

    /**
     * Check if the route is a profile building route.
     */
    protected function isProfileBuildRoute(string $routeName): bool
    {
        $buildRoutes = [
            'talent.profile.build',
            'talent.profile.build.step',
            'talent.profile.build.save',
            'talent.profile.complete',
        ];

        return in_array($routeName, $buildRoutes, true) || 
               str_starts_with($routeName, 'talent.profile.build');
    }

    /**
     * Check if the talent profile is complete.
     */
    protected function isProfileComplete($profile): bool
    {
        $progress = $this->profileService->getWizardProgress($profile);

        // Check if all steps are completed
        foreach ($progress['steps'] as $stepData) {
            if (! $stepData['completed']) {
                return false;
            }
        }

        // Additional check: ensure completeness score is reasonable
        if ($profile->profile_completeness_score < 70) {
            return false;
        }

        return true;
    }
}

