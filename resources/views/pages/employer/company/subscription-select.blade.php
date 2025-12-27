@extends('layouts.dashboard.main')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/subscription-select.css') }}">
<div class="dashboard-container subscription-select-page">
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <h2 class="dashboard-card-title">Select Subscription Plan</h2>
                <p class="dashboard-card-subtitle">
                    Choose the subscription tier that best fits your hiring needs
                </p>
            </div>
        </div>

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @php
        $contactEmail = $company->primary_contact_email ?? $company->official_email;
        $hasContactEmail = !empty($contactEmail);
        $isApproved = $company->status === \App\Enums\EmployerCompanyStatusEnum::APPROVED->value;
        $isVerified = $company->verification_status === \App\Enums\EmployerCompanyVerificationStatusEnum::VERIFIED->value;
        @endphp

        @if(!$isApproved)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Approval required:</strong> Your company profile must be approved by an admin before you can select a subscription plan.
            <a href="{{ route('employer.company.show') }}" class="alert-link">Back to company profile</a>.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(!$hasContactEmail)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Action Required:</strong> To select a paid subscription plan, please add a contact email to your
            company profile.
            You can add either a <strong>Primary Contact Email</strong> or <strong>Official Email</strong> in the
            <a href="{{ route('employer.company.show') }}#primary-contact" class="alert-link">Primary Contact
                Information</a>
            or <a href="{{ route('employer.company.show') }}#contact-location" class="alert-link">Contact & Location
                Information</a> section.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if($isApproved && !$isVerified)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>Verification required for paid plans:</strong> To select Starter or Professional, your business must be verified (COM-04).
            Upload Ghana Card + Business Registration under <a href="{{ route('employer.company.show') }}#registration" class="alert-link">Registration & Verification</a>.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <form action="{{ route('employer.subscription.store') }}" method="POST" id="subscription-form">
            @csrf

            <!-- Billing Cycle Toggle (only show for paid tiers) -->
            <div class="mb-4 text-center">
                <div class="btn-group" role="group" id="billing-cycle-toggle">
                    <input type="radio" class="btn-check" name="billing_cycle" id="billing_monthly" value="monthly"
                        checked>
                    <label class="btn btn-outline-primary" for="billing_monthly">Monthly</label>

                    <input type="radio" class="btn-check" name="billing_cycle" id="billing_annual" value="annual">
                    <label class="btn btn-outline-primary" for="billing_annual">Annual <span
                            class="badge bg-success ms-1">Save 17%</span></label>
                </div>
            </div>

            <!-- Subscription Tiers -->
            <div class="row g-4 mb-4">
                @foreach(['free', 'starter', 'professional'] as $tierKey)
                @php
                $package = $packages[$tierKey] ?? null;
                if (!$package) continue;
                @endphp
                <div class="col-md-4">
                    <div class="card h-100 subscription-tier-card" data-tier="{{ $tierKey }}">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $package['display_name'] }}</h5>
                            <p class="text-muted small">{{ $package['description'] }}</p>

                            <div class="pricing mb-3">
                                <div class="monthly-price">
                                    <span class="h3">GHS {{ number_format($package['pricing']['monthly'], 2) }}</span>
                                    <span class="text-muted">/month</span>
                                </div>
                                <div class="annual-price">
                                    <span class="h3">GHS {{ number_format($package['pricing']['annual'], 2) }}</span>
                                    <span class="text-muted">/year</span>
                                    <div class="small text-success">Save {{
                                        $package['pricing']['annual_discount_percentage'] ?? 0 }}%</div>
                                </div>
                            </div>

                            <ul class="list-unstyled flex-grow-1 mb-3">
                                <li class="mb-2">
                                    <strong>Active Postings:</strong>
                                    {{ $package['limits']['active_postings'] === null ? 'Unlimited' :
                                    $package['limits']['active_postings'] }}
                                </li>
                                @if($package['features']['custom_logo'] ?? false)
                                <li class="mb-2">✓ Custom Logo</li>
                                @endif
                                @if($package['features']['company_photos'] ?? false)
                                <li class="mb-2">✓ Company Photos ({{ $package['limits']['company_photos'] === null ?
                                    'Unlimited' : $package['limits']['company_photos'] }})</li>
                                @endif
                                @if($package['features']['employee_testimonials'] ?? false)
                                <li class="mb-2">✓ Employee Testimonials</li>
                                @endif
                                @if($package['features']['advanced_analytics'] ?? false)
                                <li class="mb-2">✓ Advanced Analytics</li>
                                @endif
                                @if($package['features']['ats_integration'] ?? false)
                                <li class="mb-2">✓ ATS Integration</li>
                                @endif
                            </ul>

                            <button type="button" class="btn btn-primary w-100 select-tier-btn"
                                data-tier="{{ $tierKey }}"
                                @if(!$isApproved) disabled title="Company approval required"
                                @elseif($tierKey !== 'free' && !$hasContactEmail) disabled title="Please add a contact email to your company profile first"
                                @elseif($tierKey !== 'free' && !$isVerified) disabled title="Business verification required for paid plans"
                                @endif>
                                @if($currentSubscription && $currentSubscription->tier === $tierKey &&
                                $currentSubscription->isActive())
                                Current Plan
                                @elseif(!$isApproved)
                                Approval Required
                                @elseif($tierKey !== 'free' && !$hasContactEmail)
                                Add Email Required
                                @elseif($tierKey !== 'free' && !$isVerified)
                                Verification Required
                                @else
                                Select Plan
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Hidden input for selected tier -->
            <input type="hidden" name="tier" id="selected_tier" value="">

            <div class="d-flex justify-content-between">
                <a href="{{ route('employer.company.show') }}" class="btn btn-secondary">Back to Profile</a>
                <button type="submit" class="btn btn-primary" id="submit-btn" disabled>Continue</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('subscription-form');
    const billingToggle = document.getElementById('billing-cycle-toggle');
    const monthlyPrices = document.querySelectorAll('.monthly-price');
    const annualPrices = document.querySelectorAll('.annual-price');
    const tierCards = document.querySelectorAll('.subscription-tier-card');
    const selectButtons = document.querySelectorAll('.select-tier-btn');
    const selectedTierInput = document.getElementById('selected_tier');
    const submitBtn = document.getElementById('submit-btn');
    const monthlyRadio = document.getElementById('billing_monthly');
    const annualRadio = document.getElementById('billing_annual');

    // Handle tier selection
    selectButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Prevent selection if button is disabled
            if (this.disabled) {
                return;
            }

            const tier = this.dataset.tier;

            // Remove active class from all cards
            tierCards.forEach(card => card.classList.remove('border-primary', 'border-2'));

            // Add active class to selected card
            const selectedCard = document.querySelector(`[data-tier="${tier}"]`);
            selectedCard.classList.add('border-primary', 'border-2');

            // Set hidden input
            selectedTierInput.value = tier;

            // Show/hide billing cycle toggle
            if (tier === 'free') {
                billingToggle.style.display = 'none';
            } else {
                billingToggle.style.display = 'block';
            }

            // Enable submit button
            submitBtn.disabled = false;
        });
    });

    // Handle billing cycle change
    [monthlyRadio, annualRadio].forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'monthly') {
                monthlyPrices.forEach(el => el.style.display = 'block');
                annualPrices.forEach(el => el.style.display = 'none');
            } else {
                monthlyPrices.forEach(el => el.style.display = 'none');
                annualPrices.forEach(el => el.style.display = 'block');
            }
        });
    });
});
</script>
@endsection