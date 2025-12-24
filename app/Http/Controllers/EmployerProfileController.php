<?php

namespace App\Http\Controllers;

use App\Enums\EmployerCompanyStatusEnum;
use App\Enums\UserRoleEnum;
use App\Http\Requests\EmployerCompany\StoreEmployerCompanyRequest;
use App\Http\Requests\EmployerCompany\SubmitEmployerCompanyRequest;
use App\Http\Requests\EmployerCompany\UpdateEmployerCompanyRequest;
use App\Models\EmployerCompany;
use App\Models\Industry;
use App\Services\EmployerCompanyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            return redirect()->route('employer.company.build');
        }

        // Check if wizard is complete, if not redirect to wizard
        $progress = $this->employerCompanyService->getWizardProgress($company);
        $allStepsComplete = true;
        foreach ($progress['steps'] as $stepData) {
            if (! $stepData['completed']) {
                $allStepsComplete = false;
                break;
            }
        }

        if (! $allStepsComplete) {
            return redirect()->route('employer.company.build');
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
        if (! $company) {
            return redirect()->route('employer.company.show')
                ->with('info', 'Please create your company profile first.');
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
            $this->employerCompanyService->updateCompanyByEmployer($user, $company, $request->validated());

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

    /**
     * Show the profile wizard or redirect to current step.
     */
    public function showWizard(): RedirectResponse|View
    {
        $user = Auth::user();
        $company = $user->employerCompany();

        // Create company if it doesn't exist
        if (! $company) {
            try {
                $company = $this->employerCompanyService->createCompanyForEmployer($user, [
                    'legal_name' => '',
                    'status' => EmployerCompanyStatusEnum::DRAFT->value,
                ]);
                Log::info('EmployerProfileController: created company for wizard', [
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                ]);
            } catch (\Exception $e) {
                Log::error('EmployerProfileController: failed to create company for wizard', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);

                return redirect()->route('dashboard')
                    ->with('error', 'Failed to initialize company profile. Please contact support.');
            }
        }

        $progress = $this->employerCompanyService->getWizardProgress($company);

        // Redirect to current step
        return redirect()->route('employer.company.build.step', ['step' => $progress['current_step']]);
    }

    /**
     * Show a specific wizard step.
     */
    public function step(int $step): View|RedirectResponse
    {
        $user = Auth::user();
        $company = $user->employerCompany();
        $industries = Industry::where('is_active', true)->get();

        if (! $company) {
            return redirect()->route('employer.company.build');
        }

        $progress = $this->employerCompanyService->getWizardProgress($company);
        $validSteps = [1, 2, 3, 4];

        // Validate step number
        if (! in_array($step, $validSteps)) {
            return redirect()->route('employer.company.build.step', ['step' => $progress['current_step']]);
        }

        // Don't allow skipping ahead to incomplete steps
        if ($step > $progress['current_step']) {
            $currentStepName = $this->getStepName($progress['current_step']);
            $requirements = $this->getStepRequirements($progress['current_step']);
            $errorMessage = "Please complete Step {$progress['current_step']}: {$currentStepName} before proceeding. {$requirements}";

            return redirect()->route('employer.company.build.step', ['step' => $progress['current_step']])
                ->with('error', $errorMessage);
        }

        $data = [
            'company' => $company,
            'progress' => $progress,
            'current_step' => $step,
            'industries' => $industries,
        ];

        return view('pages.employer.company.wizard', $data);
    }

    /**
     * Get user-friendly step name for a given step number.
     */
    private function getStepName(int $step): string
    {
        return match ($step) {
            1 => 'Basic Company Info',
            2 => 'Contact & Location',
            3 => 'Registration & Verification',
            4 => 'Primary Contact',
            default => 'Unknown Step',
        };
    }

    /**
     * Get step-specific requirements message.
     */
    private function getStepRequirements(int $step): string
    {
        return match ($step) {
            1 => 'Make sure the legal company name is filled.',
            2 => 'Add at least one contact or location field (city, address, phone, email, website, or LinkedIn).',
            3 => 'Add your registration number or TIN.',
            4 => 'Add at least primary contact name or email.',
            default => 'Complete all required fields.',
        };
    }

    /**
     * Save step data.
     */
    public function saveStep(Request $request, int $step): RedirectResponse
    {
        $user = Auth::user();
        $company = $user->employerCompany();

        if (! $company) {
            return redirect()->route('employer.company.build')
                ->with('error', 'Company profile not found. Please contact support.');
        }

        try {
            switch ($step) {
                case 1:
                    $validated = $request->validate([
                        'legal_name' => ['required', 'string', 'max:255'],
                        'trading_name' => ['nullable', 'string', 'max:255'],
                        'industry' => ['nullable', 'string', 'max:255'],
                        'other_industry' => ['nullable', 'string', 'max:255'],
                        'company_size' => ['nullable', 'string', 'max:255'],
                    ]);

                    // If "Others(Please Specify)" is selected, use other_industry value
                    if ($validated['industry'] === 'Others(Please Specify)') {
                        if (empty($validated['other_industry'])) {
                            return back()
                                ->withInput()
                                ->withErrors(['other_industry' => 'Please specify your industry.']);
                        }
                        $validated['industry'] = $validated['other_industry'];
                    }
                    unset($validated['other_industry']);

                    Log::info('EmployerProfileController: saving step 1', [
                        'user_id' => $user->id,
                        'company_id' => $company->id,
                        'industry' => $validated['industry'] ?? null,
                    ]);

                    $this->employerCompanyService->updateCompanyByEmployer($user, $company, $validated);
                    break;

                case 2:
                    $validated = $request->validate([
                        'country' => ['nullable', 'string', 'max:255'],
                        'city' => ['nullable', 'string', 'max:255'],
                        'address' => ['nullable', 'string', 'max:500'],
                        'phone_number' => ['nullable', 'string', 'max:20'],
                        'official_email' => ['nullable', 'email', 'max:255'],
                        'website' => ['nullable', 'url', 'max:255'],
                        'linkedin_url' => ['nullable', 'url', 'max:255'],
                    ]);

                    // Normalize empty strings to null and trim whitespace
                    $fieldsToNormalize = ['city', 'address', 'phone_number', 'official_email', 'website', 'linkedin_url'];
                    foreach ($fieldsToNormalize as $field) {
                        if (isset($validated[$field])) {
                            $trimmed = trim($validated[$field]);
                            $validated[$field] = $trimmed !== '' ? $trimmed : null;
                        } else {
                            $validated[$field] = null;
                        }
                    }

                    // Set default country if not provided, otherwise trim it
                    if (empty($validated['country']) || trim($validated['country']) === '') {
                        $validated['country'] = 'Ghana';
                    } else {
                        $validated['country'] = trim($validated['country']);
                    }

                    Log::info('EmployerProfileController: saving step 2', [
                        'user_id' => $user->id,
                        'company_id' => $company->id,
                        'validated_data' => $validated,
                    ]);

                    $this->employerCompanyService->updateCompanyByEmployer($user, $company, $validated);
                    break;

                case 3:
                    $validated = $request->validate([
                        'registration_number' => ['nullable', 'string', 'max:255'],
                    ]);

                    Log::info('EmployerProfileController: saving step 3', [
                        'user_id' => $user->id,
                        'company_id' => $company->id,
                    ]);

                    $this->employerCompanyService->updateCompanyByEmployer($user, $company, $validated);
                    break;

                case 4:
                    $validated = $request->validate([
                        'primary_contact_name' => ['nullable', 'string', 'max:255'],
                        'primary_contact_title' => ['nullable', 'string', 'max:255'],
                        'primary_contact_email' => ['nullable', 'email', 'max:255'],
                        'primary_contact_phone' => ['nullable', 'string', 'max:20'],
                    ]);

                    Log::info('EmployerProfileController: saving step 4', [
                        'user_id' => $user->id,
                        'company_id' => $company->id,
                    ]);

                    $this->employerCompanyService->updateCompanyByEmployer($user, $company, $validated);
                    break;

                default:
                    return redirect()->route('employer.company.build')
                        ->with('error', 'Invalid step number.');
            }

            // Determine next step
            $progress = $this->employerCompanyService->getWizardProgress($company->fresh());
            $nextStep = $progress['current_step'];

            // If all steps are complete, redirect to completion page
            $allComplete = true;
            foreach ($progress['steps'] as $stepData) {
                if (! $stepData['completed']) {
                    $allComplete = false;
                    break;
                }
            }

            if ($allComplete) {
                return redirect()->route('employer.company.complete')
                    ->with('success', 'Company profile completed successfully!');
            }

            // Redirect to next step
            if ($nextStep > $step) {
                return redirect()->route('employer.company.build.step', ['step' => $nextStep])
                    ->with('success', 'Step '.$step.' saved successfully. Continue to step '.$nextStep.'.');
            }

            return redirect()->route('employer.company.build.step', ['step' => $step])
                ->with('success', 'Step saved successfully.');
        } catch (\Exception $e) {
            Log::error('EmployerProfileController: saveStep failed', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'step' => $step,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show completion page.
     */
    public function complete(): View|RedirectResponse
    {
        $user = Auth::user();
        $company = $user->employerCompany();

        if (! $company) {
            return redirect()->route('employer.company.build');
        }

        $progress = $this->employerCompanyService->getWizardProgress($company);

        return view('pages.employer.company.complete', [
            'company' => $company,
            'progress' => $progress,
        ]);
    }
}
