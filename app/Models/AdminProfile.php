<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class AdminProfile extends Model implements Auditable
{
    use HasFactory, HasUuids;
    use \OwenIt\Auditing\Auditable; // for auditing

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone_number',
        'profile_photo',
        'bio',
        'department',
        'job_title',
        'office_location',
        'extension',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // Add any casts if needed
        ];
    }

    /**
     * Get the user that owns the admin profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full name of the admin profile.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
