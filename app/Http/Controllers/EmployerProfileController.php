<?php

namespace App\Http\Controllers;

use App\Enums\EmployerCompanyStatusEnum;
use App\Enums\UserRoleEnum;
use App\Http\Requests\EmployerCompany\StoreEmployerCompanyRequest;
use App\Http\Requests\EmployerCompany\SubmitEmployerCompanyRequest;
use App\Http\Requests\EmployerCompany\UpdateEmployerCompanyRequest;
use App\Models\EmployerCompany;
use App\Services\EmployerCompanyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class EmployerProfileController extends Controller
{
    public function __construct(private EmployerCompanyService $employerCompanyService)
    {
        $this->middleware('auth')->except('public');
        $this->middleware('role:'.UserRoleEnum::EMPLOYER->value)->except('public');
    }

    /**
     * Show the employer's company profile (private view).
     */
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();

        $company = $user->employerCompany();

        if (! $company) {
            return redirect()->route('employer.company.edit');
        }

        return view('pages.employer.company.show', [
            'company' => $company,
        ]);
    }

    /**
     * Show the company profile edit page.
     */
    public function edit(): View|RedirectResponse
    {
        $user = Auth::user();

        $company = $user->employerCompany();

        // If no company exists, create a draft company automatically
        if (! $company) {
            try {
                $company = $this->employerCompanyService->createCompanyForEmployer($user, [
                    'legal_name' => '',
                    'status' => EmployerCompanyStatusEnum::DRAFT->value,
                ]);

                Log::info('EmployerProfileController: created draft company for edit', [
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                ]);
            } catch (\Exception $e) {
                Log::error('EmployerProfileController: failed to create draft company', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);

                return redirect()->route('dashboard')
                    ->with('error', 'Failed to initialize company profile. Please contact support.');
            }
        }

        if (! $company->isEditableByEmployer()) {
            return redirect()->route('employer.company.show')
                ->with('info', 'Your company profile cannot be edited right now.');
        }

        return view('pages.employer.company.edit', [
            'company' => $company,
        ]);
    }

    /**
     * Update company profile information.
     */
    public function update(UpdateEmployerCompanyRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $company = $user->employerCompany();

        if (! $company) {
            return redirect()->route('employer.company.show')
                ->with('error', 'Company profile not found. Please create one first.');
        }

        try {
            $updatedCompany = $this->employerCompanyService->updateCompanyByEmployer($user, $company, $request->validated());

            // Clear session flag if company profile reaches 70%+ completion
            if ($updatedCompany->profile_completeness_score >= 70) {
                $request->session()->forget('profile_completion_prompted');
            }

            return redirect()->route('employer.company.show')
                ->with('success', 'Company profile updated successfully.');
        } catch (\Exception $e) {
            Log::error('EmployerProfileController: update failed', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Create a company profile (draft).
     */
    public function store(StoreEmployerCompanyRequest $request): RedirectResponse
    {
        $user = Auth::user();

        try {
            $company = $this->employerCompanyService->createCompanyForEmployer($user, $request->validated());

            return redirect()->route('employer.company.show')
                ->with('success', 'Company profile created. Please review and submit for approval.');
        } catch (\Exception $e) {
            Log::error('EmployerProfileController: store failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Submit company profile for admin review.
     */
    public function submit(SubmitEmployerCompanyRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $company = $user->employerCompany();

        if (! $company) {
            return redirect()->route('employer.company.show')
                ->with('error', 'Company profile not found. Please create one first.');
        }

        try {
            $this->employerCompanyService->submitCompanyForReview($user, $company);

            return redirect()->route('employer.company.show')
                ->with('success', 'Company profile submitted for review.');
        } catch (\Exception $e) {
            Log::error('EmployerProfileController: submit failed', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('employer.company.show')
                ->with('error', $e->getMessage());
        }

    }

    /**
     * Show public company profile (viewable by anyone).
     */
    public function public(string $id): View
    {
        /** @var EmployerCompany $company */
        $company = EmployerCompany::query()
            ->where('id', $id)
            ->where('status', EmployerCompanyStatusEnum::APPROVED->value)
            ->firstOrFail();

        return view('pages.employer.company.public', [
            'company' => $company,
        ]);
    }
}
