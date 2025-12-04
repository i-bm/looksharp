<?php

namespace App\Models;

use App\Enums\DegreeTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class TalentEducation extends Model implements Auditable
{
    use HasUuids, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'talent_id',
        'institution_id',
        'degree_type',
        'field_of_study',
        'start_date',
        'end_date',
        'is_current',
        'gpa',
        'is_primary',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'degree_type' => DegreeTypeEnum::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
            'gpa' => 'decimal:2',
            'is_primary' => 'boolean',
        ];
    }

    /**
     * Get the talent profile that owns the education record.
     */
    public function talentProfile(): BelongsTo
    {
        return $this->belongsTo(TalentProfile::class, 'talent_id');
    }

    /**
     * Get the institution for this education record.
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}
