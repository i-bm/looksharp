<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\EmployerCompanyStatusEnum;
use App\Enums\EmployerCompanyVerificationStatusEnum;
use App\Models\EmployerCompany;
use App\Models\EmployerCompanyMember;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployerCompanyOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_can_create_company_profile_draft(): void
    {
        Role::create(['name' => 'employer', 'guard_name' => 'web']);

        $employer = User::create([
            'email' => 'employer@example.com',
            'user_type' => 'employer',
            'password' => null,
        ]);
        $employer->assignRole('employer');

        $response = $this->actingAs($employer)->post(route('employer.company.store'), [
            'legal_name' => 'Acme Ghana Ltd',
            'country' => 'Ghana',
        ]);

        $response->assertRedirect(route('employer.company.show'));

        $company = EmployerCompany::query()->first();
        $this->assertNotNull($company);
        $this->assertSame('Acme Ghana Ltd', $company->legal_name);
        $this->assertSame(EmployerCompanyStatusEnum::DRAFT->value, $company->status);

        $this->assertDatabaseHas('employer_company_members', [
            'employer_company_id' => $company->id,
            'user_id' => $employer->id,
        ]);
    }

    public function test_employer_can_submit_company_for_review(): void
    {
        Role::create(['name' => 'employer', 'guard_name' => 'web']);

        $employer = User::create([
            'email' => 'employer2@example.com',
            'user_type' => 'employer',
            'password' => null,
        ]);
        $employer->assignRole('employer');

        $company = EmployerCompany::create([
            'created_by_user_id' => $employer->id,
            'legal_name' => 'Beta Co',
            'status' => EmployerCompanyStatusEnum::DRAFT->value,
        ]);

        EmployerCompanyMember::create([
            'employer_company_id' => $company->id,
            'user_id' => $employer->id,
            'role' => 'company_admin',
        ]);

        $response = $this->actingAs($employer)->post(route('employer.company.submit'));

        $response->assertRedirect(route('employer.company.show'));

        $company->refresh();
        $this->assertSame(EmployerCompanyStatusEnum::SUBMITTED->value, $company->status);
        $this->assertNotNull($company->submitted_at);
    }

    public function test_admin_can_approve_submitted_company(): void
    {
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'employer', 'guard_name' => 'web']);

        $admin = User::create([
            'email' => 'admin@example.com',
            'user_type' => 'admin',
            'password' => null,
        ]);
        $admin->assignRole('admin');

        $employer = User::create([
            'email' => 'employer3@example.com',
            'user_type' => 'employer',
            'password' => null,
        ]);
        $employer->assignRole('employer');

        $company = EmployerCompany::create([
            'created_by_user_id' => $employer->id,
            'legal_name' => 'Gamma Co',
            'status' => EmployerCompanyStatusEnum::SUBMITTED->value,
            'submitted_at' => now(),
        ]);

        EmployerCompanyMember::create([
            'employer_company_id' => $company->id,
            'user_id' => $employer->id,
            'role' => 'company_admin',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.employer-companies.approve', ['id' => $company->id]), [
            'notes' => 'Looks good.',
        ]);

        $response->assertRedirect(route('admin.employer-companies.show', ['id' => $company->id]));

        $company->refresh();
        $this->assertSame(EmployerCompanyStatusEnum::APPROVED->value, $company->status);
        $this->assertSame($admin->id, $company->reviewed_by_user_id);
        $this->assertNotNull($company->approved_at);
    }

    public function test_admin_can_mark_company_verification_as_verified(): void
    {
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'employer', 'guard_name' => 'web']);

        $admin = User::create([
            'email' => 'admin2@example.com',
            'user_type' => 'admin',
            'password' => null,
        ]);
        $admin->assignRole('admin');

        $employer = User::create([
            'email' => 'employer4@example.com',
            'user_type' => 'employer',
            'password' => null,
        ]);
        $employer->assignRole('employer');

        $company = EmployerCompany::create([
            'created_by_user_id' => $employer->id,
            'legal_name' => 'Delta Co',
            'status' => EmployerCompanyStatusEnum::SUBMITTED->value,
            'submitted_at' => now(),
            'verification_status' => EmployerCompanyVerificationStatusEnum::PENDING->value,
            'ghana_card_document_url' => 'employer-verification/ghana-cards/test.pdf',
            'business_registration_document_url' => 'employer-verification/business-registration/test.pdf',
        ]);

        EmployerCompanyMember::create([
            'employer_company_id' => $company->id,
            'user_id' => $employer->id,
            'role' => 'company_admin',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.employer-companies.verify', ['id' => $company->id]), [
            'verified' => true,
            'notes' => 'Docs checked.',
        ]);

        $response->assertRedirect(route('admin.employer-companies.show', ['id' => $company->id]));

        $company->refresh();
        $this->assertSame(EmployerCompanyVerificationStatusEnum::VERIFIED->value, $company->verification_status);
        $this->assertSame($admin->id, $company->verified_by_user_id);
        $this->assertNotNull($company->verified_at);
    }

    public function test_employer_cannot_select_subscription_when_company_not_approved(): void
    {
        Role::create(['name' => 'employer', 'guard_name' => 'web']);

        $employer = User::create([
            'email' => 'employer5@example.com',
            'user_type' => 'employer',
            'password' => null,
        ]);
        $employer->assignRole('employer');

        $company = EmployerCompany::create([
            'created_by_user_id' => $employer->id,
            'legal_name' => 'Epsilon Co',
            'status' => EmployerCompanyStatusEnum::SUBMITTED->value,
            'submitted_at' => now(),
        ]);

        EmployerCompanyMember::create([
            'employer_company_id' => $company->id,
            'user_id' => $employer->id,
            'role' => 'company_admin',
        ]);

        $response = $this->actingAs($employer)->get(route('employer.subscription.select'));
        $response->assertRedirect(route('employer.company.show'));
    }

    public function test_employer_cannot_choose_paid_plan_when_company_not_verified(): void
    {
        Role::create(['name' => 'employer', 'guard_name' => 'web']);

        $employer = User::create([
            'email' => 'employer6@example.com',
            'user_type' => 'employer',
            'password' => null,
        ]);
        $employer->assignRole('employer');

        $company = EmployerCompany::create([
            'created_by_user_id' => $employer->id,
            'legal_name' => 'Zeta Co',
            'status' => EmployerCompanyStatusEnum::APPROVED->value,
            'approved_at' => now(),
            'verification_status' => EmployerCompanyVerificationStatusEnum::PENDING->value,
            'primary_contact_email' => 'billing@zeta.test',
        ]);

        EmployerCompanyMember::create([
            'employer_company_id' => $company->id,
            'user_id' => $employer->id,
            'role' => 'company_admin',
        ]);

        $response = $this->actingAs($employer)->post(route('employer.subscription.store'), [
            'tier' => 'starter',
            'billing_cycle' => 'monthly',
        ]);

        $response->assertRedirect(route('employer.company.show'));
    }
}

