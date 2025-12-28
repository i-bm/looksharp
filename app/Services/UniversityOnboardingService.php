<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\InstitutionPartnershipTierEnum;
use App\Enums\InstitutionTypeEnum;
use App\Enums\UserRoleEnum;
use App\Models\Institution;
use App\Models\UniversityAdmin;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UniversityOnboardingService
{
    public function getOrCreateUniversityAdmin(User $user): UniversityAdmin
    {
        if (! $user->hasRole(UserRoleEnum::UNIVERSITY->value)) {
            throw new \Exception('Only university users can access university onboarding.');
        }

        try {
            return DB::transaction(function () use ($user) {
                /** @var UniversityAdmin $admin */
                $admin = UniversityAdmin::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name' => $user->full_name ?: null,
                        'email' => $user->email,
                        'phone' => $user->phone_number,
                        'profile_completeness_score' => 0,
                        'wizard_complete' => false,
                    ]
                );

                $this->calculateCompletenessScore($admin);

                return $admin->fresh();
            });
        } catch (\Exception $e) {
            Log::error('UniversityOnboardingService: getOrCreateUniversityAdmin failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to initialize university profile. Please try again.');
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateUniversityProfile(User $user, UniversityAdmin $admin, array $data): UniversityAdmin
    {
        if ($admin->user_id !== $user->id) {
            throw new \Exception('Unauthorized.');
        }

        try {
            return DB::transaction(function () use ($admin, $data) {
                $admin->update([
                    'name' => $data['admin_name'] ?? $admin->name,
                    'role' => $data['admin_role'] ?? $admin->role,
                    'email' => $data['admin_email'] ?? $admin->email,
                    'phone' => $data['admin_phone'] ?? $admin->phone,
                ]);

                // Institution handling: either link to existing or create a new one
                $institution = null;

                if (! empty($data['institution_id'])) {
                    $institution = Institution::query()->findOrFail($data['institution_id']);
                } elseif (! empty($data['institution_name'])) {
                    $institution = Institution::create([
                        'name' => (string) $data['institution_name'],
                        'type' => isset($data['institution_type']) ? InstitutionTypeEnum::from($data['institution_type']) : null,
                        'location' => $data['institution_location'] ?? null,
                        'website' => $data['institution_website'] ?? null,
                        'email' => $data['institution_email'] ?? null,
                        'phone' => $data['institution_phone'] ?? null,
                        'city' => $data['institution_city'] ?? null,
                        'state' => $data['institution_state'] ?? null,
                        'country' => $data['institution_country'] ?? 'Ghana',
                        'student_email_domain' => $data['student_email_domain'] ?? null,
                        'is_active' => true,
                        'is_partner' => false,
                        'partnership_tier' => isset($data['partnership_tier']) && $data['partnership_tier']
                            ? InstitutionPartnershipTierEnum::from($data['partnership_tier'])
                            : null,
                    ]);
                }

                if ($institution) {
                    $admin->update(['institution_id' => $institution->id]);

                    // Update institution fields if provided (even when selecting an existing one)
                    $institutionUpdate = [];
                    foreach ([
                        'type' => 'institution_type',
                        'location' => 'institution_location',
                        'website' => 'institution_website',
                        'email' => 'institution_email',
                        'phone' => 'institution_phone',
                        'student_email_domain' => 'student_email_domain',
                        'city' => 'institution_city',
                        'state' => 'institution_state',
                        'country' => 'institution_country',
                        'partnership_tier' => 'partnership_tier',
                    ] as $field => $key) {
                        if (array_key_exists($key, $data)) {
                            $institutionUpdate[$field] = $data[$key] ?: null;
                        }
                    }

                    if (isset($institutionUpdate['type']) && $institutionUpdate['type'] !== null) {
                        $institutionUpdate['type'] = InstitutionTypeEnum::from((string) $institutionUpdate['type']);
                    }
                    if (isset($institutionUpdate['partnership_tier']) && $institutionUpdate['partnership_tier'] !== null) {
                        $institutionUpdate['partnership_tier'] = InstitutionPartnershipTierEnum::from((string) $institutionUpdate['partnership_tier']);
                    }
                    if (array_key_exists('country', $institutionUpdate) && empty($institutionUpdate['country'])) {
                        $institutionUpdate['country'] = 'Ghana';
                    }

                    if ($institutionUpdate !== []) {
                        $institution->update($institutionUpdate);
                    }
                }

                $this->calculateCompletenessScore($admin);

                return $admin->fresh(['institution']);
            });
        } catch (\Exception $e) {
            Log::error('UniversityOnboardingService: updateUniversityProfile failed', [
                'user_id' => $user->id,
                'university_admin_id' => $admin->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to update university profile. Please try again.');
        }
    }

    public function uploadInstitutionLogo(User $user, UniversityAdmin $admin, UploadedFile $file): Institution
    {
        if ($admin->user_id !== $user->id) {
            throw new \Exception('Unauthorized.');
        }

        if (! $admin->institution_id) {
            throw new \Exception('Please select an institution first before uploading a logo.');
        }

        /** @var Institution $institution */
        $institution = Institution::query()->findOrFail($admin->institution_id);

        try {
            return DB::transaction(function () use ($institution, $file, $admin) {
                if ($institution->logo) {
                    Storage::disk('public')->delete($institution->logo);
                }

                $path = $file->store('institution-logos', 'public');
                $institution->update(['logo' => $path]);

                $this->calculateCompletenessScore($admin->fresh());

                return $institution->fresh();
            });
        } catch (\Exception $e) {
            Log::error('UniversityOnboardingService: uploadInstitutionLogo failed', [
                'institution_id' => $institution->id,
                'university_admin_id' => $admin->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to upload institution logo. Please try again.');
        }
    }

    public function calculateCompletenessScore(UniversityAdmin $admin): void
    {
        try {
            $score = 0;

            $institution = $admin->institution;
            if (! $institution && $admin->institution_id) {
                $institution = Institution::query()->find($admin->institution_id);
            }

            // Institution link (30)
            if ($admin->institution_id) {
                $score += 30;
            }

            // Institution details (40)
            if ($institution) {
                if (! empty($institution->type)) {
                    $score += 10;
                }
                if (! empty($institution->location) || ! empty($institution->city) || ! empty($institution->state)) {
                    $score += 10;
                }
                if (! empty($institution->website)) {
                    $score += 10;
                }
                if (! empty($institution->logo)) {
                    $score += 10;
                }
            }

            // Admin contact (30)
            if (! empty($admin->name)) {
                $score += 10;
            }
            if (! empty($admin->role)) {
                $score += 10;
            }
            if (! empty($admin->email) || ! empty($admin->user?->email)) {
                $score += 5;
            }
            if (! empty($admin->phone) || ! empty($admin->user?->phone_number)) {
                $score += 5;
            }

            $score = min(100, $score);

            $wizardComplete = $score >= 70;

            $admin->update([
                'profile_completeness_score' => $score,
                'wizard_complete' => $wizardComplete,
            ]);
        } catch (\Exception $e) {
            Log::error('UniversityOnboardingService: calculateCompletenessScore failed', [
                'university_admin_id' => $admin->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getWizardProgress(UniversityAdmin $admin): array
    {
        $institution = $admin->institution;

        $steps = [
            'institution' => [
                'completed' => (bool) $admin->institution_id,
                'step' => 1,
            ],
            'institution_details' => [
                'completed' => $institution
                    && (! empty($institution->type) || ! empty($institution->location) || ! empty($institution->city))
                    && (! empty($institution->website) || ! empty($institution->logo)),
                'step' => 2,
            ],
            'admin_contact' => [
                'completed' => ! empty($admin->role) && (! empty($admin->email) || ! empty($admin->user?->email)),
                'step' => 3,
            ],
            'partnership' => [
                'completed' => $institution && ! empty($institution->partnership_tier),
                'step' => 4,
            ],
        ];

        $currentStep = 1;
        foreach ($steps as $step) {
            if (! $step['completed']) {
                $currentStep = $step['step'];
                break;
            }
            $currentStep = $step['step'];
        }

        return [
            'steps' => $steps,
            'current_step' => $currentStep,
            'completeness_score' => $admin->profile_completeness_score ?? 0,
        ];
    }
}

