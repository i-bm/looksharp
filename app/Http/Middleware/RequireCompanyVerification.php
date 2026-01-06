<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\SubscriptionGateService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequireCompanyVerification
{
    public function __construct(private SubscriptionGateService $gateService) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasRole('employer')) {
            Log::warning('RequireCompanyVerification: User is not an employer', [
                'user_id' => $user?->id,
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'This feature is only available to employers.');
        }

        $company = $user->employerCompany();

        if (! $company) {
            Log::warning('RequireCompanyVerification: Employer has no company', [
                'user_id' => $user->id,
            ]);

            return redirect()->route('employer.company.show')
                ->with('error', 'Please complete your company profile first.');
        }

        // Check if company can access paid features
        if (! $this->gateService->canAccessPaidFeatures($company)) {
            $reason = $this->gateService->getGatingReason($company);

            Log::info('RequireCompanyVerification: Access denied', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'reason' => $reason,
            ]);

            return redirect()->route('employer.company.show')
                ->with('error', $reason ?? 'You do not have access to this feature.');
        }

        return $next($request);
    }
}

