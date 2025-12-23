<?php

namespace App\Http\Middleware;

use App\Enums\UserRoleEnum;
use App\Services\EmployerCompanyService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfCompanyComplete
{
    protected EmployerCompanyService $employerCompanyService;

    public function __construct(EmployerCompanyService $employerCompanyService)
    {
        $this->employerCompanyService = $employerCompanyService;
    }

    /**
     * Handle an incoming request.
     * Redirects users with complete company profiles away from company building pages to their company page.
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

        // Only apply to company building routes
        $routeName = $request->route()?->getName();
        if (! $routeName || ! $this->isCompanyBuildRoute($routeName)) {
            return $next($request);
        }

        // Check if company exists
        $company = $user->employerCompany();
        if (! $company) {
            Log::info('RedirectIfCompanyComplete: no company found, allowing access', [
                'user_id' => $user->id,
                'route' => $routeName,
            ]);

            return $next($request);
        }

        // Check if company wizard is complete
        if ($this->isCompanyComplete($company)) {
            Log::info('RedirectIfCompanyComplete: wizard complete, redirecting to company page', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'route' => $routeName,
                'wizard_complete' => $company->wizard_complete,
            ]);

            // Redirect to company page if trying to access build pages
            return redirect()->route('employer.company.show')
                ->with('info', 'Your company profile is already complete. You can edit it from your company page.');
        }

        Log::debug('RedirectIfCompanyComplete: wizard incomplete, allowing access to wizard', [
            'user_id' => $user->id,
            'company_id' => $company->id,
            'route' => $routeName,
            'wizard_complete' => $company->wizard_complete,
        ]);

        return $next($request);
    }

    /**
     * Check if the route is a company building route.
     */
    protected function isCompanyBuildRoute(string $routeName): bool
    {
        $buildRoutes = [
            'employer.company.build',
            'employer.company.build.step',
            'employer.company.build.save',
            'employer.company.complete',
        ];

        return in_array($routeName, $buildRoutes, true) ||
               str_starts_with($routeName, 'employer.company.build');
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
