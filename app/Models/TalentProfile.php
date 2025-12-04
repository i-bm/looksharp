<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class TalentProfile extends Model implements Auditable
{
    use HasFactory, HasUuids;
    use \OwenIt\Auditing\Auditable; // for auditing

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'profile_photo',
        'video_introduction',
        'bio',
        'location',
        'nss_status',
        'nss_posting_location',
        'nss_posting_number',
        'verification_status',
        'verification_type',
        'verification_document_url',
        'verification_verified_at',
        'profile_completeness_score',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'verification_verified_at' => 'datetime',
            'profile_completeness_score' => 'integer',
        ];
    }

    /**
     * Get the user that owns the talent profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the education records for the talent profile.
     */
    public function education(): HasMany
    {
        return $this->hasMany(TalentEducation::class, 'talent_id');
    }

    /**
     * Get the skills for the talent profile.
     */
    public function skills(): HasMany
    {
        return $this->hasMany(TalentSkill::class, 'talent_id');
    }
}
