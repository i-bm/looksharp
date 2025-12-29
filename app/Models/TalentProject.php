<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class TalentProject extends Model implements Auditable
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
        'title',
        'description',
        'project_type',
        'image_url',
        'project_url',
        'technologies',
        'start_date',
        'end_date',
        'is_featured',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'technologies' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * Get the talent profile that owns the project.
     */
    public function talentProfile(): BelongsTo
    {
        return $this->belongsTo(TalentProfile::class, 'talent_id');
    }
}

