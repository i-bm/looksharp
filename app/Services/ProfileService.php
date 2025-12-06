<?php

namespace App\Services;

use App\Enums\ProficiencyLevelEnum;
use App\Models\TalentCertification;
use App\Models\TalentEducation;
use App\Models\TalentGigsFreelance;
use App\Models\TalentLanguage;
use App\Models\TalentLeadershipExperience;
use App\Models\TalentProfile;
use App\Models\TalentSkill;
use App\Models\TalentVolunteerExperience;
use App\Models\TalentWorkHistory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    /**
     * Save basic profile information.
     */
    public function saveBasicInfo(TalentProfile $profile, array $data): TalentProfile
    {
        try {
            return DB::transaction(function () use ($profile, $data) {
                $updateData = [
                    'first_name' => $data['first_name'] ?? null,
                    'last_name' => $data['last_name'] ?? null,
                    'date_of_birth' => $data['date_of_birth'] ?? null,
                    'gender' => $data['gender'] ?? null,
                    'location' => $data['location'] ?? null,
                    'bio' => $data['bio'] ?? null,
                ];

                // Auto-generate public_url if it doesn't exist and we have a name
                if (empty($profile->public_url) && (! empty($data['first_name']) || ! empty($data['last_name']))) {
                    $firstName = $data['first_name'] ?? $profile->first_name ?? $profile->user->first_name ?? 'user';
                    $lastName = $data['last_name'] ?? $profile->last_name ?? $profile->user->last_name ?? '';
                    $updateData['public_url'] = generatePublicUrlSlug($firstName, $lastName);
                }

                $profile->update($updateData);

                $this->calculateCompletenessScore($profile);

                return $profile->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to save basic info: '.$e->getMessage(), [
                'profile_id' => $profile->id,
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to save basic information. Please try again.');
        }
    }

    /**
     * Upload and save profile photo.
     */
    public function uploadProfilePhoto(TalentProfile $profile, UploadedFile $file): TalentProfile
    {
        try {
            return DB::transaction(function () use ($profile, $file) {
                // Delete old photo if exists
                if ($profile->profile_photo) {
                    Storage::disk('public')->delete($profile->profile_photo);
                }

                // Store new photo
                $path = $file->store('profile-photos', 'public');
                $profile->update(['profile_photo' => $path]);

                $this->calculateCompletenessScore($profile);

                return $profile->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to upload profile photo: '.$e->getMessage(), [
                'profile_id' => $profile->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to upload profile photo. Please try again.');
        }
    }

    /**
     * Upload and save resume.
     */
    public function uploadResume(TalentProfile $profile, UploadedFile $file): TalentProfile
    {
        try {
            return DB::transaction(function () use ($profile, $file) {
                // Delete old resume if exists
                if ($profile->resume_url) {
                    Storage::disk('private')->delete($profile->resume_url);
                }

                // Store new resume in private storage
                $path = $file->store('resumes', 'private');
                $profile->update(['resume_url' => $path]);

                $this->calculateCompletenessScore($profile);

                return $profile->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to upload resume: '.$e->getMessage(), [
                'profile_id' => $profile->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to upload resume. Please try again.');
        }
    }

    /**
     * Update video introduction.
     */
    public function updateVideoIntroduction(TalentProfile $profile, array $data): TalentProfile
    {
        try {
            return DB::transaction(function () use ($profile, $data) {
                $videoUrl = $data['video_introduction'] ?? null;
                // Trim whitespace and convert empty string to null
                $videoUrl = $videoUrl !== null ? trim($videoUrl) : null;
                $videoUrl = $videoUrl === '' ? null : $videoUrl;

                $profile->update([
                    'video_introduction' => $videoUrl,
                ]);

                $this->calculateCompletenessScore($profile);

                return $profile->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to update video introduction: '.$e->getMessage(), [
                'profile_id' => $profile->id,
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to update video introduction. Please try again.');
        }
    }

    /**
     * Save education record.
     */
    public function saveEducation(TalentProfile $profile, array $data): TalentEducation
    {
        try {
            return DB::transaction(function () use ($profile, $data) {
                // If this is marked as primary, unmark other primary records
                if (isset($data['is_primary']) && $data['is_primary']) {
                    $profile->education()->update(['is_primary' => false]);
                }

                $education = TalentEducation::create([
                    'talent_id' => $profile->id,
                    'institution_id' => $data['institution_id'] ?? null,
                    'degree_type' => $data['degree_type'] ?? null,
                    'field_of_study' => $data['field_of_study'] ?? null,
                    'start_date' => $data['start_date'] ?? null,
                    'end_date' => $data['end_date'] ?? null,
                    'is_current' => $data['is_current'] ?? false,
                    'gpa' => $data['gpa'] ?? null,
                    'is_primary' => $data['is_primary'] ?? false,
                ]);

                $this->calculateCompletenessScore($profile);

                return $education;
            });
        } catch (\Exception $e) {
            Log::error('Failed to save education: '.$e->getMessage(), [
                'profile_id' => $profile->id,
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to save education record. Please try again.');
        }
    }

    /**
     * Update education record.
     */
    public function updateEducation(TalentEducation $education, array $data): TalentEducation
    {
        try {
            return DB::transaction(function () use ($education, $data) {
                // If this is marked as primary, unmark other primary records
                if (isset($data['is_primary']) && $data['is_primary']) {
                    $education->talentProfile->education()
                        ->where('id', '!=', $education->id)
                        ->update(['is_primary' => false]);
                }

                $education->update($data);

                $this->calculateCompletenessScore($education->talentProfile);

                return $education->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to update education: '.$e->getMessage(), [
                'education_id' => $education->id,
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to update education record. Please try again.');
        }
    }

    /**
     * Delete education record.
     */
    public function deleteEducation(TalentEducation $education): bool
    {
        try {
            return DB::transaction(function () use ($education) {
                $profile = $education->talentProfile;
                $deleted = $education->delete();

                $this->calculateCompletenessScore($profile);

                return $deleted;
            });
        } catch (\Exception $e) {
            Log::error('Failed to delete education: '.$e->getMessage(), [
                'education_id' => $education->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to delete education record. Please try again.');
        }
    }

    /**
     * Save skill record.
     */
    public function saveSkill(TalentProfile $profile, array $data): TalentSkill
    {
        try {
            return DB::transaction(function () use ($profile, $data) {
                $skill = TalentSkill::create([
                    'talent_id' => $profile->id,
                    'skill_name' => $data['skill_name'] ?? null,
                    'proficiency_level' => $data['proficiency_level'] ?? ProficiencyLevelEnum::BEGINNER,
                    'verified' => $data['verified'] ?? false,
                ]);

                $this->calculateCompletenessScore($profile);

                return $skill;
            });
        } catch (\Exception $e) {
            Log::error('Failed to save skill: '.$e->getMessage(), [
                'profile_id' => $profile->id,
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to save skill. Please try again.');
        }
    }

    /**
     * Delete skill record.
     */
    public function deleteSkill(TalentSkill $skill): bool
    {
        try {
            return DB::transaction(function () use ($skill) {
                $profile = $skill->talentProfile;
                $deleted = $skill->delete();

                $this->calculateCompletenessScore($profile);

                return $deleted;
            });
        } catch (\Exception $e) {
            Log::error('Failed to delete skill: '.$e->getMessage(), [
                'skill_id' => $skill->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to delete skill. Please try again.');
        }
    }

    /**
     * Upload and save verification document.
     */
    public function uploadVerificationDocument(TalentProfile $profile, UploadedFile $file, string $verificationType): TalentProfile
    {
        try {
            return DB::transaction(function () use ($profile, $file, $verificationType) {
                // Delete old document if exists
                if ($profile->verification_document_url) {
                    Storage::disk('private')->delete($profile->verification_document_url);
                }

                // Store new document in private storage
                $path = $file->store('verification-documents', 'private');
                $profile->update([
                    'verification_document_url' => $path,
                    'verification_type' => $verificationType,
                    'verification_status' => 'pending',
                ]);

                $this->calculateCompletenessScore($profile);

                return $profile->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to upload verification document: '.$e->getMessage(), [
                'profile_id' => $profile->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to upload verification document. Please try again.');
        }
    }

    /**
     * Calculate and update profile completeness score (0-100).
     */
    public function calculateCompletenessScore(TalentProfile $profile): void
    {
        $score = 0;
        $maxScore = 100;

        // Basic Info (15 points) - Core required fields
        $basicInfoFields = ['first_name', 'last_name', 'date_of_birth', 'gender', 'location'];
        $basicInfoCount = 0;
        foreach ($basicInfoFields as $field) {
            if (! empty($profile->$field)) {
                $basicInfoCount++;
            }
        }
        $score += min(15, ($basicInfoCount / count($basicInfoFields)) * 15);

        // Profile Photo (5 points)
        if (! empty($profile->profile_photo)) {
            $score += 5;
        }

        // Bio (5 points)
        if (! empty($profile->bio)) {
            $score += 5;
        }

        // Video Introduction (3 points) - 30-sec max video introduction
        if (! empty($profile->video_introduction)) {
            $score += 3;
        }

        // Education (12 points) - At least one complete education record
        $hasEducation = $profile->education()->exists();
        if ($hasEducation) {
            $education = $profile->education()->first();
            $educationFields = ['institution_id', 'degree_type', 'field_of_study', 'start_date'];
            $educationComplete = true;
            foreach ($educationFields as $field) {
                if (empty($education->$field)) {
                    $educationComplete = false;
                    break;
                }
            }
            $score += $educationComplete ? 12 : 6; // Half points if incomplete
        }

        // Skills (10 points) - At least 3 skills
        $skillCount = $profile->skills()->count();
        if ($skillCount >= 3) {
            $score += 10;
        } elseif ($skillCount > 0) {
            $score += ($skillCount / 3) * 10; // Proportional points
        }

        // Work History (8 points) - At least one work history record
        $workHistoryCount = $profile->workHistory()->count();
        if ($workHistoryCount > 0) {
            $score += 8;
        }

        // Languages (4 points) - At least one language
        $languageCount = $profile->languages()->count();
        if ($languageCount > 0) {
            $score += 4;
        }

        // Certifications (4 points) - At least one certification
        $certificationCount = $profile->certifications()->count();
        if ($certificationCount > 0) {
            $score += 4;
        }

        // Volunteer Experiences (2 points) - At least one volunteer experience
        $volunteerCount = $profile->volunteerExperiences()->count();
        if ($volunteerCount > 0) {
            $score += 2;
        }

        // Leadership Experiences (2 points) - At least one leadership experience
        $leadershipCount = $profile->leadershipExperiences()->count();
        if ($leadershipCount > 0) {
            $score += 2;
        }

        // Gigs/Freelance (2 points) - At least one gig/freelance work
        $gigsCount = $profile->gigsFreelance()->count();
        if ($gigsCount > 0) {
            $score += 2;
        }

        // Resume (5 points) - Resume uploaded
        if (! empty($profile->resume_url)) {
            $score += 5;
        }

        // Social Links (2 points) - At least one social link
        $socialLinks = [
            $profile->github_url,
            $profile->behance_url,
            $profile->portfolio_url,
            $profile->linkedin_url,
            $profile->twitter_url,
        ];
        $socialLinksCount = count(array_filter($socialLinks));
        if ($socialLinksCount > 0) {
            $score += 2;
        }

        // Work Preferences (8 points total)
        // - Basic work preferences (4 points): 1 point per field
        $workPrefsFields = [
            $profile->availability,
            $profile->preferred_location,
            $profile->availability_details,
            $profile->salary_expectations,
        ];
        $workPrefsCount = count(array_filter($workPrefsFields));
        $score += min(4, $workPrefsCount); // 1 point per field, max 4 points

        // - Career Interest Areas (4 points): Important for job matching
        $careerInterestAreasCount = $profile->careerInterestAreas()->count();
        if ($careerInterestAreasCount >= 3) {
            $score += 4; // Full points for 3+ areas
        } elseif ($careerInterestAreasCount === 2) {
            $score += 3; // 3 points for 2 areas
        } elseif ($careerInterestAreasCount === 1) {
            $score += 2; // 2 points for 1 area
        }

        // Additional Details (2 points) - Fun fact, passion, or hobbies
        $additionalDetails = [
            $profile->fun_fact,
            $profile->passion,
            $profile->hobbies,
        ];
        $additionalDetailsCount = count(array_filter($additionalDetails));
        if ($additionalDetailsCount > 0) {
            $score += 2;
        }

        // NSS Info (3 points) - National Service information (important for Ghana context)
        $nssFields = [
            $profile->nss_status,
            $profile->nss_posting_location,
            $profile->nss_posting_number,
        ];
        $nssFieldsCount = count(array_filter($nssFields));
        if ($nssFieldsCount > 0) {
            $score += ($nssFieldsCount / 3) * 3; // Proportional points
        }

        // Verification (3 points) - Document uploaded
        if (! empty($profile->verification_document_url)) {
            $score += 3;
        }

        // Ensure score doesn't exceed 100
        $score = min($maxScore, $score);

        $profile->update(['profile_completeness_score' => (int) round($score)]);
    }

    /**
     * Get wizard progress information.
     */
    public function getWizardProgress(TalentProfile $profile): array
    {
        $steps = [
            'basic_info' => [
                'completed' => ! empty($profile->first_name) && ! empty($profile->last_name) && ! empty($profile->profile_photo),
                'step' => 1,
            ],
            'education' => [
                'completed' => $profile->education()->exists(),
                'step' => 2,
            ],
            'skills' => [
                'completed' => $profile->skills()->count() >= 3,
                'step' => 3,
            ],
            'verification' => [
                'completed' => ! empty($profile->verification_document_url),
                'step' => 4,
            ],
        ];

        // Determine current step (first incomplete step, or last step if all complete)
        $currentStep = 1;
        foreach ($steps as $stepKey => $stepData) {
            if (! $stepData['completed']) {
                $currentStep = $stepData['step'];
                break;
            }
            $currentStep = $stepData['step'];
        }

        return [
            'steps' => $steps,
            'current_step' => $currentStep,
            'completeness_score' => $profile->profile_completeness_score,
        ];
    }

    /**
     * Update profile information.
     */
    public function updateProfile(TalentProfile $profile, array $data): TalentProfile
    {
        try {
            return DB::transaction(function () use ($profile, $data) {
                $profile->update([
                    'first_name' => $data['first_name'] ?? $profile->first_name,
                    'last_name' => $data['last_name'] ?? $profile->last_name,
                    'date_of_birth' => $data['date_of_birth'] ?? $profile->date_of_birth,
                    'gender' => $data['gender'] ?? $profile->gender,
                    'location' => $data['location'] ?? $profile->location,
                    'bio' => $data['bio'] ?? $profile->bio,
                    'video_introduction' => $data['video_introduction'] ?? $profile->video_introduction,
                    'nss_status' => $data['nss_status'] ?? $profile->nss_status,
                    'nss_posting_location' => $data['nss_posting_location'] ?? $profile->nss_posting_location,
                    'nss_posting_number' => $data['nss_posting_number'] ?? $profile->nss_posting_number,
                    // Additional Details
                    'fun_fact' => $data['fun_fact'] ?? $profile->fun_fact,
                    'passion' => $data['passion'] ?? $profile->passion,
                    'hobbies' => $data['hobbies'] ?? $profile->hobbies,
                    // Portfolio & Social Links
                    'github_url' => $data['github_url'] ?? $profile->github_url,
                    'behance_url' => $data['behance_url'] ?? $profile->behance_url,
                    'portfolio_url' => $data['portfolio_url'] ?? $profile->portfolio_url,
                    'linkedin_url' => $data['linkedin_url'] ?? $profile->linkedin_url,
                    'twitter_url' => $data['twitter_url'] ?? $profile->twitter_url,
                    // Work Preferences
                    'availability' => $data['availability'] ?? $profile->availability,
                    'availability_details' => $data['availability_details'] ?? $profile->availability_details,
                    'preferred_location' => $data['preferred_location'] ?? $profile->preferred_location,
                    'salary_expectations' => $data['salary_expectations'] ?? $profile->salary_expectations,
                ]);

                $this->calculateCompletenessScore($profile);

                return $profile->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to update profile: '.$e->getMessage(), [
                'profile_id' => $profile->id,
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to update profile. Please try again.');
        }
    }

    /**
     * Save work history record.
     */
    public function saveWorkHistory(TalentProfile $profile, array $data): TalentWorkHistory
    {
        try {
            return DB::transaction(function () use ($profile, $data) {
                $workHistory = TalentWorkHistory::create([
                    'talent_id' => $profile->id,
                    'company' => $data['company'] ?? null,
                    'position' => $data['position'] ?? null,
                    'description' => $data['description'] ?? null,
                    'location' => $data['location'] ?? null,
                    'start_date' => $data['start_date'] ?? null,
                    'end_date' => $data['end_date'] ?? null,
                    'is_current' => $data['is_current'] ?? false,
                ]);

                $this->calculateCompletenessScore($profile);

                return $workHistory;
            });
        } catch (\Exception $e) {
            Log::error('Failed to save work history: '.$e->getMessage(), [
                'profile_id' => $profile->id,
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to save work history record. Please try again.');
        }
    }

    /**
     * Delete work history record.
     */
    public function deleteWorkHistory(TalentWorkHistory $workHistory): bool
    {
        try {
            return DB::transaction(function () use ($workHistory) {
                $profile = $workHistory->talentProfile;
                $deleted = $workHistory->delete();

                $this->calculateCompletenessScore($profile);

                return $deleted;
            });
        } catch (\Exception $e) {
            Log::error('Failed to delete work history: '.$e->getMessage(), [
                'work_history_id' => $workHistory->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to delete work history record. Please try again.');
        }
    }

    /**
     * Save language record.
     */
    public function saveLanguage(TalentProfile $profile, array $data): TalentLanguage
    {
        try {
            return DB::transaction(function () use ($profile, $data) {
                $language = TalentLanguage::create([
                    'talent_id' => $profile->id,
                    'language_name' => $data['language_name'] ?? null,
                    'proficiency_level' => $data['proficiency_level'] ?? ProficiencyLevelEnum::BEGINNER,
                ]);

                $this->calculateCompletenessScore($profile);

                return $language;
            });
        } catch (\Exception $e) {
            Log::error('Failed to save language: '.$e->getMessage(), [
                'profile_id' => $profile->id,
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to save language. Please try again.');
        }
    }

    /**
     * Delete language record.
     */
    public function deleteLanguage(TalentLanguage $language): bool
    {
        try {
            return DB::transaction(function () use ($language) {
                $profile = $language->talentProfile;
                $deleted = $language->delete();

                $this->calculateCompletenessScore($profile);

                return $deleted;
            });
        } catch (\Exception $e) {
            Log::error('Failed to delete language: '.$e->getMessage(), [
                'language_id' => $language->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to delete language. Please try again.');
        }
    }

    /**
     * Save certification record.
     */
    public function saveCertification(TalentProfile $profile, array $data): TalentCertification
    {
        try {
            return DB::transaction(function () use ($profile, $data) {
                $certification = TalentCertification::create([
                    'talent_id' => $profile->id,
                    'name' => $data['name'] ?? null,
                    'issuer' => $data['issuer'] ?? null,
                    'date_obtained' => $data['date_obtained'] ?? null,
                    'expiration_date' => $data['expiration_date'] ?? null,
                    'credential_url' => $data['credential_url'] ?? null,
                ]);

                $this->calculateCompletenessScore($profile);

                return $certification;
            });
        } catch (\Exception $e) {
            Log::error('Failed to save certification: '.$e->getMessage(), [
                'profile_id' => $profile->id,
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to save certification. Please try again.');
        }
    }

    /**
     * Delete certification record.
     */
    public function deleteCertification(TalentCertification $certification): bool
    {
        try {
            return DB::transaction(function () use ($certification) {
                $profile = $certification->talentProfile;
                $deleted = $certification->delete();

                $this->calculateCompletenessScore($profile);

                return $deleted;
            });
        } catch (\Exception $e) {
            Log::error('Failed to delete certification: '.$e->getMessage(), [
                'certification_id' => $certification->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to delete certification. Please try again.');
        }
    }

    /**
     * Save volunteer experience record.
     */
    public function saveVolunteerExperience(TalentProfile $profile, array $data): TalentVolunteerExperience
    {
        try {
            return DB::transaction(function () use ($profile, $data) {
                $volunteerExperience = TalentVolunteerExperience::create([
                    'talent_id' => $profile->id,
                    'organization' => $data['organization'] ?? null,
                    'start_date' => $data['start_date'] ?? null,
                    'end_date' => $data['end_date'] ?? null,
                    'is_current' => $data['is_current'] ?? false,
                    'details' => $data['details'] ?? null,
                ]);

                $this->calculateCompletenessScore($profile);

                return $volunteerExperience;
            });
        } catch (\Exception $e) {
            Log::error('Failed to save volunteer experience: '.$e->getMessage(), [
                'profile_id' => $profile->id,
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to save volunteer experience. Please try again.');
        }
    }

    /**
     * Delete volunteer experience record.
     */
    public function deleteVolunteerExperience(TalentVolunteerExperience $volunteerExperience): bool
    {
        try {
            return DB::transaction(function () use ($volunteerExperience) {
                $profile = $volunteerExperience->talentProfile;
                $deleted = $volunteerExperience->delete();

                $this->calculateCompletenessScore($profile);

                return $deleted;
            });
        } catch (\Exception $e) {
            Log::error('Failed to delete volunteer experience: '.$e->getMessage(), [
                'volunteer_experience_id' => $volunteerExperience->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to delete volunteer experience. Please try again.');
        }
    }

    /**
     * Save leadership experience record.
     */
    public function saveLeadershipExperience(TalentProfile $profile, array $data): TalentLeadershipExperience
    {
        try {
            return DB::transaction(function () use ($profile, $data) {
                $leadershipExperience = TalentLeadershipExperience::create([
                    'talent_id' => $profile->id,
                    'organization' => $data['organization'] ?? null,
                    'title' => $data['title'] ?? null,
                    'start_date' => $data['start_date'] ?? null,
                    'end_date' => $data['end_date'] ?? null,
                    'is_current' => $data['is_current'] ?? false,
                    'details' => $data['details'] ?? null,
                ]);

                $this->calculateCompletenessScore($profile);

                return $leadershipExperience;
            });
        } catch (\Exception $e) {
            Log::error('Failed to save leadership experience: '.$e->getMessage(), [
                'profile_id' => $profile->id,
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to save leadership experience. Please try again.');
        }
    }

    /**
     * Delete leadership experience record.
     */
    public function deleteLeadershipExperience(TalentLeadershipExperience $leadershipExperience): bool
    {
        try {
            return DB::transaction(function () use ($leadershipExperience) {
                $profile = $leadershipExperience->talentProfile;
                $deleted = $leadershipExperience->delete();

                $this->calculateCompletenessScore($profile);

                return $deleted;
            });
        } catch (\Exception $e) {
            Log::error('Failed to delete leadership experience: '.$e->getMessage(), [
                'leadership_experience_id' => $leadershipExperience->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to delete leadership experience. Please try again.');
        }
    }

    /**
     * Save gigs/freelance record.
     */
    public function saveGigsFreelance(TalentProfile $profile, array $data): TalentGigsFreelance
    {
        try {
            return DB::transaction(function () use ($profile, $data) {
                $gigsFreelance = TalentGigsFreelance::create([
                    'talent_id' => $profile->id,
                    'company' => $data['company'] ?? null,
                    'title' => $data['title'] ?? null,
                    'start_date' => $data['start_date'] ?? null,
                    'end_date' => $data['end_date'] ?? null,
                    'is_current' => $data['is_current'] ?? false,
                    'details' => $data['details'] ?? null,
                ]);

                $this->calculateCompletenessScore($profile);

                return $gigsFreelance;
            });
        } catch (\Exception $e) {
            Log::error('Failed to save gigs/freelance: '.$e->getMessage(), [
                'profile_id' => $profile->id,
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to save gigs/freelance work. Please try again.');
        }
    }

    /**
     * Delete gigs/freelance record.
     */
    public function deleteGigsFreelance(TalentGigsFreelance $gigsFreelance): bool
    {
        try {
            return DB::transaction(function () use ($gigsFreelance) {
                $profile = $gigsFreelance->talentProfile;
                $deleted = $gigsFreelance->delete();

                $this->calculateCompletenessScore($profile);

                return $deleted;
            });
        } catch (\Exception $e) {
            Log::error('Failed to delete gigs/freelance: '.$e->getMessage(), [
                'gigs_freelance_id' => $gigsFreelance->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to delete gigs/freelance work. Please try again.');
        }
    }

    /**
     * Update about me / bio.
     */
    public function updateAboutMe(TalentProfile $profile, array $data): TalentProfile
    {
        try {
            return DB::transaction(function () use ($profile, $data) {
                $updateData = [
                    'bio' => $data['bio'] ?? null,
                ];

                if (isset($data['headline'])) {
                    $updateData['headline'] = $data['headline'] ?: null;
                }

                if (isset($data['public_url'])) {
                    $updateData['public_url'] = $data['public_url'] ?: null;
                }

                $profile->update($updateData);

                $this->calculateCompletenessScore($profile);

                return $profile->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to update about me: '.$e->getMessage(), [
                'profile_id' => $profile->id,
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to update about me. Please try again.');
        }
    }

    /**
     * Update fun fact.
     */
    public function updateFunFact(TalentProfile $profile, array $data): TalentProfile
    {
        try {
            return DB::transaction(function () use ($profile, $data) {
                $funFact = $data['fun_fact'] ?? null;
                // Trim whitespace and convert empty string to null
                $funFact = $funFact !== null ? trim($funFact) : null;
                $funFact = $funFact === '' ? null : $funFact;

                $profile->update([
                    'fun_fact' => $funFact,
                ]);

                $this->calculateCompletenessScore($profile);

                return $profile->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to update fun fact: '.$e->getMessage(), [
                'profile_id' => $profile->id,
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to update fun fact. Please try again.');
        }
    }

    /**
     * Update passion.
     */
    public function updatePassion(TalentProfile $profile, array $data): TalentProfile
    {
        try {
            return DB::transaction(function () use ($profile, $data) {
                $passion = $data['passion'] ?? null;
                // Trim whitespace and convert empty string to null
                $passion = $passion !== null ? trim($passion) : null;
                $passion = $passion === '' ? null : $passion;

                $profile->update([
                    'passion' => $passion,
                ]);

                $this->calculateCompletenessScore($profile);

                return $profile->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to update passion: '.$e->getMessage(), [
                'profile_id' => $profile->id,
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to update passion. Please try again.');
        }
    }

    /**
     * Update hobbies.
     */
    public function updateHobbies(TalentProfile $profile, array $data): TalentProfile
    {
        try {
            return DB::transaction(function () use ($profile, $data) {
                $profile->update([
                    'hobbies' => $data['hobbies'] ?? null,
                ]);

                $this->calculateCompletenessScore($profile);

                return $profile->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to update hobbies: '.$e->getMessage(), [
                'profile_id' => $profile->id,
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to update hobbies. Please try again.');
        }
    }

    /**
     * Update social links.
     */
    public function updateSocialLinks(TalentProfile $profile, array $data): TalentProfile
    {
        try {
            return DB::transaction(function () use ($profile, $data) {
                $profile->update([
                    'github_url' => $data['github_url'] ?? null,
                    'behance_url' => $data['behance_url'] ?? null,
                    'portfolio_url' => $data['portfolio_url'] ?? null,
                    'linkedin_url' => $data['linkedin_url'] ?? null,
                    'twitter_url' => $data['twitter_url'] ?? null,
                ]);

                $this->calculateCompletenessScore($profile);

                return $profile->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to update social links: '.$e->getMessage(), [
                'profile_id' => $profile->id,
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to update social links. Please try again.');
        }
    }

    /**
     * Update work preferences.
     */
    public function updateWorkPreferences(TalentProfile $profile, array $data): TalentProfile
    {
        try {
            return DB::transaction(function () use ($profile, $data) {
                $updateData = [
                    'availability' => $data['availability'] ?? null,
                    'availability_details' => $data['availability_details'] ?? null,
                    'preferred_location' => $data['preferred_location'] ?? null,
                    'salary_expectations' => $data['salary_expectations'] ?? null,
                ];

                $profile->update($updateData);

                // Handle career interest areas - sync the relationship
                if (isset($data['career_interest_areas'])) {
                    $careerInterestAreaIds = is_array($data['career_interest_areas'])
                        ? $data['career_interest_areas']
                        : [];
                    $profile->careerInterestAreas()->sync($careerInterestAreaIds);
                }

                $this->calculateCompletenessScore($profile);

                return $profile->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to update work preferences: '.$e->getMessage(), [
                'profile_id' => $profile->id,
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to update work preferences. Please try again.');
        }
    }
}
