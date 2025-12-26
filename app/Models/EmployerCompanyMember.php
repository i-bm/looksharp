<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class EmployerCompanyMember extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\EmployerCompanyMemberFactory> */
    use HasFactory, HasUuids;
    use \OwenIt\Auditing\Auditable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'employer_company_id',
        'user_id',
        'role',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(EmployerCompany::class, 'employer_company_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

