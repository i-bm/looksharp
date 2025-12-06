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
        'talent.profile.build',
        'talent.profile.build.step',
        'talent.profile.build.save',
        'talent.profile.photo.upload',
        'talent.profile.resume.upload',
        'talent.profile.education.remove',
        'talent.profile.skill.remove',
        'talent.profile.complete',
        'talent.profile.show',
        'talent.profile.edit',
        'talent.profile.update',
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
            return redirect()->route('talent.profile.build')
                ->with('error', 'Please complete your profile to continue.');
        }

        // Check if profile building step is completed
        if (! $this->isProfileComplete($profile)) {
            return redirect()->route('talent.profile.build')
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

        // Check if route starts with talent.profile.build (for step routes with parameters)
        if (str_starts_with($routeName, 'talent.profile.build')) {
            return true;
        }

        // Allow profile view and edit routes
        if (str_starts_with($routeName, 'talent.profile.show') ||
            str_starts_with($routeName, 'talent.profile.edit') ||
            str_starts_with($routeName, 'talent.profile.update')) {
            return true;
        }

        return false;
    }

    /**
     * Check if the talent profile is complete.
     */
    protected function isProfileComplete($profile): bool
    {
        return $profile->is_profile_building_step_completed === true;
    }
}
