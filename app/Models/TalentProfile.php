<?php

namespace App\Models;

use App\Enums\AvailabilityEnum;
use App\Enums\PreferredLocationEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'headline',
        'public_url',
        'date_of_birth',
        'gender',
        'profile_photo',
        'resume_url',
        'video_introduction',
        'bio',
        'location',
        'phone_number',
        'nss_status',
        'nss_posting_location',
        'nss_posting_number',
        'verification_status',
        'verification_type',
        'verification_document_url',
        'verification_verified_at',
        'profile_completeness_score',
        // Additional Details
        'fun_fact',
        'passion',
        'hobbies',
        // Portfolio & Social Links
        'github_url',
        'behance_url',
        'portfolio_url',
        'linkedin_url',
        'twitter_url',
        // Work Preferences
        'availability',
        'availability_details',
        'preferred_location',
        'salary_expectations',
        'job_categories',
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
            'is_profile_building_step_completed' => 'boolean',
            'availability' => AvailabilityEnum::class,
            'preferred_location' => PreferredLocationEnum::class,
            'salary_expectations' => 'decimal:2',
            'job_categories' => 'array',
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

    /**
     * Get the work history records for the talent profile.
     */
    public function workHistory(): HasMany
    {
        return $this->hasMany(TalentWorkHistory::class, 'talent_id');
    }

    /**
     * Get the languages for the talent profile.
     */
    public function languages(): HasMany
    {
        return $this->hasMany(TalentLanguage::class, 'talent_id');
    }

    /**
     * Get the certifications for the talent profile.
     */
    public function certifications(): HasMany
    {
        return $this->hasMany(TalentCertification::class, 'talent_id');
    }

    /**
     * Get the volunteer experiences for the talent profile.
     */
    public function volunteerExperiences(): HasMany
    {
        return $this->hasMany(TalentVolunteerExperience::class, 'talent_id');
    }

    /**
     * Get the leadership experiences for the talent profile.
     */
    public function leadershipExperiences(): HasMany
    {
        return $this->hasMany(TalentLeadershipExperience::class, 'talent_id');
    }

    /**
     * Get the gigs/freelance work for the talent profile.
     */
    public function gigsFreelance(): HasMany
    {
        return $this->hasMany(TalentGigsFreelance::class, 'talent_id');
    }

    /**
     * Get the career interest areas for the talent profile.
     */
    public function careerInterestAreas(): BelongsToMany
    {
        return $this->belongsToMany(CareerInterestArea::class, 'career_interest_area_talent_profile')
            ->withTimestamps();
    }

    /**
     * Get the full name of the talent profile.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
