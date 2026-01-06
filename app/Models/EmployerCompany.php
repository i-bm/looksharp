<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EmployerCompanyStatusEnum;
use App\Enums\EmployerCompanyVerificationStatusEnum;
use App\Enums\SubscriptionTierEnum;
use App\Services\SubscriptionGateService;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class EmployerCompany extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\EmployerCompanyFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    use \OwenIt\Auditing\Auditable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'created_by_user_id',
        'legal_name',
        'trading_name',
        'industry',
        'company_size',
        'website',
        'linkedin_url',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'youtube_url',
        'country',
        'city',
        'state_or_region',
        'address',
        'official_email',
        'phone_number',
        'registration_number',
        'ghana_card_document_url',
        'business_registration_document_url',
        'verification_status',
        'verified_at',
        'verified_by_user_id',
        'primary_contact_name',
        'primary_contact_title',
        'primary_contact_email',
        'primary_contact_phone',
        'owner_name',
        'owner_ghana_card_number',
        'owner_title',
        'logo_url',
        'company_description',
        'year_established',
        'video_url',
        'status',
        'wizard_complete',
        'profile_completeness_score',
        'submitted_at',
        'reviewed_by_user_id',
        'review_notes',
        'approved_at',
        'rejected_at',
        'suspended_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'wizard_complete' => 'boolean',
            'year_established' => 'integer',
            'is_featured' => 'boolean',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'suspended_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'employer_company_members', 'employer_company_id', 'user_id')
            ->withPivot(['role'])
            ->withTimestamps();
    }

    public function photos(): HasMany
    {
        return $this->hasMany(EmployerCompanyPhoto::class)->orderBy('display_order');
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(EmployerCompanyTestimonial::class)->orderBy('display_order');
    }

    public function featuredTestimonials(): HasMany
    {
        return $this->hasMany(EmployerCompanyTestimonial::class)
            ->where('is_featured', true)
            ->orderBy('display_order');
    }

    public function isEditableByEmployer(): bool
    {
        return in_array($this->status, [
            EmployerCompanyStatusEnum::DRAFT->value,
            EmployerCompanyStatusEnum::NEEDS_CHANGES->value,
        ], true);
    }

    public function isApproved(): bool
    {
        return $this->status === EmployerCompanyStatusEnum::APPROVED->value;
    }

    public function isVerified(): bool
    {
        return $this->verification_status === EmployerCompanyVerificationStatusEnum::VERIFIED->value;
    }

    public function canAccessPaidFeatures(): bool
    {
        $gateService = app(SubscriptionGateService::class);

        return $gateService->canAccessPaidFeatures($this);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the active subscription for this company.
     * This method queries for active subscriptions directly instead of relying on latestOfMany(),
     * to avoid issues where a pending_payment subscription becomes the latest but an older
     * active subscription still exists.
     */
    public function getActiveSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->active()
            ->latest()
            ->first();
    }

    public function hasActiveSubscription(): bool
    {
        $subscription = $this->getActiveSubscription();

        return $subscription !== null;
    }

    public function currentSubscriptionTier(): ?SubscriptionTierEnum
    {
        $subscription = $this->getActiveSubscription();

        if ($subscription === null) {
            return null;
        }

        return $subscription->getTierEnum();
    }

    public function canPostOpportunity(): bool
    {
        $gateService = app(SubscriptionGateService::class);

        return $gateService->canPostOpportunity($this);
    }
}
