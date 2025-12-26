---
name: Subscription Onboarding Integration
overview: Integrate subscription selection as a required step in the employer onboarding wizard, with Paystack payment integration for paid tiers. Subscription will only be created when explicitly selected during onboarding (no auto-creation of FREE tier).
todos:
  - id: "1"
    content: Create Subscription model and migration with UUID primary key, relationships, and all required fields (tier, billing_cycle, status, payment tracking, etc.)
    status: pending
  - id: "2"
    content: Create SubscriptionService with methods for creating subscriptions, processing payments, activating subscriptions, and handling upgrades/downgrades
    status: pending
    dependencies:
      - "1"
  - id: "3"
    content: Create PaymentService to abstract Paystack integration with methods for initiating payments, verifying payments, and handling webhooks
    status: pending
  - id: "4"
    content: Update EmployerCompany model to add subscription relationships (hasOne, hasMany) and helper methods (hasActiveSubscription, currentSubscriptionTier, canPostOpportunity)
    status: pending
    dependencies:
      - "1"
  - id: "5"
    content: Update EmployerCompanyService to add subscription step to wizard progress and update wizard completion logic to require all 5 steps
    status: pending
    dependencies:
      - "1"
      - "4"
  - id: "6"
    content: Create StoreSubscriptionRequest and ProcessPaymentRequest form request classes with validation rules
    status: pending
  - id: "7"
    content: Add subscription routes to web.php (select, store, payment, callback, webhook)
    status: pending
  - id: "8"
    content: Add subscription selection methods to EmployerProfileController (selectSubscription, storeSubscription, processSubscriptionPayment, paymentCallback, paymentWebhook)
    status: pending
    dependencies:
      - "2"
      - "3"
      - "6"
  - id: "9"
    content: Create subscription-edit.blade.php partial view with tier comparison, pricing display, billing cycle toggle, and payment method selection
    status: pending
  - id: "10"
    content: Create payment.blade.php view for payment processing status and Paystack redirect
    status: pending
  - id: "11"
    content: Update company show view to display subscription step in wizard progress and show current subscription status
    status: pending
    dependencies:
      - "9"
  - id: "12"
    content: Add Paystack configuration to config/services.php and update .env with Paystack credentials
    status: pending
  - id: "13"
    content: Implement subscription limits enforcement (check active_postings limit before allowing new postings) and add upgrade prompts
    status: pending
    dependencies:
      - "2"
      - "4"
  - id: "14"
    content: Add comprehensive logging throughout SubscriptionService and PaymentService for all subscription and payment operations
    status: pending
    dependencies:
      - "2"
      - "3"
---

# Subscription Integration into Employer Onboarding

## Overview

Add subscription selection as a required 5th step in the employer company onboarding wizard. Employers must select a subscription tier (FREE, STARTER, or PROFESSIONAL) before completing onboarding. Integrate Paystack payment gateway for processing paid subscription payments.

## Architecture

### Data Flow

```javascript
Company Creation → Wizard Steps 1-4 → Subscription Selection (Step 5) → Payment Processing (if paid) → Subscription Activation → Wizard Complete
```



### Key Components

1. **Subscription Model** - Tracks subscription details, billing cycles, and payment status
2. **SubscriptionService** - Handles subscription creation, upgrades, and payment processing
3. **Payment Integration** - Paystack integration for processing subscription payments
4. **Wizard Updates** - Add subscription step to existing wizard flow
5. **UI Components** - Subscription selection interface with tier comparison

## Implementation Tasks

### 1. Database Schema

#### Create Subscription Model and Migration

- **File**: `database/migrations/YYYY_MM_DD_HHMMSS_create_subscriptions_table.php`
- **Fields**:
- `id` (UUID primary key)
- `employer_company_id` (foreignUuid to employer_companies)
- `tier` (enum: free, starter, professional)
- `billing_cycle` (nullable enum: monthly, annual)
- `amount` (decimal, default 0)
- `currency` (string, default 'GHS')
- `status` (enum: active, cancelled, expired, pending_payment)
- `starts_at`, `ends_at`, `renews_at` (timestamps)
- `auto_renew` (boolean, default true)
- `cancelled_at`, `cancellation_reason` (nullable)
- Payment tracking: `payment_method`, `payment_reference`, `payment_status`
- Soft deletes and timestamps
- **Indexes**: employer_company_id, status, tier

#### Create Subscription Model

- **File**: `app/Models/Subscription.php`
- **Features**:
- Use `HasUuids` trait
- Implement `Auditable` contract
- Relationships: `belongsTo(EmployerCompany::class)`
- Scopes: `active()`, `paid()`, `expired()`
- Methods: `isActive()`, `isExpired()`, `daysRemaining()`, `canUpgrade()`, `canDowngrade()`

### 2. Service Layer

#### Create SubscriptionService

- **File**: `app/Services/SubscriptionService.php`
- **Dependencies**: `NotificationService`, `PaymentService` (to be created)
- **Key Methods**:
- `createSubscription(EmployerCompany $company, SubscriptionTierEnum $tier, ?BillingCycleEnum $billingCycle = null): Subscription`
    - Creates subscription record
    - Sets start/end dates based on billing cycle
    - Logs subscription creation
    - Wraps in transaction
- `processPayment(Subscription $subscription, PaymentMethodEnum $method, array $paymentData): bool`
    - Initiates Paystack payment
    - Updates subscription status
    - Handles payment callbacks
- `activateSubscription(Subscription $subscription): Subscription`
    - Sets status to 'active'
    - Sends confirmation email
- `cancelSubscription(Subscription $subscription, ?string $reason = null): Subscription`
- `upgradeSubscription(Subscription $current, SubscriptionTierEnum $newTier, BillingCycleEnum $billingCycle): Subscription`
    - Handles prorating (per config)
- `downgradeSubscription(Subscription $current, SubscriptionTierEnum $newTier): Subscription`
    - Validates posting limits before downgrade

#### Create PaymentService

- **File**: `app/Services/PaymentService.php`
- **Purpose**: Abstract payment gateway integration
- **Methods**:
- `initiatePayment(array $data): array` - Returns Paystack payment link
- `verifyPayment(string $reference): array` - Verifies Paystack payment
- `handleWebhook(array $payload): void` - Processes Paystack webhooks

#### Update EmployerCompanyService

- **File**: `app/Services/EmployerCompanyService.php`
- **Changes**:
- Update `getWizardProgress()` to include subscription step
- Add `hasActiveSubscription()` helper method
- Update wizard completion logic to require subscription step
- Add subscription check in `createCompanyForEmployer()` if needed

### 3. Model Relationships

#### Update EmployerCompany Model

- **File**: `app/Models/EmployerCompany.php`
- **Add**:
- `subscription(): HasOne` relationship
- `subscriptions(): HasMany` relationship
- `hasActiveSubscription(): bool` method
- `currentSubscriptionTier(): ?SubscriptionTierEnum` method
- `canPostOpportunity(): bool` method (checks subscription limits)

### 4. Controller Updates

#### Update EmployerProfileController

- **File**: `app/Http/Controllers/EmployerProfileController.php`
- **Add Methods**:
- `selectSubscription(Request $request): View` - Show subscription selection page
- `storeSubscription(StoreSubscriptionRequest $request): RedirectResponse` - Process subscription selection
- `processSubscriptionPayment(ProcessPaymentRequest $request): RedirectResponse` - Handle payment initiation
- `paymentCallback(Request $request): RedirectResponse` - Handle Paystack callback
- `paymentWebhook(Request $request): JsonResponse` - Handle Paystack webhook

#### Create Form Requests

- **File**: `app/Http/Requests/Subscription/StoreSubscriptionRequest.php`
- Validation: tier (required, enum), billing_cycle (required_if:tier,starter|professional, enum)
- **File**: `app/Http/Requests/Subscription/ProcessPaymentRequest.php`
- Validation: payment_method (required, enum), phone_number (required_if:payment_method,mobile_money)

### 5. Routes

#### Update routes/web.php

- **Add Routes**:
  ```php
        Route::middleware(['auth', 'role:employer'])->group(function () {
            Route::get('/company/subscription/select', [EmployerProfileController::class, 'selectSubscription'])
                ->name('employer.subscription.select');
            Route::post('/company/subscription', [EmployerProfileController::class, 'storeSubscription'])
                ->name('employer.subscription.store');
            Route::post('/company/subscription/payment', [EmployerProfileController::class, 'processSubscriptionPayment'])
                ->name('employer.subscription.payment');
            Route::get('/company/subscription/payment/callback', [EmployerProfileController::class, 'paymentCallback'])
                ->name('employer.subscription.payment.callback');
        });
        
        // Webhook route (no auth required)
        Route::post('/webhooks/paystack/subscription', [EmployerProfileController::class, 'paymentWebhook'])
            ->name('webhooks.paystack.subscription');
  ```




### 6. Views

#### Create Subscription Selection View

- **File**: `resources/views/pages/employer/company/partials/subscription-edit.blade.php`
- **Features**:
- Display all 3 tiers (FREE, STARTER, PROFESSIONAL)
- Feature comparison table
- Monthly/Annual billing toggle
- Pricing display from `config/subscriptions.php`
- "Select" buttons for each tier
- Payment method selection (if paid tier selected)
- Mobile money phone number input (if mobile money selected)

#### Update Company Show View

- **File**: `resources/views/pages/employer/company/show.blade.php`
- **Changes**:
- Add subscription step to wizard progress display
- Show current subscription tier and status
- Display subscription management section

#### Create Payment Processing View

- **File**: `resources/views/pages/employer/subscription/payment.blade.php`
- **Purpose**: Show payment processing status and redirect to Paystack

### 7. Wizard Integration

#### Update Wizard Progress Logic

- **File**: `app/Services/EmployerCompanyService.php`
- **In `getWizardProgress()` method**:
- Add 5th step: `'subscription' => ['completed' => $company->hasActiveSubscription(), 'step' => 5]`
- Update `$allStepsComplete` to require all 5 steps (including subscription)
- Update wizard completion message/logic

#### Add Subscription Step Navigation

- Update wizard navigation to include subscription step
- Add redirect logic: if wizard incomplete and subscription not selected, redirect to subscription selection

### 8. Payment Integration

#### Paystack Configuration

- **File**: `config/services.php`
- **Add**:
  ```php
        'paystack' => [
            'public_key' => env('PAYSTACK_PUBLIC_KEY'),
            'secret_key' => env('PAYSTACK_SECRET_KEY'),
            'merchant_email' => env('PAYSTACK_MERCHANT_EMAIL'),
            'webhook_secret' => env('PAYSTACK_WEBHOOK_SECRET'),
        ],
  ```




#### Payment Processing Flow

1. User selects paid tier → Create subscription with `pending_payment` status
2. Initiate Paystack payment → Get payment link
3. Redirect user to Paystack payment page
4. Handle callback → Verify payment → Activate subscription
5. Handle webhook → Update subscription status (for async updates)

### 9. Business Logic

#### Subscription Limits Enforcement

- Create middleware or helper to check subscription limits before posting opportunities
- Check `active_postings` limit from `config/subscriptions.php`
- Show upgrade prompts when limits reached

#### Subscription Status Management

- Handle subscription expiration
- Auto-renewal logic (if enabled)
- Grace period handling (7 days per config)
- Downgrade restrictions (validate posting limits)

### 10. Logging and Auditing

#### Add Comprehensive Logging

- Log subscription creation, activation, cancellation
- Log payment initiation, success, failure
- Log upgrade/downgrade attempts
- Include user_id, company_id, subscription_id in all logs

### 11. Testing Considerations

#### Test Scenarios

- FREE tier selection (no payment)
- STARTER tier with monthly/annual billing
- PROFESSIONAL tier with payment
- Payment failure handling
- Webhook processing
- Subscription upgrade/downgrade
- Wizard completion with subscription

## Files to Create

1. `database/migrations/YYYY_MM_DD_HHMMSS_create_subscriptions_table.php`
2. `app/Models/Subscription.php`
3. `app/Services/SubscriptionService.php`
4. `app/Services/PaymentService.php`
5. `app/Http/Requests/Subscription/StoreSubscriptionRequest.php`
6. `app/Http/Requests/Subscription/ProcessPaymentRequest.php`
7. `resources/views/pages/employer/company/partials/subscription-edit.blade.php`
8. `resources/views/pages/employer/subscription/payment.blade.php`

## Files to Modify

1. `app/Models/EmployerCompany.php` - Add subscription relationships
2. `app/Services/EmployerCompanyService.php` - Update wizard progress
3. `app/Http/Controllers/EmployerProfileController.php` - Add subscription methods
4. `routes/web.php` - Add subscription routes
5. `config/services.php` - Add Paystack config
6. `resources/views/pages/employer/company/show.blade.php` - Add subscription step

## Environment Variables

Add to `.env`:

```javascript
PAYSTACK_PUBLIC_KEY=
PAYSTACK_SECRET_KEY=
PAYSTACK_MERCHANT_EMAIL=
PAYSTACK_WEBHOOK_SECRET=
```



## Dependencies

- Paystack PHP SDK (install via Composer if needed)
- Ensure UUID support for subscriptions table
- Laravel Auditing for subscription changes

## Notes

- Subscription is REQUIRED during onboarding (but FREE tier is always available)
- No auto-creation of FREE subscription - only created when selected