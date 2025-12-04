<?php

namespace App\Services;

use App\Enums\DegreeTypeEnum;
use App\Enums\ProficiencyLevelEnum;
use App\Models\TalentEducation;
use App\Models\TalentProfile;
use App\Models\TalentSkill;
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
                $profile->update([
                    'first_name' => $data['first_name'] ?? null,
                    'last_name' => $data['last_name'] ?? null,
                    'date_of_birth' => $data['date_of_birth'] ?? null,
                    'gender' => $data['gender'] ?? null,
                    'location' => $data['location'] ?? null,
                    'bio' => $data['bio'] ?? null,
                ]);

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

        // Basic Info (30 points)
        $basicInfoFields = ['first_name', 'last_name', 'date_of_birth', 'gender', 'location'];
        $basicInfoCount = 0;
        foreach ($basicInfoFields as $field) {
            if (! empty($profile->$field)) {
                $basicInfoCount++;
            }
        }
        // Profile photo adds 5 points
        if (! empty($profile->profile_photo)) {
            $basicInfoCount += 0.5; // Counts as half a field
        }
        $score += min(30, ($basicInfoCount / count($basicInfoFields)) * 30);

        // Education (30 points) - At least one complete education record
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
            $score += $educationComplete ? 30 : 15; // Half points if incomplete
        }

        // Skills (20 points) - At least 3 skills
        $skillCount = $profile->skills()->count();
        if ($skillCount >= 3) {
            $score += 20;
        } elseif ($skillCount > 0) {
            $score += ($skillCount / 3) * 20; // Proportional points
        }

        // Verification (20 points) - Document uploaded
        if (! empty($profile->verification_document_url)) {
            $score += 20;
        }

        $profile->update(['profile_completeness_score' => (int) round($score)]);
    }

    /**
     * Get wizard progress information.
     */
    public function getWizardProgress(TalentProfile $profile): array
    {
        $steps = [
            'basic_info' => [
                'completed' => ! empty($profile->first_name) && ! empty($profile->last_name),
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
}

