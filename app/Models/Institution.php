<?php

namespace App\Models;

use App\Enums\InstitutionPartnershipTierEnum;
use App\Enums\InstitutionTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Institution extends Model implements Auditable
{
    use HasUuids, SoftDeletes;
    use \OwenIt\Auditing\Auditable; // for auditing

    protected $fillable = [
        'name',
        'type',
        'location',
        'email',
        'student_email_domain',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'website',
        'logo',
        'is_active',
        'is_partner',
        'partnership_tier',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_partner' => 'boolean',
            'type' => InstitutionTypeEnum::class,
            'partnership_tier' => InstitutionPartnershipTierEnum::class,
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function universityAdmins(): HasMany
    {
        return $this->hasMany(UniversityAdmin::class);
    }

    /**
     * Get the education records for this institution.
     */
    public function education(): HasMany
    {
        return $this->hasMany(TalentEducation::class);
    }
}
