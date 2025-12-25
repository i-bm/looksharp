<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class EmployerCompanyTestimonial extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\EmployerCompanyTestimonialFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    use \OwenIt\Auditing\Auditable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'employer_company_id',
        'employee_name',
        'employee_title',
        'testimonial',
        'photo_url',
        'display_order',
        'is_featured',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    public function employerCompany(): BelongsTo
    {
        return $this->belongsTo(EmployerCompany::class);
    }
}
