<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class EmployerCompanyPhoto extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\EmployerCompanyPhotoFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    use \OwenIt\Auditing\Auditable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'employer_company_id',
        'photo_url',
        'caption',
        'display_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
        ];
    }

    public function employerCompany(): BelongsTo
    {
        return $this->belongsTo(EmployerCompany::class);
    }
}
