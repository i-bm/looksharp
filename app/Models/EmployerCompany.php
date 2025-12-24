<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EmployerCompanyStatusEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'country',
        'city',
        'address',
        'official_email',
        'phone_number',
        'registration_number',
        'primary_contact_name',
        'primary_contact_title',
        'primary_contact_email',
        'primary_contact_phone',
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
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'suspended_at' => 'datetime',
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

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'employer_company_members', 'employer_company_id', 'user_id')
            ->withPivot(['role'])
            ->withTimestamps();
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
}
