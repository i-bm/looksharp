<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EmployerCompanyStatusEnum;
use App\Enums\EmployerCompanyVerificationStatusEnum;
use App\Enums\SubscriptionTierEnum;
use App\Models\EmployerCompany;
use Illuminate\Support\Facades\Log;

class SubscriptionGateService
{
    /**
     * Check if company can access paid subscription features.
     * Requires: approved status + verified status + active paid subscription.
     */
    public function canAccessPaidFeatures(EmployerCompany $company): bool
    {
        Log::debug('SubscriptionGateService: Checking paid features access', [
            'company_id' => $company->id,
            'status' => $company->status,
            'verification_status' => $company->verification_status,
        ]);

        // Must be approved
        if ($company->status !== EmployerCompanyStatusEnum::APPROVED->value) {
            Log::debug('SubscriptionGateService: Company not approved', [
                'company_id' => $company->id,
                'status' => $company->status,
            ]);

            return false;
        }

        // Must be verified
        if ($company->verification_status !== EmployerCompanyVerificationStatusEnum::VERIFIED->value) {
            Log::debug('SubscriptionGateService: Company not verified', [
                'company_id' => $company->id,
                'verification_status' => $company->verification_status,
            ]);

            return false;
        }

        // Must have active paid subscription
        $subscription = $company->getActiveSubscription();
        if ($subscription === null) {
            Log::debug('SubscriptionGateService: No active subscription', [
                'company_id' => $company->id,
            ]);

            return false;
        }

        $tier = $subscription->getTierEnum();
        if ($tier === SubscriptionTierEnum::FREE) {
            Log::debug('SubscriptionGateService: Subscription is free tier', [
                'company_id' => $company->id,
            ]);

            return false;
        }

        if (! $subscription->isActive()) {
            Log::debug('SubscriptionGateService: Subscription not active', [
                'company_id' => $company->id,
                'subscription_status' => $subscription->status,
            ]);

            return false;
        }

        Log::debug('SubscriptionGateService: Company can access paid features', [
            'company_id' => $company->id,
        ]);

        return true;
    }

    /**
     * Check if company can post opportunities.
     * Free tier: only needs approval (no verification required).
     * Paid tiers: requires approval + verification + active subscription.
     */
    public function canPostOpportunity(EmployerCompany $company): bool
    {
        Log::debug('SubscriptionGateService: Checking opportunity posting access', [
            'company_id' => $company->id,
            'status' => $company->status,
            'verification_status' => $company->verification_status,
        ]);

        // Must be approved
        if ($company->status !== EmployerCompanyStatusEnum::APPROVED->value) {
            Log::debug('SubscriptionGateService: Company not approved for posting', [
                'company_id' => $company->id,
                'status' => $company->status,
            ]);

            return false;
        }

        // Check subscription tier
        $subscription = $company->getActiveSubscription();
        if ($subscription === null) {
            Log::debug('SubscriptionGateService: No active subscription for posting', [
                'company_id' => $company->id,
            ]);

            return false;
        }

        $tier = $subscription->getTierEnum();

        // Free tier: only needs approval
        if ($tier === SubscriptionTierEnum::FREE) {
            if (! $subscription->isActive()) {
                Log::debug('SubscriptionGateService: Free subscription not active', [
                    'company_id' => $company->id,
                ]);

                return false;
            }

            Log::debug('SubscriptionGateService: Company can post (free tier)', [
                'company_id' => $company->id,
            ]);

            return true;
        }

        // Paid tiers: require verification + active subscription
        if ($company->verification_status !== EmployerCompanyVerificationStatusEnum::VERIFIED->value) {
            Log::debug('SubscriptionGateService: Paid tier requires verification', [
                'company_id' => $company->id,
                'verification_status' => $company->verification_status,
                'tier' => $tier->value,
            ]);

            return false;
        }

        if (! $subscription->isActive()) {
            Log::debug('SubscriptionGateService: Paid subscription not active', [
                'company_id' => $company->id,
                'subscription_status' => $subscription->status,
            ]);

            return false;
        }

        Log::debug('SubscriptionGateService: Company can post (paid tier)', [
            'company_id' => $company->id,
            'tier' => $tier->value,
        ]);

        return true;
    }

    /**
     * Get human-readable reason if company is gated from accessing features.
     */
    public function getGatingReason(EmployerCompany $company): ?string
    {
        // Check approval status
        if ($company->status !== EmployerCompanyStatusEnum::APPROVED->value) {
            $statusMessages = [
                EmployerCompanyStatusEnum::DRAFT->value => 'Your company profile is still in draft. Please submit it for review.',
                EmployerCompanyStatusEnum::SUBMITTED->value => 'Your company profile is pending approval.',
                EmployerCompanyStatusEnum::NEEDS_CHANGES->value => 'Your company profile needs changes before it can be approved.',
                EmployerCompanyStatusEnum::REJECTED->value => 'Your company profile has been rejected.',
                EmployerCompanyStatusEnum::SUSPENDED->value => 'Your company profile has been suspended.',
            ];

            return $statusMessages[$company->status] ?? 'Your company profile is not approved.';
        }

        // Check subscription
        $subscription = $company->getActiveSubscription();
        if ($subscription === null) {
            return 'You do not have an active subscription. Please subscribe to access paid features.';
        }

        $tier = $subscription->getTierEnum();

        // For paid tiers, check verification
        if ($tier !== SubscriptionTierEnum::FREE) {
            if ($company->verification_status !== EmployerCompanyVerificationStatusEnum::VERIFIED->value) {
                $verificationMessages = [
                    EmployerCompanyVerificationStatusEnum::PENDING->value => 'Your company verification is pending. Please wait for admin review.',
                    EmployerCompanyVerificationStatusEnum::REJECTED->value => 'Your company verification has been rejected. Please contact support.',
                ];

                return $verificationMessages[$company->verification_status] ?? 'Your company must be verified to access paid features.';
            }

            if (! $subscription->isActive()) {
                return 'Your subscription is not active. Please complete payment or contact support.';
            }
        } else {
            // Free tier
            if (! $subscription->isActive()) {
                return 'Your free subscription is not active.';
            }
        }

        return null; // No gating reason - access allowed
    }
}
