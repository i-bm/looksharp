<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BillingCycleEnum;
use App\Enums\EmployerCompanyStatusEnum;
use App\Enums\EmployerCompanyVerificationStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\SubscriptionTierEnum;
use App\Models\EmployerCompany;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    public function __construct(
        private NotificationService $notificationService,
        private PaymentService $paymentService
    ) {}

    /**
     * Create a new subscription for an employer company.
     */
    public function createSubscription(
        EmployerCompany $company,
        SubscriptionTierEnum $tier,
        ?BillingCycleEnum $billingCycle = null
    ): Subscription {
        Log::info('SubscriptionService: Creating subscription', [
            'company_id' => $company->id,
            'tier' => $tier->value,
            'billing_cycle' => $billingCycle?->value,
        ]);

        try {
            return DB::transaction(function () use ($company, $tier, $billingCycle) {
                // For paid tiers, check company approval and verification
                if ($tier !== SubscriptionTierEnum::FREE) {
                    if ($company->status !== EmployerCompanyStatusEnum::APPROVED->value) {
                        Log::warning('SubscriptionService: Company not approved for paid subscription', [
                            'company_id' => $company->id,
                            'status' => $company->status,
                            'tier' => $tier->value,
                        ]);
                        throw new \Exception('Your company must be approved before subscribing to paid plans.');
                    }

                    if ($company->verification_status !== EmployerCompanyVerificationStatusEnum::VERIFIED->value) {
                        Log::warning('SubscriptionService: Company not verified for paid subscription', [
                            'company_id' => $company->id,
                            'verification_status' => $company->verification_status,
                            'tier' => $tier->value,
                        ]);
                        throw new \Exception('Your company must be verified before subscribing to paid plans. Please complete verification first.');
                    }
                }

                // Get package configuration
                $packageConfig = config("subscriptions.packages.{$tier->value}");

                if ($packageConfig === null) {
                    throw new \Exception("Invalid subscription tier: {$tier->value}");
                }

                // Calculate amount and dates
                $amount = 0;
                $startsAt = now();
                $endsAt = null;
                $renewsAt = null;

                if ($tier !== SubscriptionTierEnum::FREE) {
                    if ($billingCycle === null) {
                        throw new \Exception('Billing cycle is required for paid subscriptions');
                    }

                    $pricing = $packageConfig['pricing'] ?? [];
                    $amount = $pricing[$billingCycle->value] ?? 0;

                    if ($billingCycle === BillingCycleEnum::MONTHLY) {
                        $endsAt = $startsAt->copy()->addMonth();
                        $renewsAt = $endsAt;
                    } else {
                        $endsAt = $startsAt->copy()->addYear();
                        $renewsAt = $endsAt;
                    }
                }

                // Determine initial status
                $status = $tier === SubscriptionTierEnum::FREE ? 'active' : 'pending_payment';

                // Create subscription
                $subscription = Subscription::create([
                    'employer_company_id' => $company->id,
                    'tier' => $tier->value,
                    'billing_cycle' => $billingCycle?->value,
                    'amount' => $amount,
                    'currency' => $packageConfig['pricing']['currency'] ?? 'GHS',
                    'status' => $status,
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'renews_at' => $renewsAt,
                    'auto_renew' => config('subscriptions.business_rules.auto_renewal_default', true),
                ]);

                Log::info('SubscriptionService: Subscription created successfully', [
                    'subscription_id' => $subscription->id,
                    'company_id' => $company->id,
                    'tier' => $tier->value,
                    'status' => $status,
                ]);

                // If FREE tier, activate immediately
                if ($tier === SubscriptionTierEnum::FREE) {
                    $this->sendSubscriptionConfirmation($subscription);
                }

                return $subscription;
            });
        } catch (\Exception $e) {
            Log::error('SubscriptionService: Failed to create subscription', [
                'company_id' => $company->id,
                'tier' => $tier->value,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to create subscription: '.$e->getMessage());
        }
    }

    /**
     * Process payment for a subscription.
     */
    public function processPayment(
        Subscription $subscription,
        PaymentMethodEnum $method,
        array $paymentData
    ): array {
        Log::info('SubscriptionService: Processing payment for subscription', [
            'subscription_id' => $subscription->id,
            'payment_method' => $method->value,
            'amount' => $subscription->amount,
        ]);

        try {
            // Generate payment reference
            $reference = 'SUB_'.strtoupper(uniqid());

            // Update subscription with payment method and reference
            $subscription->update([
                'payment_method' => $method->value,
                'payment_reference' => $reference,
                'payment_status' => 'pending',
            ]);

            // Get company admin email for payment
            $company = $subscription->employerCompany;
            $adminEmail = $company->primary_contact_email ?? $company->official_email;

            if (empty($adminEmail)) {
                throw new \Exception('Company contact email is required for payment processing');
            }

            // Prepare payment data for Paystack
            $paymentRequestData = [
                'email' => $adminEmail,
                'amount' => $subscription->amount,
                'reference' => $reference,
                'callback_url' => route('employer.subscription.payment.callback'),
                'metadata' => [
                    'subscription_id' => $subscription->id,
                    'company_id' => $company->id,
                    'tier' => $subscription->tier,
                    'billing_cycle' => $subscription->billing_cycle,
                ],
            ];

            // Initiate payment
            $paymentResponse = $this->paymentService->initiatePayment($paymentRequestData);

            Log::info('SubscriptionService: Payment initiated successfully', [
                'subscription_id' => $subscription->id,
                'reference' => $reference,
                'authorization_url' => $paymentResponse['authorization_url'] ?? null,
            ]);

            return $paymentResponse;
        } catch (\Exception $e) {
            Log::error('SubscriptionService: Payment processing failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $subscription->update([
                'payment_status' => 'failed',
            ]);

            throw new \Exception('Payment processing failed: '.$e->getMessage());
        }
    }

    /**
     * Activate a subscription after successful payment.
     */
    public function activateSubscription(Subscription $subscription): Subscription
    {
        Log::info('SubscriptionService: Activating subscription', [
            'subscription_id' => $subscription->id,
        ]);

        try {
            return DB::transaction(function () use ($subscription) {
                $subscription->update([
                    'status' => 'active',
                    'payment_status' => 'success',
                    'starts_at' => $subscription->starts_at ?? now(),
                ]);

                // Recalculate ends_at if needed
                if ($subscription->ends_at === null && $subscription->billing_cycle !== null) {
                    $billingCycle = BillingCycleEnum::from($subscription->billing_cycle);
                    $startsAt = $subscription->starts_at ?? now();

                    if ($billingCycle === BillingCycleEnum::MONTHLY) {
                        $subscription->update([
                            'ends_at' => $startsAt->copy()->addMonth(),
                            'renews_at' => $startsAt->copy()->addMonth(),
                        ]);
                    } else {
                        $subscription->update([
                            'ends_at' => $startsAt->copy()->addYear(),
                            'renews_at' => $startsAt->copy()->addYear(),
                        ]);
                    }
                }

                Log::info('SubscriptionService: Subscription activated successfully', [
                    'subscription_id' => $subscription->id,
                ]);

                $this->sendSubscriptionConfirmation($subscription);

                return $subscription->fresh();
            });
        } catch (\Exception $e) {
            Log::error('SubscriptionService: Failed to activate subscription', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to activate subscription: '.$e->getMessage());
        }
    }

    /**
     * Cancel a subscription.
     */
    public function cancelSubscription(Subscription $subscription, ?string $reason = null): Subscription
    {
        Log::info('SubscriptionService: Cancelling subscription', [
            'subscription_id' => $subscription->id,
            'reason' => $reason,
        ]);

        try {
            return DB::transaction(function () use ($subscription, $reason) {
                $subscription->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'cancellation_reason' => $reason,
                    'auto_renew' => false,
                ]);

                Log::info('SubscriptionService: Subscription cancelled successfully', [
                    'subscription_id' => $subscription->id,
                ]);

                return $subscription->fresh();
            });
        } catch (\Exception $e) {
            Log::error('SubscriptionService: Failed to cancel subscription', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to cancel subscription: '.$e->getMessage());
        }
    }

    /**
     * Upgrade a subscription to a higher tier.
     */
    public function upgradeSubscription(
        Subscription $current,
        SubscriptionTierEnum $newTier,
        BillingCycleEnum $billingCycle
    ): Subscription {
        Log::info('SubscriptionService: Upgrading subscription', [
            'subscription_id' => $current->id,
            'current_tier' => $current->tier,
            'new_tier' => $newTier->value,
            'billing_cycle' => $billingCycle->value,
        ]);

        if (! $current->canUpgrade()) {
            throw new \Exception('Subscription cannot be upgraded in its current state.');
        }

        try {
            return DB::transaction(function () use ($current, $newTier, $billingCycle) {
                $company = $current->employerCompany;

                // For paid tiers, check company approval and verification
                if ($newTier !== SubscriptionTierEnum::FREE) {
                    if ($company->status !== EmployerCompanyStatusEnum::APPROVED->value) {
                        Log::warning('SubscriptionService: Company not approved for paid subscription upgrade', [
                            'company_id' => $company->id,
                            'status' => $company->status,
                            'new_tier' => $newTier->value,
                        ]);
                        throw new \Exception('Your company must be approved before upgrading to paid plans.');
                    }

                    if ($company->verification_status !== EmployerCompanyVerificationStatusEnum::VERIFIED->value) {
                        Log::warning('SubscriptionService: Company not verified for paid subscription upgrade', [
                            'company_id' => $company->id,
                            'verification_status' => $company->verification_status,
                            'new_tier' => $newTier->value,
                        ]);
                        throw new \Exception('Your company must be verified before upgrading to paid plans. Please complete verification first.');
                    }
                }

                // Get new package configuration
                $packageConfig = config("subscriptions.packages.{$newTier->value}");

                if ($packageConfig === null) {
                    throw new \Exception("Invalid subscription tier: {$newTier->value}");
                }

                $pricing = $packageConfig['pricing'] ?? [];
                $newAmount = $pricing[$billingCycle->value] ?? 0;

                // Handle prorating if enabled
                $proratedAmount = $newAmount;
                if (config('subscriptions.business_rules.prorated_upgrades', true) && $current->ends_at !== null) {
                    $daysRemaining = $current->daysRemaining();
                    if ($daysRemaining !== null && $daysRemaining > 0) {
                        $totalDays = $billingCycle === BillingCycleEnum::MONTHLY ? 30 : 365;
                        $proratedAmount = ($newAmount / $totalDays) * $daysRemaining;
                    }
                }

                // Update current subscription
                $current->update([
                    'tier' => $newTier->value,
                    'billing_cycle' => $billingCycle->value,
                    'amount' => $proratedAmount,
                    'status' => 'pending_payment',
                ]);

                Log::info('SubscriptionService: Subscription upgraded', [
                    'subscription_id' => $current->id,
                    'new_tier' => $newTier->value,
                    'prorated_amount' => $proratedAmount,
                ]);

                return $current->fresh();
            });
        } catch (\Exception $e) {
            Log::error('SubscriptionService: Failed to upgrade subscription', [
                'subscription_id' => $current->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to upgrade subscription: '.$e->getMessage());
        }
    }

    /**
     * Downgrade a subscription to a lower tier.
     */
    public function downgradeSubscription(Subscription $current, SubscriptionTierEnum $newTier): Subscription
    {
        Log::info('SubscriptionService: Downgrading subscription', [
            'subscription_id' => $current->id,
            'current_tier' => $current->tier,
            'new_tier' => $newTier->value,
        ]);

        if (! $current->canDowngrade()) {
            throw new \Exception('Subscription cannot be downgraded in its current state.');
        }

        try {
            return DB::transaction(function () use ($current, $newTier) {
                // Validate posting limits before downgrade
                $downgradeRules = config('subscriptions.business_rules.downgrade_restrictions', []);
                $ruleKey = "{$current->tier}_to_{$newTier->value}";

                if (isset($downgradeRules[$ruleKey])) {
                    $maxPostings = $downgradeRules[$ruleKey]['max_active_postings'] ?? null;
                    // TODO: Check actual active postings count and validate
                    // For now, we'll allow the downgrade
                }

                // Get new package configuration
                $packageConfig = config("subscriptions.packages.{$newTier->value}");

                if ($packageConfig === null) {
                    throw new \Exception("Invalid subscription tier: {$newTier->value}");
                }

                // Update subscription (no prorating for downgrades per config)
                $current->update([
                    'tier' => $newTier->value,
                    'amount' => 0, // Downgrades take effect at end of current period
                    'auto_renew' => false, // Disable auto-renewal for downgrades
                ]);

                Log::info('SubscriptionService: Subscription downgraded', [
                    'subscription_id' => $current->id,
                    'new_tier' => $newTier->value,
                ]);

                return $current->fresh();
            });
        } catch (\Exception $e) {
            Log::error('SubscriptionService: Failed to downgrade subscription', [
                'subscription_id' => $current->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to downgrade subscription: '.$e->getMessage());
        }
    }

    /**
     * Send subscription confirmation email.
     */
    private function sendSubscriptionConfirmation(Subscription $subscription): void
    {
        try {
            $company = $subscription->employerCompany;
            $email = $company->primary_contact_email ?? $company->official_email;

            if (empty($email)) {
                Log::warning('SubscriptionService: Cannot send confirmation email, no email address', [
                    'subscription_id' => $subscription->id,
                    'company_id' => $company->id,
                ]);
                return;
            }

            $subject = 'Subscription Confirmation - '.config('app.name');
            $content = view('emails.subscription-confirmation', [
                'subscription' => $subscription,
                'company' => $company,
            ])->render();

            $this->notificationService->sendEmail($email, $subject, $content);

            Log::info('SubscriptionService: Confirmation email sent', [
                'subscription_id' => $subscription->id,
                'email' => $email,
            ]);
        } catch (\Exception $e) {
            Log::error('SubscriptionService: Failed to send confirmation email', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
            // Don't throw - email failure shouldn't break subscription creation
        }
    }
}

