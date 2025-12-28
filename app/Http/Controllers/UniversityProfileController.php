<?php

namespace App\Http\Controllers;

use App\Enums\InstitutionPartnershipTierEnum;
use App\Enums\InstitutionTypeEnum;
use App\Enums\UserRoleEnum;
use App\Models\Institution;
use App\Services\UniversityOnboardingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UniversityProfileController extends Controller
{
    public function __construct(private UniversityOnboardingService $universityOnboardingService)
    {
        $this->middleware('auth')->except('public');
        $this->middleware('role:'.UserRoleEnum::UNIVERSITY->value)->except('public');
    }

    /**
     * Show the university's profile (private view).
     */
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();

        try {
            $admin = $this->universityOnboardingService->getOrCreateUniversityAdmin($user)->load('institution');
            $wizardProgress = $this->universityOnboardingService->getWizardProgress($admin);

            if (! $admin->institution_id) {
                return redirect()->route('university.profile.edit')
                    ->with('info', 'Please complete your university profile to get started.');
            }

            return view('pages.university.profile.show', [
                'admin' => $admin,
                'institution' => $admin->institution,
                'wizardProgress' => $wizardProgress,
            ]);
        } catch (\Exception $e) {
            Log::error('UniversityProfileController: show failed', [
                'user_id' => $user?->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('dashboard')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the university profile edit page.
     */
    public function edit(): View|RedirectResponse
    {
        $user = Auth::user();

        try {
            $admin = $this->universityOnboardingService->getOrCreateUniversityAdmin($user)->load('institution');

            $institutions = Institution::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            return view('pages.university.profile.edit', [
                'admin' => $admin,
                'institution' => $admin->institution,
                'institutions' => $institutions,
                'institutionTypes' => InstitutionTypeEnum::cases(),
                'partnershipTiers' => InstitutionPartnershipTierEnum::cases(),
                'wizardProgress' => $this->universityOnboardingService->getWizardProgress($admin),
            ]);
        } catch (\Exception $e) {
            Log::error('UniversityProfileController: edit failed', [
                'user_id' => $user?->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('dashboard')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update university profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            // Admin fields
            'admin_name' => ['nullable', 'string', 'max:255'],
            'admin_role' => ['required', 'string', 'max:255'],
            'admin_email' => ['nullable', 'email', 'max:255'],
            'admin_phone' => ['nullable', 'string', 'max:30'],

            // Institution selection / creation
            'institution_id' => ['nullable', 'uuid', 'exists:institutions,id', 'required_without:institution_name'],
            'institution_name' => ['nullable', 'string', 'max:255', 'required_without:institution_id'],

            // Institution details (optional; can update existing too)
            'institution_type' => ['nullable', Rule::enum(InstitutionTypeEnum::class)],
            'institution_location' => ['nullable', 'string', 'max:255'],
            'institution_website' => ['nullable', 'url', 'max:255'],
            'institution_email' => ['nullable', 'email', 'max:255'],
            'institution_phone' => ['nullable', 'string', 'max:30'],
            'institution_city' => ['nullable', 'string', 'max:255'],
            'institution_state' => ['nullable', 'string', 'max:255'],
            'institution_country' => ['nullable', 'string', 'max:255'],
            'student_email_domain' => ['nullable', 'string', 'max:255'],
            'partnership_tier' => ['nullable', Rule::enum(InstitutionPartnershipTierEnum::class)],

            // Files
            'institution_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        try {
            $admin = $this->universityOnboardingService->getOrCreateUniversityAdmin($user)->load('institution');

            $updated = $this->universityOnboardingService->updateUniversityProfile($user, $admin, $validated);

            if ($request->hasFile('institution_logo')) {
                $this->universityOnboardingService->uploadInstitutionLogo($user, $updated, $request->file('institution_logo'));
            }

            if (($updated->profile_completeness_score ?? 0) >= 70) {
                $request->session()->forget('profile_completion_prompted');
            }

            return redirect()->route('university.profile.show')
                ->with('success', 'University profile updated successfully.');
        } catch (\Exception $e) {
            Log::error('UniversityProfileController: update failed', [
                'user_id' => $user?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show public university profile (viewable by anyone).
     */
    public function public(string $id): View
    {
        /** @var Institution $institution */
        $institution = Institution::query()
            ->where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();

        return view('pages.university.profile.public', [
            'institution' => $institution,
        ]);
    }
}

