<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class TalentCertification extends Model implements Auditable
{
    use HasFactory, HasUuids, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'talent_id',
        'name',
        'issuer',
        'date_obtained',
        'expiration_date',
        'credential_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_obtained' => 'date',
            'expiration_date' => 'date',
        ];
    }

    /**
     * Get the talent profile that owns the certification.
     */
    public function talentProfile(): BelongsTo
    {
        return $this->belongsTo(TalentProfile::class, 'talent_id');
    }
}

