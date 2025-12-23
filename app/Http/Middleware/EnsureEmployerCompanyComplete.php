<?php

namespace App\Http\Middleware;

use App\Enums\UserRoleEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployerCompanyComplete
{
    /**
     * Routes that should be accessible even with incomplete company wizard.
     *
     * @var list<string>
     */
    protected array $allowedRoutes = [
        'employer.company.build',
        'employer.company.build.step',
        'employer.company.build.save',
        'employer.company.complete',
        'employer.company.show',
        'employer.company.edit',
        'employer.company.update',
        'employer.company.store',
        'employer.company.submit',
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

        // Only apply to employer users
        if (! $user->hasRole(UserRoleEnum::EMPLOYER->value)) {
            return $next($request);
        }

        // Check if current route is in allowed routes
        if ($this->isAllowedRoute($request)) {
            Log::debug('EnsureEmployerCompanyComplete: route is allowed', [
                'user_id' => $user->id,
                'route' => $request->route()?->getName(),
            ]);
            return $next($request);
        }

        // Check if company exists
        $company = $user->employerCompany();
        if (! $company) {
            Log::info('EnsureEmployerCompanyComplete: no company found, redirecting to wizard', [
                'user_id' => $user->id,
                'route' => $request->route()?->getName(),
            ]);
            return redirect()->route('employer.company.build')
                ->with('info', 'Please complete your company profile to continue.');
        }

        // Check if company wizard is complete
        if (! $this->isCompanyComplete($company)) {
            Log::info('EnsureEmployerCompanyComplete: wizard incomplete, redirecting to wizard', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'route' => $request->route()?->getName(),
                'wizard_complete' => $company->wizard_complete,
            ]);
            return redirect()->route('employer.company.build')
                ->with('info', 'Please complete your company profile to access this page.');
        }

        Log::debug('EnsureEmployerCompanyComplete: wizard complete, allowing access', [
            'user_id' => $user->id,
            'company_id' => $company->id,
            'route' => $request->route()?->getName(),
        ]);

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

        // Check if route starts with employer.company.build (for step routes with parameters)
        if (str_starts_with($routeName, 'employer.company.build')) {
            return true;
        }

        // Allow company view and edit routes
        if (str_starts_with($routeName, 'employer.company.show') ||
            str_starts_with($routeName, 'employer.company.edit') ||
            str_starts_with($routeName, 'employer.company.update') ||
            str_starts_with($routeName, 'employer.company.store') ||
            str_starts_with($routeName, 'employer.company.submit')) {
            return true;
        }

        return false;
    }

    /**
     * Check if the company wizard is complete.
     */
    protected function isCompanyComplete($company): bool
    {
        // Use the wizard_complete boolean column for efficient checking
        return $company->wizard_complete === true;
    }
}

