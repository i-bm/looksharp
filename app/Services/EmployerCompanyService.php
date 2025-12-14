<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EmployerCompanyMemberRoleEnum;
use App\Enums\EmployerCompanyStatusEnum;
use App\Enums\UserRoleEnum;
use App\Models\EmployerCompany;
use App\Models\EmployerCompanyMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployerCompanyService
{
    public function __construct(private NotificationService $notificationService)
    {
    }

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
                $company->update([
                    'legal_name' => (string) ($data['legal_name'] ?? $company->legal_name),
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
                    ]
                );

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
}

