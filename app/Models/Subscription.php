<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BillingCycleEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\SubscriptionTierEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Subscription extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\SubscriptionFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    use \OwenIt\Auditing\Auditable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'employer_company_id',
        'tier',
        'billing_cycle',
        'amount',
        'currency',
        'status',
        'starts_at',
        'ends_at',
        'renews_at',
        'auto_renew',
        'cancelled_at',
        'cancellation_reason',
        'payment_method',
        'payment_reference',
        'payment_status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'auto_renew' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'renews_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function employerCompany(): BelongsTo
    {
        return $this->belongsTo(EmployerCompany::class);
    }

    /**
     * Scope a query to only include active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            });
    }

    /**
     * Scope a query to only include paid subscriptions.
     */
    public function scopePaid($query)
    {
        return $query->whereIn('tier', [
            SubscriptionTierEnum::STARTER->value,
            SubscriptionTierEnum::PROFESSIONAL->value,
        ]);
    }

    /**
     * Scope a query to only include expired subscriptions.
     */
    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'expired')
                ->orWhere(function ($subQ) {
                    $subQ->where('status', 'active')
                        ->where('ends_at', '<=', now());
                });
        });
    }

    /**
     * Check if subscription is currently active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' &&
            ($this->ends_at === null || $this->ends_at->isFuture());
    }

    /**
     * Check if subscription is expired.
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' ||
            ($this->status === 'active' && $this->ends_at !== null && $this->ends_at->isPast());
    }

    /**
     * Get days remaining in subscription.
     */
    public function daysRemaining(): ?int
    {
        if ($this->ends_at === null) {
            return null; // Unlimited
        }

        if ($this->ends_at->isPast()) {
            return 0;
        }

        return (int) now()->diffInDays($this->ends_at, false);
    }

    /**
     * Check if subscription can be upgraded.
     */
    public function canUpgrade(): bool
    {
        if ($this->tier === SubscriptionTierEnum::PROFESSIONAL->value) {
            return false; // Already at highest tier
        }

        return $this->isActive();
    }

    /**
     * Check if subscription can be downgraded.
     */
    public function canDowngrade(): bool
    {
        if ($this->tier === SubscriptionTierEnum::FREE->value) {
            return false; // Already at lowest tier
        }

        return $this->isActive();
    }

    /**
     * Get the current tier enum.
     */
    public function getTierEnum(): SubscriptionTierEnum
    {
        return SubscriptionTierEnum::from($this->tier);
    }

    /**
     * Get the billing cycle enum if set.
     */
    public function getBillingCycleEnum(): ?BillingCycleEnum
    {
        return $this->billing_cycle ? BillingCycleEnum::from($this->billing_cycle) : null;
    }

    /**
     * Get the payment method enum if set.
     */
    public function getPaymentMethodEnum(): ?PaymentMethodEnum
    {
        return $this->payment_method ? PaymentMethodEnum::from($this->payment_method) : null;
    }
}
