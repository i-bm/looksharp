<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Subscription Packages Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for employer subscription packages,
    | including pricing, features, limits, and business rules.
    |
    */

    'packages' => [
        'free' => [
            'name' => 'Free',
            'display_name' => 'Free Tier',
            'description' => 'Perfect for small businesses and startups getting started',
            'tier' => \App\Enums\SubscriptionTierEnum::FREE->value,
            'pricing' => [
                'monthly' => 0,
                'annual' => 0,
                'currency' => 'GHS',
            ],
            'limits' => [
                'active_postings' => 3,
                'total_postings' => null, // Unlimited (archived don't count)
                'company_photos' => 0,
                'testimonials' => 0,
                'video_duration_seconds' => 0,
            ],
            'features' => [
                'basic_company_profile' => true,
                'custom_logo' => false,
                'company_photos' => false,
                'employee_testimonials' => false,
                'company_video' => false,
                'featured_posting_option' => false,
                'advanced_filtering' => false,
                'bulk_actions' => false,
                'custom_tags_notes' => false,
                'ai_candidate_matching' => false,
                'automated_workflows' => false,
                'basic_analytics' => true,
                'advanced_analytics' => false,
                'custom_reports' => false,
                'export_csv_excel' => false,
                'roi_tracking' => false,
                'calendar_sync' => false,
                'ats_integration' => false,
                'api_access' => false,
                'webhooks' => false,
                'priority_email_support' => false,
                'phone_support' => false,
                'dedicated_account_manager' => false,
                'premium_career_fair_booth' => false,
                'diversity_inclusion_reporting' => false,
            ],
            'support' => [
                'level' => 'standard',
                'response_time_hours' => null, // Standard support, no SLA
                'phone_support' => false,
            ],
        ],

        'starter' => [
            'name' => 'Starter',
            'display_name' => 'Starter Tier',
            'description' => 'Ideal for growing companies with regular hiring needs',
            'tier' => \App\Enums\SubscriptionTierEnum::STARTER->value,
            'pricing' => [
                'monthly' => 200,
                'annual' => 2000, // 17% discount (2 months free)
                'currency' => 'GHS',
                'annual_discount_percentage' => 17,
            ],
            'limits' => [
                'active_postings' => 20,
                'total_postings' => null, // Unlimited (archived don't count)
                'company_photos' => 10,
                'testimonials' => 5,
                'video_duration_seconds' => 90,
            ],
            'features' => [
                'basic_company_profile' => true,
                'custom_logo' => true,
                'company_photos' => true,
                'employee_testimonials' => true,
                'company_video' => true,
                'featured_posting_option' => true, // Available for additional fee
                'advanced_filtering' => true,
                'bulk_actions' => true,
                'custom_tags_notes' => true,
                'ai_candidate_matching' => false,
                'automated_workflows' => false,
                'basic_analytics' => true,
                'advanced_analytics' => true,
                'custom_reports' => false,
                'export_csv_excel' => true,
                'roi_tracking' => false,
                'calendar_sync' => true,
                'ats_integration' => false,
                'api_access' => false,
                'webhooks' => false,
                'priority_email_support' => true,
                'phone_support' => false,
                'dedicated_account_manager' => false,
                'premium_career_fair_booth' => false,
                'diversity_inclusion_reporting' => true,
            ],
            'support' => [
                'level' => 'priority_email',
                'response_time_hours' => 48,
                'phone_support' => false,
            ],
        ],

        'professional' => [
            'name' => 'Professional',
            'display_name' => 'Professional Tier',
            'description' => 'For large enterprises and high-volume recruiters',
            'tier' => \App\Enums\SubscriptionTierEnum::PROFESSIONAL->value,
            'pricing' => [
                'monthly' => 500,
                'annual' => 5000, // 17% discount (2 months free)
                'currency' => 'GHS',
                'annual_discount_percentage' => 17,
            ],
            'limits' => [
                'active_postings' => null, // Unlimited
                'total_postings' => null, // Unlimited
                'company_photos' => null, // Unlimited
                'testimonials' => null, // Unlimited
                'video_duration_seconds' => 90,
            ],
            'features' => [
                'basic_company_profile' => true,
                'custom_logo' => true,
                'company_photos' => true,
                'employee_testimonials' => true,
                'company_video' => true,
                'featured_posting_option' => true, // Available for additional fee
                'advanced_filtering' => true,
                'bulk_actions' => true,
                'custom_tags_notes' => true,
                'ai_candidate_matching' => true,
                'automated_workflows' => true,
                'basic_analytics' => true,
                'advanced_analytics' => true,
                'custom_reports' => true,
                'export_csv_excel' => true,
                'roi_tracking' => true,
                'calendar_sync' => true,
                'ats_integration' => true,
                'api_access' => true,
                'webhooks' => true,
                'priority_email_support' => true,
                'phone_support' => true,
                'dedicated_account_manager' => true,
                'premium_career_fair_booth' => true,
                'diversity_inclusion_reporting' => true,
            ],
            'support' => [
                'level' => 'priority_phone',
                'response_time_hours' => 24,
                'phone_support' => true,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Additional Services Pricing
    |--------------------------------------------------------------------------
    |
    | Pricing for services that are separate from subscription packages.
    |
    */

    'additional_services' => [
        'featured_posting' => [
            '7_day_boost' => [
                'name' => '7-Day Featured Posting',
                'price' => 50,
                'currency' => 'GHS',
                'duration_days' => 7,
                'description' => 'Boost your posting to the top of search results for 7 days',
            ],
            '30_day_boost' => [
                'name' => '30-Day Featured Posting',
                'price' => 150,
                'currency' => 'GHS',
                'duration_days' => 30,
                'description' => 'Boost your posting to the top of search results for 30 days',
            ],
        ],
        'career_fair_booth' => [
            'standard' => [
                'name' => 'Standard Career Fair Booth',
                'price' => 500,
                'currency' => 'GHS',
                'description' => 'Standard booth placement at virtual career fair',
            ],
            'premium' => [
                'name' => 'Premium Career Fair Booth',
                'price' => 1000,
                'currency' => 'GHS',
                'description' => 'Premium booth placement with enhanced branding',
            ],
            'large_event' => [
                'name' => 'Large Event Career Fair Booth',
                'price' => 2000,
                'currency' => 'GHS',
                'description' => 'Premium booth at large-scale career fair events',
            ],
            'university_specific' => [
                'name' => 'University-Specific Career Fair Booth',
                'price' => 300,
                'currency' => 'GHS',
                'description' => 'Booth at university-specific career fair',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Business Rules
    |--------------------------------------------------------------------------
    |
    | Configuration for subscription business rules and policies.
    |
    */

    'business_rules' => [
        'default_tier' => 'free',
        'auto_renewal_default' => true,
        'grace_period_days' => 7, // Days before downgrade after payment failure
        'cancellation_notice_days' => 0, // Immediate cancellation allowed
        'reactivation_window_days' => 30, // Days to reactivate without data loss
        'prorated_upgrades' => true, // Prorate billing when upgrading mid-cycle
        'prorated_downgrades' => false, // No refunds, access until period ends
        'downgrade_restrictions' => [
            'professional_to_starter' => [
                'max_active_postings' => 20,
                'message' => 'You must archive postings to reduce active count to 20 or less before downgrading.',
            ],
            'professional_to_free' => [
                'max_active_postings' => 3,
                'message' => 'You must archive postings to reduce active count to 3 or less before downgrading.',
            ],
            'starter_to_free' => [
                'max_active_postings' => 3,
                'message' => 'You must archive postings to reduce active count to 3 or less before downgrading.',
            ],
        ],
        'upgrade_prompts' => [
            'free' => [
                'trigger_at_postings' => 2, // Show upgrade prompt at 2/3 postings
                'message' => 'You\'re using 2 of 3 free postings. Upgrade to Starter for 20 active postings.',
            ],
            'starter' => [
                'trigger_at_postings' => 18, // Show upgrade prompt at 18/20 postings
                'message' => 'You\'re using 18 of 20 Starter postings. Upgrade to Professional for unlimited postings.',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Configuration
    |--------------------------------------------------------------------------
    |
    | Payment method settings and processing configuration.
    |
    */

    'payment' => [
        'methods' => [
            \App\Enums\PaymentMethodEnum::MTN_MOMO->value => [
                'enabled' => true,
                'name' => 'MTN Mobile Money',
                'fee_percentage' => 1.5, // Estimated fee percentage
                'fee_fixed' => 0,
            ],
            \App\Enums\PaymentMethodEnum::VODAFONE_CASH->value => [
                'enabled' => true,
                'name' => 'Vodafone Cash',
                'fee_percentage' => 1.5,
                'fee_fixed' => 0,
            ],
            \App\Enums\PaymentMethodEnum::TELECEL_CASH->value => [
                'enabled' => true,
                'name' => 'Telecel Cash',
                'fee_percentage' => 1.5,
                'fee_fixed' => 0,
            ],
            \App\Enums\PaymentMethodEnum::AIRTELTIGO_MONEY->value => [
                'enabled' => true,
                'name' => 'AirtelTigo Money',
                'fee_percentage' => 1.5,
                'fee_fixed' => 0,
            ],
            \App\Enums\PaymentMethodEnum::CARD->value => [
                'enabled' => true,
                'name' => 'Card Payment',
                'fee_percentage' => 2.5, // Estimated fee percentage
                'fee_fixed' => 2, // Estimated fixed fee in GHS
                'provider' => 'flutterwave', // or 'paystack'
            ],
            \App\Enums\PaymentMethodEnum::USSD->value => [
                'enabled' => false, // Future implementation
                'name' => 'USSD',
                'fee_percentage' => 1.5,
                'fee_fixed' => 0,
            ],
        ],
        'absorb_fees' => true, // Whether to absorb payment processing fees
        'currency' => 'GHS',
    ],

    /*
    |--------------------------------------------------------------------------
    | Launch Pricing (Early Adopter Incentives)
    |--------------------------------------------------------------------------
    |
    | Special pricing for early adopters during launch period.
    | Set to null to disable launch pricing.
    |
    */

    'launch_pricing' => [
        'enabled' => false, // Set to true during launch period
        'discount_percentage' => 20,
        'valid_until' => null, // e.g., '2026-06-30'
        'lock_in_period_months' => 12, // Lock in pricing for first year
        'tiers' => [
            'starter' => [
                'monthly' => 160, // 20% off GHS 200
                'annual' => 1600, // 20% off GHS 2000
            ],
            'professional' => [
                'monthly' => 400, // 20% off GHS 500
                'annual' => 4000, // 20% off GHS 5000
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Discounts
    |--------------------------------------------------------------------------
    |
    | Special discount configurations for specific groups.
    |
    */

    'discounts' => [
        'university_partners' => [
            'enabled' => true,
            'discount_percentage' => 10,
            'description' => 'Discount for verified university partners',
        ],
        'non_profits' => [
            'enabled' => true,
            'discount_percentage' => 25,
            'description' => 'Discount for verified non-profit organizations',
        ],
    ],

];

