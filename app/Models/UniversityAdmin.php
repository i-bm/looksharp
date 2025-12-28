<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class UniversityAdmin extends Model implements Auditable
{
    use HasUuids;
    use \OwenIt\Auditing\Auditable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'institution_id',
        'name',
        'role',
        'email',
        'phone',
        'profile_completeness_score',
        'wizard_complete',
    ];

    protected function casts(): array
    {
        return [
            'profile_completeness_score' => 'integer',
            'wizard_complete' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}

