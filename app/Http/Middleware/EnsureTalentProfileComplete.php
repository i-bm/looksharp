<?php

namespace App\Http\Middleware;

use App\Enums\UserRoleEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTalentProfileComplete
{
    /**
     * Routes that should be accessible even with incomplete profile.
     *
     * @var array<string>
     */
    protected array $allowedRoutes = [
        'profile.build',
        'profile.build.step',
        'profile.build.save',
        'profile.photo.upload',
        'profile.education.remove',
        'profile.skill.remove',
        'profile.complete',
        'logout',
        'home',
    ];

    /**
     * Minimum completeness score required (0-100).
     */
    protected int $minCompletenessScore = 80;

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

        // Only apply to talent users
        if (! $user->hasRole(UserRoleEnum::TALENT->value)) {
            return $next($request);
        }

        // Check if current route is in allowed routes
        if ($this->isAllowedRoute($request)) {
            return $next($request);
        }

        // Check if profile exists
        $profile = $user->talentProfile;
        if (! $profile) {
            return redirect()->route('profile.build')
                ->with('error', 'Please complete your profile to continue.');
        }

        // Check if profile is complete
        if (! $this->isProfileComplete($profile)) {
            return redirect()->route('profile.build')
                ->with('info', 'Please complete your profile to access this page.');
        }

        return $next($request);
    }

    /**
     * Check if the current route is in the allowed routes list.
     */
    protected function isAllowedRoute(Request $request): bool
    {
        $routeName = $request->route()?->getName();

        if (! $routeName) {
            return false;
        }

        // Check exact route name match
        if (in_array($routeName, $this->allowedRoutes, true)) {
            return true;
        }

        // Check if route starts with profile.build (for step routes with parameters)
        if (str_starts_with($routeName, 'profile.build')) {
            return true;
        }

        return false;
    }

    /**
     * Check if the talent profile is complete.
     */
    protected function isProfileComplete($profile): bool
    {
        // Check completeness score
        if ($profile->profile_completeness_score < $this->minCompletenessScore) {
            return false;
        }

        // Additional checks: ensure core fields are filled
        $requiredFields = ['first_name', 'last_name', 'date_of_birth', 'gender', 'location'];
        foreach ($requiredFields as $field) {
            if (empty($profile->$field)) {
                return false;
            }
        }

        // Check if at least one education record exists
        if (! $profile->education()->exists()) {
            return false;
        }

        // Check if at least 3 skills exist
        if ($profile->skills()->count() < 3) {
            return false;
        }

        return true;
    }
}
