<?php

namespace App\Models;

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
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'website',
        'logo',
        'is_active',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the education records for this institution.
     */
    public function education(): HasMany
    {
        return $this->hasMany(TalentEducation::class);
    }
}
