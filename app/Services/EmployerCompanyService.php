<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EmployerCompanyMemberRoleEnum;
use App\Enums\EmployerCompanyStatusEnum;
use App\Enums\EmployerCompanyVerificationStatusEnum;
use App\Enums\UserRoleEnum;
use App\Models\EmployerCompany;
use App\Models\EmployerCompanyMember;
use App\Models\EmployerCompanyPhoto;
use App\Models\EmployerCompanyTestimonial;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EmployerCompanyService
{
    public function __construct(private NotificationService $notificationService) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function createCompanyForEmployer(User $employer, array $data): EmployerCompany
    {
        Log::info('EmployerCompanyService: createCompanyForEmployer started', [
            'user_id' => $employer->id,
        ]);

        if (! $employer->hasRole(UserRoleEnum::EMPLOYER->value)) {
            throw new \Exception('Only employers can create a company profile.');
        }

        if ($employer->employerCompany()) {
            throw new \Exception('You already have a company profile.');
        }

        try {
            return DB::transaction(function () use ($employer, $data) {
                $company = EmployerCompany::create([
                    'created_by_user_id' => $employer->id,
                    'legal_name' => (string) ($data['legal_name'] ?? ''),
                    'trading_name' => $data['trading_name'] ?? null,
                    'industry' => $data['industry'] ?? null,
                    'company_size' => $data['company_size'] ?? null,
                    'website' => $data['website'] ?? null,
                    'linkedin_url' => $data['linkedin_url'] ?? null,
                    'country' => $data['country'] ?? 'Ghana',
                    'city' => $data['city'] ?? null,
                    'address' => $data['address'] ?? null,
                    'official_email' => $data['official_email'] ?? null,
                    'phone_number' => $data['phone_number'] ?? null,
                    'registration_number' => $data['registration_number'] ?? null,
                    'primary_contact_name' => $data['primary_contact_name'] ?? null,
                    'primary_contact_title' => $data['primary_contact_title'] ?? null,
                    'primary_contact_email' => $data['primary_contact_email'] ?? null,
                    'primary_contact_phone' => $data['primary_contact_phone'] ?? null,
                    'status' => EmployerCompanyStatusEnum::DRAFT->value,
                    'wizard_complete' => false,
                ]);

                EmployerCompanyMember::create([
                    'employer_company_id' => $company->id,
                    'user_id' => $employer->id,
                    'role' => EmployerCompanyMemberRoleEnum::COMPANY_ADMIN->value,
                ]);

                Log::info('EmployerCompanyService: company created', [
                    'company_id' => $company->id,
                    'user_id' => $employer->id,
                ]);

                // Calculate initial completeness score
                $this->calculateCompletenessScore($company);
                $company->refresh();

                return $company;
            });
        } catch (\Exception $e) {
            Log::error('EmployerCompanyService: createCompanyForEmployer failed', [
                'user_id' => $employer->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to create company profile. Please try again.');
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateCompanyByEmployer(User $employer, EmployerCompany $company, array $data): EmployerCompany
    {
        Log::info('EmployerCompanyService: updateCompanyByEmployer started', [
            'user_id' => $employer->id,
            'company_id' => $company->id,
            'status' => $company->status,
        ]);

        if (! $company->isEditableByEmployer()) {
            throw new \Exception('This company profile cannot be edited in its current status.');
        }

        try {
            return DB::transaction(function () use ($company, $data) {
                $updateData = [];

                // Only include fields that are present in $data array
                $fields = [
                    'legal_name', 'trading_name', 'industry', 'company_size',
                    'website', 'linkedin_url', 'facebook_url', 'twitter_url',
                    'instagram_url', 'youtube_url', 'country', 'city', 'state_or_region', 'address',
                    'official_email', 'phone_number', 'registration_number',
                    'primary_contact_name', 'primary_contact_title',
                    'primary_contact_email', 'primary_contact_phone',
                    'owner_name', 'owner_ghana_card_number', 'owner_title',
                    'company_description', 'year_established', 'video_url',
                ];

                foreach ($fields as $field) {
                    if (array_key_exists($field, $data)) {
                        if ($field === 'legal_name' && isset($data[$field])) {
                            $updateData[$field] = (string) $data[$field];
                        } else {
                            $updateData[$field] = $data[$field];
                        }
                    }
                }

                // Ensure country has a default value if it's being updated
                if (isset($updateData['country']) && (empty($updateData['country']) || trim($updateData['country']) === '')) {
                    $updateData['country'] = 'Ghana';
                }

                Log::info('EmployerCompanyService: updating company', [
                    'company_id' => $company->id,
                    'update_data' => $updateData,
                ]);

                $company->update($updateData);

                // Recalculate completeness score after update
                $this->calculateCompletenessScore($company);
                $company->refresh();

                Log::info('EmployerCompanyService: company updated successfully', [
                    'company_id' => $company->id,
                ]);

                return $company->fresh();
            });
        } catch (\Exception $e) {
            Log::error('EmployerCompanyService: updateCompanyByEmployer failed', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to update company profile. Please try again.');
        }
    }

    public function submitCompanyForReview(User $employer, EmployerCompany $company): EmployerCompany
    {
        Log::info('EmployerCompanyService: submitCompanyForReview started', [
            'user_id' => $employer->id,
            'company_id' => $company->id,
            'status' => $company->status,
        ]);

        if (! $company->isEditableByEmployer()) {
            throw new \Exception('This company profile cannot be submitted in its current status.');
        }

        try {
            $updated = DB::transaction(function () use ($company) {
                $company->update([
                    'status' => EmployerCompanyStatusEnum::SUBMITTED->value,
                    'submitted_at' => now(),
                    'reviewed_by_user_id' => null,
                    'review_notes' => null,
                    'approved_at' => null,
                    'rejected_at' => null,
                    'suspended_at' => null,
                ]);

                return $company->fresh();
            });

            $this->notifyAdminsCompanySubmitted($updated);

            return $updated;
        } catch (\Exception $e) {
            Log::error('EmployerCompanyService: submitCompanyForReview failed', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to submit company profile. Please try again.');
        }
    }

    /**
     * Admin: provision company and invite an employer admin user.
     *
     * @param  array<string, mixed>  $companyData
     */
    public function adminProvisionCompanyAndInvite(User $admin, array $companyData, string $inviteEmail): EmployerCompany
    {
        Log::info('EmployerCompanyService: adminProvisionCompanyAndInvite started', [
            'admin_user_id' => $admin->id,
            'invite_email' => $inviteEmail,
        ]);

        if (! $admin->hasRole(UserRoleEnum::ADMIN->value)) {
            throw new \Exception('Unauthorized.');
        }

        try {
            $company = DB::transaction(function () use ($admin, $companyData, $inviteEmail) {
                $invitee = User::firstOrCreate(
                    ['email' => $inviteEmail],
                    [
                        'user_type' => UserRoleEnum::EMPLOYER->value,
                        'password' => null,
                        'user_type_checked' => true,
                    ]
                );

                // Ensure user_type and user_type_checked are set correctly
                // (handles case where user already existed)
                $invitee->user_type = UserRoleEnum::EMPLOYER->value;
                $invitee->user_type_checked = true;
                $invitee->save();

                if (! $invitee->hasRole(UserRoleEnum::EMPLOYER->value)) {
                    $invitee->assignRole(UserRoleEnum::EMPLOYER->value);
                }

                $company = EmployerCompany::create([
                    'created_by_user_id' => $admin->id,
                    'legal_name' => (string) ($companyData['legal_name'] ?? ''),
                    'trading_name' => $companyData['trading_name'] ?? null,
                    'industry' => $companyData['industry'] ?? null,
                    'company_size' => $companyData['company_size'] ?? null,
                    'website' => $companyData['website'] ?? null,
                    'linkedin_url' => $companyData['linkedin_url'] ?? null,
                    'country' => $companyData['country'] ?? 'Ghana',
                    'city' => $companyData['city'] ?? null,
                    'address' => $companyData['address'] ?? null,
                    'official_email' => $companyData['official_email'] ?? $inviteEmail,
                    'phone_number' => $companyData['phone_number'] ?? null,
                    'registration_number' => $companyData['registration_number'] ?? null,
                    'primary_contact_name' => $companyData['primary_contact_name'] ?? null,
                    'primary_contact_title' => $companyData['primary_contact_title'] ?? null,
                    'primary_contact_email' => $companyData['primary_contact_email'] ?? $inviteEmail,
                    'primary_contact_phone' => $companyData['primary_contact_phone'] ?? null,
                    'status' => EmployerCompanyStatusEnum::DRAFT->value,
                    'wizard_complete' => false,
                ]);

                EmployerCompanyMember::create([
                    'employer_company_id' => $company->id,
                    'user_id' => $invitee->id,
                    'role' => EmployerCompanyMemberRoleEnum::COMPANY_ADMIN->value,
                ]);

                Log::info('EmployerCompanyService: admin provisioned company', [
                    'company_id' => $company->id,
                    'admin_user_id' => $admin->id,
                    'invitee_user_id' => $invitee->id,
                ]);

                // Calculate initial completeness score
                $this->calculateCompletenessScore($company);
                $company->refresh();

                return $company->fresh();
            });

            $this->notifyInvitee($company, $inviteEmail);

            return $company;
        } catch (\Exception $e) {
            Log::error('EmployerCompanyService: adminProvisionCompanyAndInvite failed', [
                'admin_user_id' => $admin->id,
                'invite_email' => $inviteEmail,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to provision company. Please try again.');
        }
    }

    public function adminApprove(User $admin, EmployerCompany $company, ?string $notes = null): EmployerCompany
    {
        return $this->adminReview(
            admin: $admin,
            company: $company,
            status: EmployerCompanyStatusEnum::APPROVED,
            notes: $notes
        );
    }

    public function adminNeedsChanges(User $admin, EmployerCompany $company, string $notes): EmployerCompany
    {
        return $this->adminReview(
            admin: $admin,
            company: $company,
            status: EmployerCompanyStatusEnum::NEEDS_CHANGES,
            notes: $notes
        );
    }

    public function adminReject(User $admin, EmployerCompany $company, string $notes): EmployerCompany
    {
        return $this->adminReview(
            admin: $admin,
            company: $company,
            status: EmployerCompanyStatusEnum::REJECTED,
            notes: $notes
        );
    }

    public function adminSuspend(User $admin, EmployerCompany $company, string $notes): EmployerCompany
    {
        return $this->adminReview(
            admin: $admin,
            company: $company,
            status: EmployerCompanyStatusEnum::SUSPENDED,
            notes: $notes
        );
    }

    private function adminReview(User $admin, EmployerCompany $company, EmployerCompanyStatusEnum $status, ?string $notes): EmployerCompany
    {
        Log::info('EmployerCompanyService: adminReview started', [
            'admin_user_id' => $admin->id,
            'company_id' => $company->id,
            'new_status' => $status->value,
        ]);

        if (! $admin->hasRole(UserRoleEnum::ADMIN->value)) {
            throw new \Exception('Unauthorized.');
        }

        if (in_array($status, [EmployerCompanyStatusEnum::NEEDS_CHANGES, EmployerCompanyStatusEnum::REJECTED, EmployerCompanyStatusEnum::SUSPENDED], true)
            && (! $notes || trim($notes) === '')
        ) {
            throw new \Exception('Review notes are required for this action.');
        }

        try {
            $updated = DB::transaction(function () use ($admin, $company, $status, $notes) {
                $payload = [
                    'status' => $status->value,
                    'reviewed_by_user_id' => $admin->id,
                    'review_notes' => $notes,
                ];

                if ($status === EmployerCompanyStatusEnum::APPROVED) {
                    $payload['approved_at'] = now();
                    $payload['rejected_at'] = null;
                    $payload['suspended_at'] = null;
                }

                if ($status === EmployerCompanyStatusEnum::REJECTED) {
                    $payload['rejected_at'] = now();
                    $payload['approved_at'] = null;
                }

                if ($status === EmployerCompanyStatusEnum::SUSPENDED) {
                    $payload['suspended_at'] = now();
                }

                if ($status === EmployerCompanyStatusEnum::NEEDS_CHANGES) {
                    $payload['approved_at'] = null;
                    $payload['rejected_at'] = null;
                    $payload['suspended_at'] = null;
                }

                $company->update($payload);

                return $company->fresh();
            });

            $this->notifyCompanyStatusChanged($updated);

            return $updated;
        } catch (\Exception $e) {
            Log::error('EmployerCompanyService: adminReview failed', [
                'admin_user_id' => $admin->id,
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to update company status. Please try again.');
        }
    }

    private function notifyAdminsCompanySubmitted(EmployerCompany $company): void
    {
        $adminEmails = User::role(UserRoleEnum::ADMIN->value)->pluck('email')->filter()->all();

        $subject = 'New employer company submitted for review';
        $content = view('emails.employer-company-submitted', [
            'company' => $company,
        ])->render();

        foreach ($adminEmails as $email) {
            $this->notificationService->sendEmail($email, $subject, $content);
        }
    }

    private function notifyInvitee(EmployerCompany $company, string $inviteEmail): void
    {
        $subject = 'You have been invited to manage a company on '.config('app.name');
        $content = view('emails.employer-company-invite', [
            'company' => $company,
            'loginUrl' => url('/login/employer'),
        ])->render();

        $this->notificationService->sendEmail($inviteEmail, $subject, $content);
    }

    private function notifyCompanyStatusChanged(EmployerCompany $company): void
    {
        $emails = $company->members()
            ->wherePivot('role', EmployerCompanyMemberRoleEnum::COMPANY_ADMIN->value)
            ->pluck('email')
            ->filter()
            ->all();

        $subject = "Company review update: {$company->legal_name}";
        $content = view('emails.employer-company-status-updated', [
            'company' => $company,
            'loginUrl' => url('/login/employer'),
        ])->render();

        foreach ($emails as $email) {
            $this->notificationService->sendEmail($email, $subject, $content);
        }
    }

    /**
     * Helper to check if a field value is not empty (handles null and whitespace).
     */
    private function isFieldNotEmpty(?string $value): bool
    {
        return ! empty($value) && trim($value) !== '';
    }

    /**
     * Calculate and update company profile completeness score (0-100).
     */
    public function calculateCompletenessScore(EmployerCompany $company): void
    {
        Log::info('EmployerCompanyService: calculateCompletenessScore started', [
            'company_id' => $company->id,
        ]);

        try {
            $score = 0;
            $maxScore = 100;

            // Basic Info (20 points) - Core required fields
            $basicInfoFields = [
                'legal_name' => 8,
                'trading_name' => 4,
                'industry' => 4,
                'company_size' => 4,
            ];
            foreach ($basicInfoFields as $field => $points) {
                if ($this->isFieldNotEmpty($company->$field)) {
                    $score += $points;
                }
            }

            // Contact Information (15 points)
            $contactFields = [
                'official_email' => 4,
                'phone_number' => 4,
                'website' => 4,
                'linkedin_url' => 3,
            ];
            foreach ($contactFields as $field => $points) {
                if ($this->isFieldNotEmpty($company->$field)) {
                    $score += $points;
                }
            }

            // Location Information (10 points)
            $locationFields = [
                'country' => 3,
                'city' => 4,
                'address' => 3,
            ];
            foreach ($locationFields as $field => $points) {
                if ($this->isFieldNotEmpty($company->$field)) {
                    $score += $points;
                }
            }

            // Registration Details (10 points)
            if ($this->isFieldNotEmpty($company->registration_number)) {
                $score += 10;
            }

            // Verification Documents (15 points) - Critical for COM-04
            if ($this->isFieldNotEmpty($company->ghana_card_document_url)) {
                $score += 8;
            }
            if ($this->isFieldNotEmpty($company->business_registration_document_url)) {
                $score += 7;
            }

            // Primary Contact Information (15 points)
            $primaryContactFields = [
                'primary_contact_name' => 6,
                'primary_contact_title' => 3,
                'primary_contact_email' => 3,
                'primary_contact_phone' => 3,
            ];
            foreach ($primaryContactFields as $field => $points) {
                if ($this->isFieldNotEmpty($company->$field)) {
                    $score += $points;
                }
            }

            // Branding (15 points) - For EMP-02
            if ($this->isFieldNotEmpty($company->logo_url)) {
                $score += 5;
            }
            if ($this->isFieldNotEmpty($company->company_description)) {
                $score += 5;
            }
            if ($this->isFieldNotEmpty($company->video_url)) {
                $score += 5;
            }

            // Ensure score doesn't exceed 100
            $score = min($maxScore, $score);

            $company->update(['profile_completeness_score' => (int) round($score)]);

            Log::info('EmployerCompanyService: completeness score calculated', [
                'company_id' => $company->id,
                'score' => (int) round($score),
            ]);
        } catch (\Exception $e) {
            Log::error('EmployerCompanyService: calculateCompletenessScore failed', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Don't throw - just log the error so it doesn't break the flow
        }
    }

    /**
     * Get wizard progress information.
     */
    public function getWizardProgress(EmployerCompany $company): array
    {
        Log::info('EmployerCompanyService: getWizardProgress', [
            'company_id' => $company->id,
        ]);

        $steps = [
            'basic_info' => [
                'completed' => $this->isFieldNotEmpty($company->legal_name),
                'step' => 1,
            ],
            'contact_location' => [
                'completed' => $this->isFieldNotEmpty($company->city)
                    || $this->isFieldNotEmpty($company->address)
                    || $this->isFieldNotEmpty($company->phone_number)
                    || $this->isFieldNotEmpty($company->official_email)
                    || $this->isFieldNotEmpty($company->website)
                    || $this->isFieldNotEmpty($company->linkedin_url),
                'step' => 2,
            ],
            'registration' => [
                'completed' => $this->isFieldNotEmpty($company->registration_number),
                'step' => 3,
            ],
            'primary_contact' => [
                'completed' => $this->isFieldNotEmpty($company->primary_contact_name)
                    || $this->isFieldNotEmpty($company->primary_contact_email),
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

        // Calculate completeness score (percentage of completed steps)
        $completedSteps = 0;
        foreach ($steps as $stepData) {
            if ($stepData['completed']) {
                $completedSteps++;
            }
        }
        $completenessScore = (int) (($completedSteps / count($steps)) * 100);

        // Update wizard_complete flag if all steps are complete
        $allStepsComplete = $completedSteps === count($steps);
        if ($allStepsComplete && ! $company->wizard_complete) {
            try {
                DB::transaction(function () use ($company) {
                    $company->update(['wizard_complete' => true]);
                    $company->refresh();
                    Log::info('EmployerCompanyService: wizard marked as complete', [
                        'company_id' => $company->id,
                    ]);
                });
            } catch (\Exception $e) {
                Log::error('EmployerCompanyService: failed to update wizard_complete', [
                    'company_id' => $company->id,
                    'error' => $e->getMessage(),
                ]);
            }
        } elseif (! $allStepsComplete && $company->wizard_complete) {
            // If wizard was marked complete but steps are now incomplete, reset it
            try {
                DB::transaction(function () use ($company) {
                    $company->update(['wizard_complete' => false]);
                    $company->refresh();
                    Log::info('EmployerCompanyService: wizard marked as incomplete', [
                        'company_id' => $company->id,
                    ]);
                });
            } catch (\Exception $e) {
                Log::error('EmployerCompanyService: failed to update wizard_complete', [
                    'company_id' => $company->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'steps' => $steps,
            'current_step' => $currentStep,
            'completeness_score' => $completenessScore,
        ];
    }

    /**
     * Upload Ghana Card document for verification.
     */
    public function uploadGhanaCardDocument(User $employer, EmployerCompany $company, UploadedFile $file): EmployerCompany
    {
        Log::info('EmployerCompanyService: uploadGhanaCardDocument started', [
            'user_id' => $employer->id,
            'company_id' => $company->id,
        ]);

        if (! $company->isEditableByEmployer()) {
            throw new \Exception('This company profile cannot be edited in its current status.');
        }

        try {
            return DB::transaction(function () use ($company, $file) {
                // Delete old document if exists
                if ($company->ghana_card_document_url) {
                    Storage::disk('private')->delete($company->ghana_card_document_url);
                }

                // Store new document in private storage
                $path = $file->store('employer-verification/ghana-cards', 'private');
                $company->update([
                    'ghana_card_document_url' => $path,
                    'verification_status' => EmployerCompanyVerificationStatusEnum::PENDING->value,
                ]);

                Log::info('EmployerCompanyService: Ghana Card document uploaded', [
                    'company_id' => $company->id,
                ]);

                $this->calculateCompletenessScore($company);

                return $company->fresh();
            });
        } catch (\Exception $e) {
            Log::error('EmployerCompanyService: uploadGhanaCardDocument failed', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to upload Ghana Card document. Please try again.');
        }
    }

    /**
     * Upload business registration document for verification.
     */
    public function uploadBusinessRegistrationDocument(User $employer, EmployerCompany $company, UploadedFile $file): EmployerCompany
    {
        Log::info('EmployerCompanyService: uploadBusinessRegistrationDocument started', [
            'user_id' => $employer->id,
            'company_id' => $company->id,
        ]);

        if (! $company->isEditableByEmployer()) {
            throw new \Exception('This company profile cannot be edited in its current status.');
        }

        try {
            return DB::transaction(function () use ($company, $file) {
                // Delete old document if exists
                if ($company->business_registration_document_url) {
                    Storage::disk('private')->delete($company->business_registration_document_url);
                }

                // Store new document in private storage
                $path = $file->store('employer-verification/business-registration', 'private');
                $company->update([
                    'business_registration_document_url' => $path,
                    'verification_status' => EmployerCompanyVerificationStatusEnum::PENDING->value,
                ]);

                Log::info('EmployerCompanyService: business registration document uploaded', [
                    'company_id' => $company->id,
                ]);

                $this->calculateCompletenessScore($company);

                return $company->fresh();
            });
        } catch (\Exception $e) {
            Log::error('EmployerCompanyService: uploadBusinessRegistrationDocument failed', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to upload business registration document. Please try again.');
        }
    }

    /**
     * Upload company logo.
     */
    public function uploadLogo(User $employer, EmployerCompany $company, UploadedFile $file): EmployerCompany
    {
        Log::info('EmployerCompanyService: uploadLogo started', [
            'user_id' => $employer->id,
            'company_id' => $company->id,
        ]);

        if (! $company->isEditableByEmployer()) {
            throw new \Exception('This company profile cannot be edited in its current status.');
        }

        try {
            return DB::transaction(function () use ($company, $file) {
                // Delete old logo if exists
                if ($company->logo_url) {
                    Storage::disk('public')->delete($company->logo_url);
                }

                // Store new logo in public storage
                $path = $file->store('employer-logos', 'public');
                $company->update(['logo_url' => $path]);

                Log::info('EmployerCompanyService: logo uploaded', [
                    'company_id' => $company->id,
                ]);

                $this->calculateCompletenessScore($company);

                return $company->fresh();
            });
        } catch (\Exception $e) {
            Log::error('EmployerCompanyService: uploadLogo failed', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to upload logo. Please try again.');
        }
    }

    /**
     * Upload company photo.
     */
    public function uploadPhoto(User $employer, EmployerCompany $company, UploadedFile $file, ?string $caption = null): EmployerCompanyPhoto
    {
        Log::info('EmployerCompanyService: uploadPhoto started', [
            'user_id' => $employer->id,
            'company_id' => $company->id,
        ]);

        if (! $company->isEditableByEmployer()) {
            throw new \Exception('This company profile cannot be edited in its current status.');
        }

        try {
            return DB::transaction(function () use ($company, $file, $caption) {
                // Store photo in public storage
                $path = $file->store('employer-photos', 'public');

                // Get the highest display order
                $maxOrder = EmployerCompanyPhoto::where('employer_company_id', $company->id)
                    ->max('display_order') ?? 0;

                $photo = EmployerCompanyPhoto::create([
                    'employer_company_id' => $company->id,
                    'photo_url' => $path,
                    'caption' => $caption,
                    'display_order' => $maxOrder + 1,
                ]);

                Log::info('EmployerCompanyService: photo uploaded', [
                    'company_id' => $company->id,
                    'photo_id' => $photo->id,
                ]);

                return $photo;
            });
        } catch (\Exception $e) {
            Log::error('EmployerCompanyService: uploadPhoto failed', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to upload photo. Please try again.');
        }
    }

    /**
     * Delete company photo.
     */
    public function deletePhoto(User $employer, EmployerCompany $company, string $photoId): bool
    {
        Log::info('EmployerCompanyService: deletePhoto started', [
            'user_id' => $employer->id,
            'company_id' => $company->id,
            'photo_id' => $photoId,
        ]);

        if (! $company->isEditableByEmployer()) {
            throw new \Exception('This company profile cannot be edited in its current status.');
        }

        try {
            return DB::transaction(function () use ($company, $photoId) {
                $photo = EmployerCompanyPhoto::where('employer_company_id', $company->id)
                    ->findOrFail($photoId);

                // Delete file from storage
                if ($photo->photo_url) {
                    Storage::disk('public')->delete($photo->photo_url);
                }

                // Delete record
                $deleted = $photo->delete();

                Log::info('EmployerCompanyService: photo deleted', [
                    'company_id' => $company->id,
                    'photo_id' => $photoId,
                ]);

                return $deleted;
            });
        } catch (\Exception $e) {
            Log::error('EmployerCompanyService: deletePhoto failed', [
                'company_id' => $company->id,
                'photo_id' => $photoId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to delete photo. Please try again.');
        }
    }

    /**
     * Upload company video (max 90 seconds).
     */
    public function uploadVideo(User $employer, EmployerCompany $company, UploadedFile $file): EmployerCompany
    {
        Log::info('EmployerCompanyService: uploadVideo started', [
            'user_id' => $employer->id,
            'company_id' => $company->id,
        ]);

        if (! $company->isEditableByEmployer()) {
            throw new \Exception('This company profile cannot be edited in its current status.');
        }

        try {
            return DB::transaction(function () use ($company, $file) {
                // Delete old video if exists
                if ($company->video_url) {
                    Storage::disk('public')->delete($company->video_url);
                }

                // Store new video in public storage
                $path = $file->store('employer-videos', 'public');
                $company->update(['video_url' => $path]);

                Log::info('EmployerCompanyService: video uploaded', [
                    'company_id' => $company->id,
                ]);

                $this->calculateCompletenessScore($company);

                return $company->fresh();
            });
        } catch (\Exception $e) {
            Log::error('EmployerCompanyService: uploadVideo failed', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to upload video. Please try again.');
        }
    }

    /**
     * Create testimonial.
     */
    public function createTestimonial(User $employer, EmployerCompany $company, array $data, ?UploadedFile $photoFile = null): EmployerCompanyTestimonial
    {
        Log::info('EmployerCompanyService: createTestimonial started', [
            'user_id' => $employer->id,
            'company_id' => $company->id,
        ]);

        if (! $company->isEditableByEmployer()) {
            throw new \Exception('This company profile cannot be edited in its current status.');
        }

        try {
            return DB::transaction(function () use ($company, $data, $photoFile) {
                $photoUrl = null;
                if ($photoFile) {
                    $photoUrl = $photoFile->store('employer-testimonials', 'public');
                }

                // Get the highest display order
                $maxOrder = EmployerCompanyTestimonial::where('employer_company_id', $company->id)
                    ->max('display_order') ?? 0;

                $testimonial = EmployerCompanyTestimonial::create([
                    'employer_company_id' => $company->id,
                    'employee_name' => $data['employee_name'],
                    'employee_title' => $data['employee_title'] ?? null,
                    'testimonial' => $data['testimonial'],
                    'photo_url' => $photoUrl,
                    'display_order' => $maxOrder + 1,
                    'is_featured' => $data['is_featured'] ?? false,
                ]);

                Log::info('EmployerCompanyService: testimonial created', [
                    'company_id' => $company->id,
                    'testimonial_id' => $testimonial->id,
                ]);

                return $testimonial;
            });
        } catch (\Exception $e) {
            Log::error('EmployerCompanyService: createTestimonial failed', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to create testimonial. Please try again.');
        }
    }

    /**
     * Update testimonial.
     */
    public function updateTestimonial(User $employer, EmployerCompany $company, string $testimonialId, array $data, ?UploadedFile $photoFile = null): EmployerCompanyTestimonial
    {
        Log::info('EmployerCompanyService: updateTestimonial started', [
            'user_id' => $employer->id,
            'company_id' => $company->id,
            'testimonial_id' => $testimonialId,
        ]);

        if (! $company->isEditableByEmployer()) {
            throw new \Exception('This company profile cannot be edited in its current status.');
        }

        try {
            return DB::transaction(function () use ($company, $testimonialId, $data, $photoFile) {
                $testimonial = EmployerCompanyTestimonial::where('employer_company_id', $company->id)
                    ->findOrFail($testimonialId);

                $updateData = [
                    'employee_name' => $data['employee_name'] ?? $testimonial->employee_name,
                    'employee_title' => $data['employee_title'] ?? $testimonial->employee_title,
                    'testimonial' => $data['testimonial'] ?? $testimonial->testimonial,
                    'is_featured' => $data['is_featured'] ?? $testimonial->is_featured,
                ];

                if ($photoFile) {
                    // Delete old photo if exists
                    if ($testimonial->photo_url) {
                        Storage::disk('public')->delete($testimonial->photo_url);
                    }
                    $updateData['photo_url'] = $photoFile->store('employer-testimonials', 'public');
                }

                $testimonial->update($updateData);

                Log::info('EmployerCompanyService: testimonial updated', [
                    'company_id' => $company->id,
                    'testimonial_id' => $testimonialId,
                ]);

                return $testimonial->fresh();
            });
        } catch (\Exception $e) {
            Log::error('EmployerCompanyService: updateTestimonial failed', [
                'company_id' => $company->id,
                'testimonial_id' => $testimonialId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to update testimonial. Please try again.');
        }
    }

    /**
     * Delete testimonial.
     */
    public function deleteTestimonial(User $employer, EmployerCompany $company, string $testimonialId): bool
    {
        Log::info('EmployerCompanyService: deleteTestimonial started', [
            'user_id' => $employer->id,
            'company_id' => $company->id,
            'testimonial_id' => $testimonialId,
        ]);

        if (! $company->isEditableByEmployer()) {
            throw new \Exception('This company profile cannot be edited in its current status.');
        }

        try {
            return DB::transaction(function () use ($company, $testimonialId) {
                $testimonial = EmployerCompanyTestimonial::where('employer_company_id', $company->id)
                    ->findOrFail($testimonialId);

                // Delete photo from storage if exists
                if ($testimonial->photo_url) {
                    Storage::disk('public')->delete($testimonial->photo_url);
                }

                // Delete record
                $deleted = $testimonial->delete();

                Log::info('EmployerCompanyService: testimonial deleted', [
                    'company_id' => $company->id,
                    'testimonial_id' => $testimonialId,
                ]);

                return $deleted;
            });
        } catch (\Exception $e) {
            Log::error('EmployerCompanyService: deleteTestimonial failed', [
                'company_id' => $company->id,
                'testimonial_id' => $testimonialId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to delete testimonial. Please try again.');
        }
    }

    /**
     * Admin: Verify company documents.
     */
    public function adminVerifyCompany(User $admin, EmployerCompany $company, bool $verified, ?string $notes = null): EmployerCompany
    {
        Log::info('EmployerCompanyService: adminVerifyCompany started', [
            'admin_user_id' => $admin->id,
            'company_id' => $company->id,
            'verified' => $verified,
        ]);

        if (! $admin->hasRole(UserRoleEnum::ADMIN->value)) {
            throw new \Exception('Unauthorized.');
        }

        try {
            return DB::transaction(function () use ($admin, $company, $verified) {
                $status = $verified
                    ? EmployerCompanyVerificationStatusEnum::VERIFIED->value
                    : EmployerCompanyVerificationStatusEnum::REJECTED->value;

                $company->update([
                    'verification_status' => $status,
                    'verified_at' => $verified ? now() : null,
                    'verified_by_user_id' => $admin->id,
                ]);

                Log::info('EmployerCompanyService: company verification updated', [
                    'company_id' => $company->id,
                    'status' => $status,
                ]);

                return $company->fresh();
            });
        } catch (\Exception $e) {
            Log::error('EmployerCompanyService: adminVerifyCompany failed', [
                'admin_user_id' => $admin->id,
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to update company verification. Please try again.');
        }
    }
}
