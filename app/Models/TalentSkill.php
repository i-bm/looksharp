<?php

namespace App\Models;

use App\Enums\ProficiencyLevelEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class TalentSkill extends Model implements Auditable
{
    use HasFactory, HasUuids;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'talent_id',
        'skill_name',
        'proficiency_level',
        'verified',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'proficiency_level' => ProficiencyLevelEnum::class,
            'verified' => 'boolean',
        ];
    }

    /**
     * Get the talent profile that owns the skill.
     */
    public function talentProfile(): BelongsTo
    {
        return $this->belongsTo(TalentProfile::class, 'talent_id');
    }
}
