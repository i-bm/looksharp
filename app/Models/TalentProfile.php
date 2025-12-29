<?php

namespace App\Models;

use App\Enums\CurrentStatusEnum;
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
        'current_status',
        'student_id',
        'student_email',
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
            'current_status' => CurrentStatusEnum::class,
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
     * Get the projects for the talent profile.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(TalentProject::class, 'talent_id');
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
     * Get the preferred cities for the talent profile.
     */
    public function preferredCities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'preferred_city_talent_profile')
            ->withTimestamps();
    }

    /**
     * Get the work models for the talent profile.
     */
    public function workModels(): BelongsToMany
    {
        return $this->belongsToMany(WorkModel::class, 'talent_profile_work_model')
            ->withTimestamps();
    }

    /**
     * Get the full name of the talent profile.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the current/primary/most recent education record.
     */
    public function getCurrentEducationAttribute(): ?TalentEducation
    {
        // First try to get current education (where is_current = true)
        $currentEducation = $this->education()
            ->where('is_current', true)
            ->with('institution')
            ->first();

        if ($currentEducation) {
            return $currentEducation;
        }

        // If no current, try primary education
        $primaryEducation = $this->education()
            ->where('is_primary', true)
            ->with('institution')
            ->first();

        if ($primaryEducation) {
            return $primaryEducation;
        }

        // If still no education, get most recent by start_date
        $recentEducation = $this->education()
            ->whereNotNull('start_date')
            ->with('institution')
            ->orderBy('start_date', 'desc')
            ->first();

        if ($recentEducation) {
            return $recentEducation;
        }

        // Last resort: get any education record
        return $this->education()
            ->with('institution')
            ->first();
    }

    /**
     * Get the primary or current education institution.
     */
    public function getPrimaryInstitution(): ?Institution
    {
        // First try to get primary education
        $primaryEducation = $this->education()
            ->where('is_primary', true)
            ->with('institution')
            ->first();

        if ($primaryEducation && $primaryEducation->institution) {
            return $primaryEducation->institution;
        }

        // Then try current education
        $currentEducation = $this->education()
            ->where('is_current', true)
            ->with('institution')
            ->first();

        if ($currentEducation && $currentEducation->institution) {
            return $currentEducation->institution;
        }

        // Finally, get the most recent education
        $recentEducation = $this->education()
            ->with('institution')
            ->orderBy('start_date', 'desc')
            ->first();

        return $recentEducation?->institution;
    }
}
