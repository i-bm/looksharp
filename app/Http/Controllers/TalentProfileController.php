<?php

namespace App\Http\Controllers;

use App\Enums\AvailabilityEnum;
use App\Enums\PreferredLocationEnum;
use App\Enums\UserRoleEnum;
use App\Models\Institution;
use App\Models\TalentCertification;
use App\Models\TalentEducation;
use App\Models\TalentLanguage;
use App\Models\TalentProfile;
use App\Models\TalentSkill;
use App\Models\TalentWorkHistory;
use App\Services\ProfileService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TalentProfileController extends Controller
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->middleware('auth')->except('public');
        $this->middleware('role:'.UserRoleEnum::TALENT->value)->except('public');
        $this->profileService = $profileService;
    }

    /**
     * Show the profile wizard or redirect to current step.
     */
    public function showWizard(): RedirectResponse|View
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found. Please contact support.');
        }

        $progress = $this->profileService->getWizardProgress($profile);

        // Redirect to current step
        return redirect()->route('talent.profile.build.step', ['step' => $progress['current_step']]);
    }

    /**
     * Show a specific wizard step.
     */
    public function step(int $step): View|RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found. Please contact support.');
        }

        $progress = $this->profileService->getWizardProgress($profile);
        $validSteps = [1, 2, 3, 4];

        // Validate step number
        if (! in_array($step, $validSteps)) {
            return redirect()->route('talent.profile.build.step', ['step' => $progress['current_step']]);
        }

        // Don't allow skipping ahead to incomplete steps
        if ($step > $progress['current_step']) {
            return redirect()->route('talent.profile.build.step', ['step' => $progress['current_step']])
                ->with('error', 'Please complete the previous steps first.');
        }

        $data = [
            'profile' => $profile,
            'progress' => $progress,
            'current_step' => $step,
        ];

        // Add step-specific data
        switch ($step) {
            case 1:
                // Basic info - no additional data needed
                break;
            case 2:
                $data['education'] = $profile->education()->with('institution')->get();
                $data['institutions'] = Institution::where('is_active', true)->orderBy('name')->get();
                break;
            case 3:
                $data['skills'] = $profile->skills;
                break;
            case 4:
                // Verification - no additional data needed
                break;
        }

        return view('pages.profile.wizard', $data);
    }

    /**
     * Save step data.
     */
    public function saveStep(Request $request, int $step): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found. Please contact support.');
        }

        try {
            switch ($step) {
                case 1:
                    // Validate individual date fields
                    $validated = $request->validate([
                        'first_name' => ['required', 'string', 'max:255'],
                        'last_name' => ['required', 'string', 'max:255'],
                        'dob_day' => ['required', 'integer', 'min:1', 'max:31'],
                        'dob_month' => ['required', 'integer', 'min:1', 'max:12'],
                        'dob_year' => ['required', 'integer', 'min:'.(date('Y') - 100), 'max:'.(date('Y') - 13)],
                        'gender' => ['required', Rule::in(['male', 'female', 'other', 'prefer_not_to_say'])],
                        'location' => ['required', 'string', 'max:255'],
                        'bio' => ['nullable', 'string', 'max:1000'],
                    ]);

                    // Validate and combine day, month, year into date_of_birth using helper
                    $dateValidation = validateDateComponents(
                        $validated['dob_day'],
                        $validated['dob_month'],
                        $validated['dob_year']
                    );

                    if (! $dateValidation['valid']) {
                        return back()
                            ->withInput()
                            ->withErrors(['date_of_birth' => $dateValidation['error']]);
                    }

                    // Validate that the date is before today and user is at least 13 years old
                    $dateOfBirth = Carbon::createFromFormat('Y-m-d', $dateValidation['date']);
                    if ($dateOfBirth->isFuture() || $dateOfBirth->age < 13) {
                        return back()
                            ->withInput()
                            ->withErrors(['date_of_birth' => 'Date of birth must be in the past and you must be at least 13 years old.']);
                    }

                    // Add combined date to validated data
                    $validated['date_of_birth'] = $dateValidation['date'];
                    unset($validated['dob_day'], $validated['dob_month'], $validated['dob_year']);

                    $this->profileService->saveBasicInfo($profile, $validated);
                    break;

                case 2:
                    // Validate individual date fields
                    $validated = $request->validate([
                        'institution_id' => ['nullable', 'uuid', 'exists:institutions,id'],
                        'degree_type' => ['required', Rule::enum(\App\Enums\DegreeTypeEnum::class)],
                        'field_of_study' => ['required', 'string', 'max:255'],
                        'start_date_day' => ['required', 'integer', 'min:1', 'max:31'],
                        'start_date_month' => ['required', 'integer', 'min:1', 'max:12'],
                        'start_date_year' => ['required', 'integer', 'min:'.(date('Y') - 50), 'max:'.(date('Y') + 10)],
                        'end_date_day' => ['nullable', 'integer', 'min:1', 'max:31'],
                        'end_date_month' => ['nullable', 'integer', 'min:1', 'max:12'],
                        'end_date_year' => ['nullable', 'integer', 'min:'.(date('Y') - 50), 'max:'.(date('Y') + 10)],
                        'is_current' => ['boolean'],
                        'gpa' => ['nullable', 'numeric', 'min:0', 'max:5'],
                        'is_primary' => ['boolean'],
                    ]);

                    // Validate and combine start_date
                    $startDateValidation = validateDateComponents(
                        $validated['start_date_day'],
                        $validated['start_date_month'],
                        $validated['start_date_year']
                    );

                    if (! $startDateValidation['valid']) {
                        return back()
                            ->withInput()
                            ->withErrors(['start_date' => $startDateValidation['error']]);
                    }

                    $validated['start_date'] = $startDateValidation['date'];
                    unset($validated['start_date_day'], $validated['start_date_month'], $validated['start_date_year']);

                    // Validate and combine end_date if provided and not currently enrolled
                    if (! ($validated['is_current'] ?? false)) {
                        if (! empty($validated['end_date_day']) && ! empty($validated['end_date_month']) && ! empty($validated['end_date_year'])) {
                            $endDateValidation = validateDateComponents(
                                $validated['end_date_day'],
                                $validated['end_date_month'],
                                $validated['end_date_year']
                            );

                            if (! $endDateValidation['valid']) {
                                return back()
                                    ->withInput()
                                    ->withErrors(['end_date' => $endDateValidation['error']]);
                            }

                            // Validate that end_date is after start_date
                            $startDate = Carbon::createFromFormat('Y-m-d', $validated['start_date']);
                            $endDate = Carbon::createFromFormat('Y-m-d', $endDateValidation['date']);

                            if ($endDate->lte($startDate)) {
                                return back()
                                    ->withInput()
                                    ->withErrors(['end_date' => 'End date must be after start date.']);
                            }

                            $validated['end_date'] = $endDateValidation['date'];
                        }
                        unset($validated['end_date_day'], $validated['end_date_month'], $validated['end_date_year']);
                    } else {
                        // Clear end_date if currently enrolled
                        $validated['end_date'] = null;
                        unset($validated['end_date_day'], $validated['end_date_month'], $validated['end_date_year']);
                    }

                    $this->profileService->saveEducation($profile, $validated);
                    break;

                case 3:
                    $validated = $request->validate([
                        'skill_name' => ['required', 'string', 'max:255'],
                        'proficiency_level' => ['required', Rule::enum(\App\Enums\ProficiencyLevelEnum::class)],
                    ]);
                    $this->profileService->saveSkill($profile, $validated);
                    break;

                case 4:
                    $validated = $request->validate([
                        'verification_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                        'verification_type' => ['required', Rule::in(['ghana_card', 'student_id', 'passport'])],
                    ]);
                    $this->profileService->uploadVerificationDocument(
                        $profile,
                        $validated['verification_document'],
                        $validated['verification_type']
                    );
                    break;

                default:
                    return redirect()->route('talent.profile.build')
                        ->with('error', 'Invalid step.');
            }

            $progress = $this->profileService->getWizardProgress($profile);

            // Check if all steps are completed
            $allStepsComplete = true;
            foreach ($progress['steps'] as $stepData) {
                if (! $stepData['completed']) {
                    $allStepsComplete = false;
                    break;
                }
            }

            // If all steps complete, redirect to completion page
            if ($allStepsComplete) {
                return redirect()->route('talent.profile.show')
                    ->with('success', 'Profile completed successfully!');
            }

            // Otherwise, go to next step
            return redirect()->route('talent.profile.build.step', ['step' => $step + 1])
                ->with('success', 'Step saved successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Upload profile photo (AJAX).
     */
    public function uploadPhoto(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }

        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], // 2MB max
        ]);

        try {
            $this->profileService->uploadProfilePhoto($profile, $request->file('photo'));

            return response()->json([
                'success' => true,
                'message' => 'Profile photo uploaded successfully.',
                'photo_url' => $profile->fresh()->profile_photo ? asset('storage/'.$profile->fresh()->profile_photo) : null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Upload resume (AJAX).
     */
    public function uploadResume(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }

        $request->validate([
            'resume' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'], // 5MB max
        ]);

        try {
            $this->profileService->uploadResume($profile, $request->file('resume'));

            return response()->json([
                'success' => true,
                'message' => 'Resume uploaded successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove education record.
     */
    public function removeEducation(string $id): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found.');
        }

        $education = TalentEducation::where('id', $id)
            ->where('talent_id', $profile->id)
            ->firstOrFail();

        try {
            $this->profileService->deleteEducation($education);

            return redirect()->route('talent.profile.build.step', ['step' => 2])
                ->with('success', 'Education record removed successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove skill record.
     */
    public function removeSkill(string $id): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found.');
        }

        $skill = TalentSkill::where('id', $id)
            ->where('talent_id', $profile->id)
            ->firstOrFail();

        try {
            $this->profileService->deleteSkill($skill);

            return redirect()->route('talent.profile.build.step', ['step' => 3])
                ->with('success', 'Skill removed successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show profile completion page.
     */
    public function complete(): View|RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found.');
        }

        $progress = $this->profileService->getWizardProgress($profile);

        return view('pages.profile.complete', [
            'profile' => $profile,
            'progress' => $progress,
        ]);
    }

    /**
     * Show the user's own profile (private view).
     */
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found. Please contact support.');
        }

        $profile->load(['education.institution', 'skills', 'workHistory', 'languages', 'certifications']);

        return view('pages.profile.show', [
            'profile' => $profile,
            'isOwner' => true,
            'isPublic' => false,
        ]);
    }

    /**
     * Show the profile edit page.
     */
    public function edit(): View|RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found. Please contact support.');
        }

        $profile->load(['education.institution', 'skills', 'workHistory', 'languages', 'certifications']);
        $institutions = Institution::where('is_active', true)->orderBy('name')->get();

        return view('pages.profile.edit', [
            'profile' => $profile,
            'institutions' => $institutions,
        ]);
    }

    /**
     * Update profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found.');
        }

        try {
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'dob_day' => ['nullable', 'integer', 'min:1', 'max:31'],
                'dob_month' => ['nullable', 'integer', 'min:1', 'max:12'],
                'dob_year' => ['nullable', 'integer', 'min:'.(date('Y') - 100), 'max:'.(date('Y') - 13)],
                'gender' => ['nullable', Rule::in(['male', 'female', 'other', 'prefer_not_to_say'])],
                'location' => ['nullable', 'string', 'max:255'],
                'bio' => ['nullable', 'string', 'max:1000'],
                'video_introduction' => ['nullable', 'url', 'max:500'],
                'nss_status' => ['nullable', 'string', 'max:255'],
                'nss_posting_location' => ['nullable', 'string', 'max:255'],
                'nss_posting_number' => ['nullable', 'string', 'max:255'],
                // Additional Details
                'fun_fact' => ['nullable', 'string', 'max:1000'],
                'passion' => ['nullable', 'string', 'max:1000'],
                'gigs_freelance' => ['nullable', 'string', 'max:1000'],
                'leadership' => ['nullable', 'string', 'max:1000'],
                'volunteer' => ['nullable', 'string', 'max:1000'],
                'hobbies' => ['nullable', 'string', 'max:1000'],
                // Portfolio & Social Links
                'github_url' => ['nullable', 'url', 'max:500'],
                'behance_url' => ['nullable', 'url', 'max:500'],
                'portfolio_url' => ['nullable', 'url', 'max:500'],
                'linkedin_url' => ['nullable', 'url', 'max:500'],
                'twitter_url' => ['nullable', 'url', 'max:500'],
                // Work Preferences
                'availability' => ['nullable', Rule::enum(AvailabilityEnum::class)],
                'availability_details' => ['nullable', 'string', 'max:500'],
                'preferred_location' => ['nullable', Rule::enum(PreferredLocationEnum::class)],
                'salary_expectations' => ['nullable', 'numeric', 'min:0'],
            ]);

            // Handle date of birth if provided
            if (isset($validated['dob_day']) && isset($validated['dob_month']) && isset($validated['dob_year'])) {
                $dateValidation = validateDateComponents(
                    $validated['dob_day'],
                    $validated['dob_month'],
                    $validated['dob_year']
                );

                if (! $dateValidation['valid']) {
                    return back()
                        ->withInput()
                        ->withErrors(['date_of_birth' => $dateValidation['error']]);
                }

                $dateOfBirth = Carbon::createFromFormat('Y-m-d', $dateValidation['date']);
                if ($dateOfBirth->isFuture() || $dateOfBirth->age < 13) {
                    return back()
                        ->withInput()
                        ->withErrors(['date_of_birth' => 'Date of birth must be in the past and you must be at least 13 years old.']);
                }

                $validated['date_of_birth'] = $dateValidation['date'];
            }

            unset($validated['dob_day'], $validated['dob_month'], $validated['dob_year']);

            $this->profileService->updateProfile($profile, $validated);

            return redirect()->route('talent.profile.show')
                ->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Add education record from edit page.
     */
    public function addEducation(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found.');
        }

        try {
            $validated = $request->validate([
                'institution_id' => ['nullable', 'uuid', 'exists:institutions,id'],
                'degree_type' => ['required', Rule::enum(\App\Enums\DegreeTypeEnum::class)],
                'field_of_study' => ['required', 'string', 'max:255'],
                'start_date_day' => ['required', 'integer', 'min:1', 'max:31'],
                'start_date_month' => ['required', 'integer', 'min:1', 'max:12'],
                'start_date_year' => ['required', 'integer', 'min:'.(date('Y') - 50), 'max:'.(date('Y') + 10)],
                'end_date_day' => ['nullable', 'integer', 'min:1', 'max:31'],
                'end_date_month' => ['nullable', 'integer', 'min:1', 'max:12'],
                'end_date_year' => ['nullable', 'integer', 'min:'.(date('Y') - 50), 'max:'.(date('Y') + 10)],
                'is_current' => ['boolean'],
                'gpa' => ['nullable', 'numeric', 'min:0', 'max:5'],
                'is_primary' => ['boolean'],
            ]);

            // Validate and combine start_date
            $startDateValidation = validateDateComponents(
                $validated['start_date_day'],
                $validated['start_date_month'],
                $validated['start_date_year']
            );

            if (! $startDateValidation['valid']) {
                return back()
                    ->withInput()
                    ->withErrors(['start_date' => $startDateValidation['error']]);
            }

            $validated['start_date'] = $startDateValidation['date'];
            unset($validated['start_date_day'], $validated['start_date_month'], $validated['start_date_year']);

            // Validate and combine end_date if provided and not currently enrolled
            if (! ($validated['is_current'] ?? false)) {
                if (! empty($validated['end_date_day']) && ! empty($validated['end_date_month']) && ! empty($validated['end_date_year'])) {
                    $endDateValidation = validateDateComponents(
                        $validated['end_date_day'],
                        $validated['end_date_month'],
                        $validated['end_date_year']
                    );

                    if (! $endDateValidation['valid']) {
                        return back()
                            ->withInput()
                            ->withErrors(['end_date' => $endDateValidation['error']]);
                    }

                    $startDate = Carbon::createFromFormat('Y-m-d', $validated['start_date']);
                    $endDate = Carbon::createFromFormat('Y-m-d', $endDateValidation['date']);

                    if ($endDate->lte($startDate)) {
                        return back()
                            ->withInput()
                            ->withErrors(['end_date' => 'End date must be after start date.']);
                    }

                    $validated['end_date'] = $endDateValidation['date'];
                }
                unset($validated['end_date_day'], $validated['end_date_month'], $validated['end_date_year']);
            } else {
                $validated['end_date'] = null;
                unset($validated['end_date_day'], $validated['end_date_month'], $validated['end_date_year']);
            }

            $this->profileService->saveEducation($profile, $validated);

            return redirect()->route('talent.profile.edit')
                ->with('success', 'Education record added successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Add skill record from edit page.
     */
    public function addSkill(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found.');
        }

        try {
            $validated = $request->validate([
                'skill_name' => ['required', 'string', 'max:255'],
                'proficiency_level' => ['required', Rule::enum(\App\Enums\ProficiencyLevelEnum::class)],
            ]);

            $this->profileService->saveSkill($profile, $validated);

            return redirect()->route('talent.profile.edit')
                ->with('success', 'Skill added successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show public profile (viewable by anyone).
     */
    public function public(string $id): View
    {
        $profile = TalentProfile::with(['user', 'education.institution', 'skills', 'workHistory', 'languages', 'certifications'])
            ->where('id', $id)
            ->firstOrFail();

        // Check if viewing own profile
        $isOwner = Auth::check() && Auth::id() === $profile->user_id;

        return view('pages.profile.show', [
            'profile' => $profile,
            'isOwner' => $isOwner,
            'isPublic' => true,
        ]);
    }

    /**
     * Add work history record from edit page.
     */
    public function addWorkHistory(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found.');
        }

        try {
            $validated = $request->validate([
                'company' => ['required', 'string', 'max:255'],
                'position' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string', 'max:2000'],
                'location' => ['nullable', 'string', 'max:255'],
                'start_date_day' => ['required', 'integer', 'min:1', 'max:31'],
                'start_date_month' => ['required', 'integer', 'min:1', 'max:12'],
                'start_date_year' => ['required', 'integer', 'min:'.(date('Y') - 50), 'max:'.(date('Y') + 10)],
                'end_date_day' => ['nullable', 'integer', 'min:1', 'max:31'],
                'end_date_month' => ['nullable', 'integer', 'min:1', 'max:12'],
                'end_date_year' => ['nullable', 'integer', 'min:'.(date('Y') - 50), 'max:'.(date('Y') + 10)],
                'is_current' => ['boolean'],
            ]);

            // Validate and combine start_date
            $startDateValidation = validateDateComponents(
                $validated['start_date_day'],
                $validated['start_date_month'],
                $validated['start_date_year']
            );

            if (! $startDateValidation['valid']) {
                return back()
                    ->withInput()
                    ->withErrors(['start_date' => $startDateValidation['error']]);
            }

            $validated['start_date'] = $startDateValidation['date'];
            unset($validated['start_date_day'], $validated['start_date_month'], $validated['start_date_year']);

            // Validate and combine end_date if provided and not current
            if (! ($validated['is_current'] ?? false)) {
                if (! empty($validated['end_date_day']) && ! empty($validated['end_date_month']) && ! empty($validated['end_date_year'])) {
                    $endDateValidation = validateDateComponents(
                        $validated['end_date_day'],
                        $validated['end_date_month'],
                        $validated['end_date_year']
                    );

                    if (! $endDateValidation['valid']) {
                        return back()
                            ->withInput()
                            ->withErrors(['end_date' => $endDateValidation['error']]);
                    }

                    $startDate = Carbon::createFromFormat('Y-m-d', $validated['start_date']);
                    $endDate = Carbon::createFromFormat('Y-m-d', $endDateValidation['date']);

                    if ($endDate->lte($startDate)) {
                        return back()
                            ->withInput()
                            ->withErrors(['end_date' => 'End date must be after start date.']);
                    }

                    $validated['end_date'] = $endDateValidation['date'];
                }
                unset($validated['end_date_day'], $validated['end_date_month'], $validated['end_date_year']);
            } else {
                $validated['end_date'] = null;
                unset($validated['end_date_day'], $validated['end_date_month'], $validated['end_date_year']);
            }

            $this->profileService->saveWorkHistory($profile, $validated);

            return redirect()->route('talent.profile.edit')
                ->with('success', 'Work history added successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove work history record.
     */
    public function removeWorkHistory(string $id): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found.');
        }

        $workHistory = TalentWorkHistory::where('id', $id)
            ->where('talent_id', $profile->id)
            ->firstOrFail();

        try {
            $this->profileService->deleteWorkHistory($workHistory);

            return redirect()->route('talent.profile.edit')
                ->with('success', 'Work history removed successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Add language record from edit page.
     */
    public function addLanguage(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found.');
        }

        try {
            $validated = $request->validate([
                'language_name' => ['required', 'string', 'max:255'],
                'proficiency_level' => ['required', Rule::enum(\App\Enums\ProficiencyLevelEnum::class)],
            ]);

            $this->profileService->saveLanguage($profile, $validated);

            return redirect()->route('talent.profile.edit')
                ->with('success', 'Language added successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove language record.
     */
    public function removeLanguage(string $id): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found.');
        }

        $language = TalentLanguage::where('id', $id)
            ->where('talent_id', $profile->id)
            ->firstOrFail();

        try {
            $this->profileService->deleteLanguage($language);

            return redirect()->route('talent.profile.edit')
                ->with('success', 'Language removed successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Add certification record from edit page.
     */
    public function addCertification(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found.');
        }

        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'issuer' => ['required', 'string', 'max:255'],
                'date_obtained_day' => ['required', 'integer', 'min:1', 'max:31'],
                'date_obtained_month' => ['required', 'integer', 'min:1', 'max:12'],
                'date_obtained_year' => ['required', 'integer', 'min:'.(date('Y') - 50), 'max:'.date('Y')],
                'expiration_date_day' => ['nullable', 'integer', 'min:1', 'max:31'],
                'expiration_date_month' => ['nullable', 'integer', 'min:1', 'max:12'],
                'expiration_date_year' => ['nullable', 'integer', 'min:'.date('Y'), 'max:'.(date('Y') + 50)],
                'credential_url' => ['nullable', 'url', 'max:500'],
            ]);

            // Validate and combine date_obtained
            $dateObtainedValidation = validateDateComponents(
                $validated['date_obtained_day'],
                $validated['date_obtained_month'],
                $validated['date_obtained_year']
            );

            if (! $dateObtainedValidation['valid']) {
                return back()
                    ->withInput()
                    ->withErrors(['date_obtained' => $dateObtainedValidation['error']]);
            }

            $validated['date_obtained'] = $dateObtainedValidation['date'];
            unset($validated['date_obtained_day'], $validated['date_obtained_month'], $validated['date_obtained_year']);

            // Validate and combine expiration_date if provided
            if (! empty($validated['expiration_date_day']) && ! empty($validated['expiration_date_month']) && ! empty($validated['expiration_date_year'])) {
                $expirationDateValidation = validateDateComponents(
                    $validated['expiration_date_day'],
                    $validated['expiration_date_month'],
                    $validated['expiration_date_year']
                );

                if (! $expirationDateValidation['valid']) {
                    return back()
                        ->withInput()
                        ->withErrors(['expiration_date' => $expirationDateValidation['error']]);
                }

                $dateObtained = Carbon::createFromFormat('Y-m-d', $validated['date_obtained']);
                $expirationDate = Carbon::createFromFormat('Y-m-d', $expirationDateValidation['date']);

                if ($expirationDate->lte($dateObtained)) {
                    return back()
                        ->withInput()
                        ->withErrors(['expiration_date' => 'Expiration date must be after date obtained.']);
                }

                $validated['expiration_date'] = $expirationDateValidation['date'];
            }
            unset($validated['expiration_date_day'], $validated['expiration_date_month'], $validated['expiration_date_year']);

            $this->profileService->saveCertification($profile, $validated);

            return redirect()->route('talent.profile.edit')
                ->with('success', 'Certification added successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove certification record.
     */
    public function removeCertification(string $id): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found.');
        }

        $certification = TalentCertification::where('id', $id)
            ->where('talent_id', $profile->id)
            ->firstOrFail();

        try {
            $this->profileService->deleteCertification($certification);

            return redirect()->route('talent.profile.edit')
                ->with('success', 'Certification removed successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
