<?php

namespace App\Http\Controllers;

use App\Enums\AvailabilityEnum;
use App\Enums\PreferredLocationEnum;
use App\Enums\UserRoleEnum;
use App\Models\Institution;
use App\Models\TalentCertification;
use App\Models\TalentEducation;
use App\Models\TalentGigsFreelance;
use App\Models\TalentLanguage;
use App\Models\TalentLeadershipExperience;
use App\Models\TalentProfile;
use App\Models\TalentSkill;
use App\Models\TalentVolunteerExperience;
use App\Models\TalentWorkHistory;
use App\Services\ProfileService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
                // Log::info('All steps complete, updating profile building step completed', ['profile' => $profile]);

                $profile->is_profile_building_step_completed = 1;
                $updatedProfile = $profile->save();

                Log::info('Profile building step completed updated', ['profile' => $updatedProfile]);

                return redirect()->route('talent.profile.show')
                    ->with('success', 'Profile is almost complete! Please complete the remaining steps to get your profile verified.');
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
            return response()->json(['success' => false, 'error' => 'Profile not found.'], 404);
        }

        try {
            $validated = $request->validate([
                'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], // 2MB max
            ]);

            $this->profileService->uploadProfilePhoto($profile, $request->file('photo'));

            return response()->json([
                'success' => true,
                'message' => 'Profile photo uploaded successfully.',
                'photo_url' => $profile->fresh()->profile_photo ? asset('storage/'.$profile->fresh()->profile_photo) : null,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload resume (AJAX).
     */
    /**
     * Update video introduction (AJAX).
     */
    public function updateVideoIntroduction(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        try {
            $validated = $request->validate([
                'video_introduction' => ['nullable', 'url', 'max:500'],
            ]);

            // Validate that it's a YouTube or Vimeo URL if provided
            if (! empty($validated['video_introduction'])) {
                $url = $validated['video_introduction'];
                $youtubePattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
                $vimeoPattern = '/(?:vimeo\.com\/|player\.vimeo\.com\/video\/)(\d+)/';
                $embedPattern = '/(youtube\.com\/embed|player\.vimeo\.com\/video)/';

                if (! preg_match($youtubePattern, $url) && ! preg_match($vimeoPattern, $url) && ! preg_match($embedPattern, $url)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please provide a valid YouTube or Vimeo URL.',
                    ], 422);
                }
            }

            $this->profileService->updateVideoIntroduction($profile, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Video introduction updated successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

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

        $profile->load(['education.institution', 'skills', 'workHistory', 'languages', 'certifications', 'volunteerExperiences', 'leadershipExperiences', 'gigsFreelance', 'careerInterestAreas']);
        $institutions = Institution::where('is_active', true)->orderBy('name')->get();
        $careerInterestAreas = \App\Models\CareerInterestArea::active()
            ->parents()
            ->with(['children' => function ($query) {
                $query->active()->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        return view('pages.profile.show', [
            'profile' => $profile,
            'isOwner' => true,
            'isPublic' => false,
            'institutions' => $institutions,
            'careerInterestAreas' => $careerInterestAreas,
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

        $profile->load(['education.institution', 'skills', 'workHistory', 'languages', 'certifications', 'volunteerExperiences', 'leadershipExperiences', 'gigsFreelance']);
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
    public function public(string $slug): View
    {
        // Try to find by public_url first, then fall back to UUID
        $profile = TalentProfile::with(['user', 'education.institution', 'skills', 'workHistory', 'languages', 'certifications', 'volunteerExperiences', 'leadershipExperiences', 'gigsFreelance', 'careerInterestAreas'])
            ->where(function ($query) use ($slug) {
                $query->where('public_url', $slug)
                    ->orWhere('id', $slug);
            })
            ->firstOrFail();

        // Check if viewing own profile
        $isOwner = Auth::check() && Auth::id() === $profile->user_id;

        $data = [
            'profile' => $profile,
            'isOwner' => $isOwner,
            'isPublic' => true,
        ];

        // Only load these if user is the owner (needed for modals)
        if ($isOwner) {
            $data['institutions'] = Institution::where('is_active', true)->orderBy('name')->get();
            $data['careerInterestAreas'] = \App\Models\CareerInterestArea::active()
                ->parents()
                ->with(['children' => function ($query) {
                    $query->active()->orderBy('order');
                }])
                ->orderBy('order')
                ->get();
        }

        return view('pages.profile.show', $data);
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

    /**
     * Add volunteer experience from edit page.
     */
    public function addVolunteerExperience(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found.');
        }

        try {
            $validated = $request->validate([
                'organization' => ['required', 'string', 'max:255'],
                'start_date_day' => ['required', 'integer', 'min:1', 'max:31'],
                'start_date_month' => ['required', 'integer', 'min:1', 'max:12'],
                'start_date_year' => ['required', 'integer', 'min:'.(date('Y') - 50), 'max:'.(date('Y') + 10)],
                'end_date_day' => ['nullable', 'integer', 'min:1', 'max:31'],
                'end_date_month' => ['nullable', 'integer', 'min:1', 'max:12'],
                'end_date_year' => ['nullable', 'integer', 'min:'.(date('Y') - 50), 'max:'.(date('Y') + 10)],
                'is_current' => ['boolean'],
                'details' => ['nullable', 'string', 'max:2000'],
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

            $this->profileService->saveVolunteerExperience($profile, $validated);

            return redirect()->route('talent.profile.edit')
                ->with('success', 'Volunteer experience added successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove volunteer experience record.
     */
    public function removeVolunteerExperience(string $id): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found.');
        }

        $volunteerExperience = TalentVolunteerExperience::where('id', $id)
            ->where('talent_id', $profile->id)
            ->firstOrFail();

        try {
            $this->profileService->deleteVolunteerExperience($volunteerExperience);

            return redirect()->route('talent.profile.edit')
                ->with('success', 'Volunteer experience removed successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Add leadership experience from edit page.
     */
    public function addLeadershipExperience(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found.');
        }

        try {
            $validated = $request->validate([
                'organization' => ['required', 'string', 'max:255'],
                'title' => ['nullable', 'string', 'max:255'],
                'start_date_day' => ['required', 'integer', 'min:1', 'max:31'],
                'start_date_month' => ['required', 'integer', 'min:1', 'max:12'],
                'start_date_year' => ['required', 'integer', 'min:'.(date('Y') - 50), 'max:'.(date('Y') + 10)],
                'end_date_day' => ['nullable', 'integer', 'min:1', 'max:31'],
                'end_date_month' => ['nullable', 'integer', 'min:1', 'max:12'],
                'end_date_year' => ['nullable', 'integer', 'min:'.(date('Y') - 50), 'max:'.(date('Y') + 10)],
                'is_current' => ['boolean'],
                'details' => ['nullable', 'string', 'max:2000'],
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

            $this->profileService->saveLeadershipExperience($profile, $validated);

            return redirect()->route('talent.profile.edit')
                ->with('success', 'Leadership experience added successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove leadership experience record.
     */
    public function removeLeadershipExperience(string $id): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found.');
        }

        $leadershipExperience = TalentLeadershipExperience::where('id', $id)
            ->where('talent_id', $profile->id)
            ->firstOrFail();

        try {
            $this->profileService->deleteLeadershipExperience($leadershipExperience);

            return redirect()->route('talent.profile.edit')
                ->with('success', 'Leadership experience removed successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Add gigs/freelance from edit page.
     */
    public function addGigsFreelance(Request $request): RedirectResponse
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
                'title' => ['nullable', 'string', 'max:255'],
                'start_date_day' => ['required', 'integer', 'min:1', 'max:31'],
                'start_date_month' => ['required', 'integer', 'min:1', 'max:12'],
                'start_date_year' => ['required', 'integer', 'min:'.(date('Y') - 50), 'max:'.(date('Y') + 10)],
                'end_date_day' => ['nullable', 'integer', 'min:1', 'max:31'],
                'end_date_month' => ['nullable', 'integer', 'min:1', 'max:12'],
                'end_date_year' => ['nullable', 'integer', 'min:'.(date('Y') - 50), 'max:'.(date('Y') + 10)],
                'is_current' => ['boolean'],
                'details' => ['nullable', 'string', 'max:2000'],
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

            $this->profileService->saveGigsFreelance($profile, $validated);

            return redirect()->route('talent.profile.edit')
                ->with('success', 'Gigs/Freelance work added successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove gigs/freelance record.
     */
    public function removeGigsFreelance(string $id): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return redirect()->route('dashboard')
                ->with('error', 'Profile not found.');
        }

        $gigsFreelance = TalentGigsFreelance::where('id', $id)
            ->where('talent_id', $profile->id)
            ->firstOrFail();

        try {
            $this->profileService->deleteGigsFreelance($gigsFreelance);

            return redirect()->route('talent.profile.edit')
                ->with('success', 'Gigs/Freelance work removed successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update about me / bio (AJAX).
     */
    public function updateAboutMe(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        try {
            $validated = $request->validate([
                'headline' => ['nullable', 'string', 'max:255'],
                'public_url' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', Rule::unique('talent_profiles', 'public_url')->ignore($profile->id)],
                'bio' => ['nullable', 'string', 'max:1000'],
            ]);

            $this->profileService->updateAboutMe($profile, $validated);

            return response()->json([
                'success' => true,
                'message' => 'About me updated successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update fun fact (AJAX).
     */
    public function updateFunFact(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        try {
            $validated = $request->validate([
                'fun_fact' => ['nullable', 'string', 'max:1000'],
            ]);

            $this->profileService->updateFunFact($profile, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Fun fact updated successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update passion (AJAX).
     */
    public function updatePassion(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        try {
            $validated = $request->validate([
                'passion' => ['nullable', 'string', 'max:1000'],
            ]);

            $this->profileService->updatePassion($profile, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Passion updated successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update hobbies (AJAX).
     */
    public function updateHobbies(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        try {
            $validated = $request->validate([
                'hobbies' => ['nullable', 'string', 'max:1000'],
            ]);

            $this->profileService->updateHobbies($profile, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Hobbies updated successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update social links (AJAX).
     */
    public function updateSocialLinks(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        try {
            $validated = $request->validate([
                'github_url' => ['nullable', 'url', 'max:500'],
                'behance_url' => ['nullable', 'url', 'max:500'],
                'portfolio_url' => ['nullable', 'url', 'max:500'],
                'linkedin_url' => ['nullable', 'url', 'max:500'],
                'twitter_url' => ['nullable', 'url', 'max:500'],
            ]);

            $this->profileService->updateSocialLinks($profile, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Social links updated successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update work preferences (AJAX).
     */
    public function updateWorkPreferences(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        try {
            $validated = $request->validate([
                'availability' => ['nullable', Rule::enum(AvailabilityEnum::class)],
                'availability_details' => ['nullable', 'string', 'max:500'],
                'preferred_location' => ['nullable', Rule::enum(PreferredLocationEnum::class)],
                'salary_expectations' => ['nullable', 'numeric', 'min:0'],
                'career_interest_areas' => ['nullable', 'array'],
                'career_interest_areas.*' => ['uuid', 'exists:career_interest_areas,id'],
            ]);

            $this->profileService->updateWorkPreferences($profile, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Work preferences updated successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add skill record (AJAX).
     */
    public function addSkillAjax(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        try {
            $validated = $request->validate([
                'skill_name' => ['required', 'string', 'max:255'],
                'proficiency_level' => ['required', Rule::enum(\App\Enums\ProficiencyLevelEnum::class)],
            ]);

            $this->profileService->saveSkill($profile, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Skill added successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove skill record (AJAX).
     */
    public function removeSkillAjax(string $id): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        $skill = TalentSkill::where('id', $id)
            ->where('talent_id', $profile->id)
            ->firstOrFail();

        try {
            $this->profileService->deleteSkill($skill);

            return response()->json([
                'success' => true,
                'message' => 'Skill removed successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add language record (AJAX).
     */
    public function addLanguageAjax(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        try {
            $validated = $request->validate([
                'language_name' => ['required', 'string', 'max:255'],
                'proficiency_level' => ['required', Rule::enum(\App\Enums\ProficiencyLevelEnum::class)],
            ]);

            $this->profileService->saveLanguage($profile, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Language added successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove language record (AJAX).
     */
    public function removeLanguageAjax(string $id): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        $language = TalentLanguage::where('id', $id)
            ->where('talent_id', $profile->id)
            ->firstOrFail();

        try {
            $this->profileService->deleteLanguage($language);

            return response()->json([
                'success' => true,
                'message' => 'Language removed successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add education record (AJAX).
     */
    public function addEducationAjax(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
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
                return response()->json([
                    'success' => false,
                    'message' => $startDateValidation['error'],
                ], 422);
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
                        return response()->json([
                            'success' => false,
                            'message' => $endDateValidation['error'],
                        ], 422);
                    }

                    $startDate = Carbon::createFromFormat('Y-m-d', $validated['start_date']);
                    $endDate = Carbon::createFromFormat('Y-m-d', $endDateValidation['date']);

                    if ($endDate->lte($startDate)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'End date must be after start date.',
                        ], 422);
                    }

                    $validated['end_date'] = $endDateValidation['date'];
                }
                unset($validated['end_date_day'], $validated['end_date_month'], $validated['end_date_year']);
            } else {
                $validated['end_date'] = null;
                unset($validated['end_date_day'], $validated['end_date_month'], $validated['end_date_year']);
            }

            $this->profileService->saveEducation($profile, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Education record added successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove education record (AJAX).
     */
    public function removeEducationAjax(string $id): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        $education = TalentEducation::where('id', $id)
            ->where('talent_id', $profile->id)
            ->firstOrFail();

        try {
            $this->profileService->deleteEducation($education);

            return response()->json([
                'success' => true,
                'message' => 'Education record removed successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add work history record (AJAX).
     */
    public function addWorkHistoryAjax(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
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
                return response()->json([
                    'success' => false,
                    'message' => $startDateValidation['error'],
                ], 422);
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
                        return response()->json([
                            'success' => false,
                            'message' => $endDateValidation['error'],
                        ], 422);
                    }

                    $startDate = Carbon::createFromFormat('Y-m-d', $validated['start_date']);
                    $endDate = Carbon::createFromFormat('Y-m-d', $endDateValidation['date']);

                    if ($endDate->lte($startDate)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'End date must be after start date.',
                        ], 422);
                    }

                    $validated['end_date'] = $endDateValidation['date'];
                }
                unset($validated['end_date_day'], $validated['end_date_month'], $validated['end_date_year']);
            } else {
                $validated['end_date'] = null;
                unset($validated['end_date_day'], $validated['end_date_month'], $validated['end_date_year']);
            }

            $this->profileService->saveWorkHistory($profile, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Work history added successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove work history record (AJAX).
     */
    public function removeWorkHistoryAjax(string $id): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        $workHistory = TalentWorkHistory::where('id', $id)
            ->where('talent_id', $profile->id)
            ->firstOrFail();

        try {
            $this->profileService->deleteWorkHistory($workHistory);

            return response()->json([
                'success' => true,
                'message' => 'Work history removed successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add certification record (AJAX).
     */
    public function addCertificationAjax(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
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
                return response()->json([
                    'success' => false,
                    'message' => $dateObtainedValidation['error'],
                ], 422);
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
                    return response()->json([
                        'success' => false,
                        'message' => $expirationDateValidation['error'],
                    ], 422);
                }

                $dateObtained = Carbon::createFromFormat('Y-m-d', $validated['date_obtained']);
                $expirationDate = Carbon::createFromFormat('Y-m-d', $expirationDateValidation['date']);

                if ($expirationDate->lte($dateObtained)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Expiration date must be after date obtained.',
                    ], 422);
                }

                $validated['expiration_date'] = $expirationDateValidation['date'];
            }
            unset($validated['expiration_date_day'], $validated['expiration_date_month'], $validated['expiration_date_year']);

            $this->profileService->saveCertification($profile, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Certification added successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove certification record (AJAX).
     */
    public function removeCertificationAjax(string $id): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        $certification = TalentCertification::where('id', $id)
            ->where('talent_id', $profile->id)
            ->firstOrFail();

        try {
            $this->profileService->deleteCertification($certification);

            return response()->json([
                'success' => true,
                'message' => 'Certification removed successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add volunteer experience (AJAX).
     */
    public function addVolunteerExperienceAjax(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        try {
            $validated = $request->validate([
                'organization' => ['required', 'string', 'max:255'],
                'start_date_day' => ['required', 'integer', 'min:1', 'max:31'],
                'start_date_month' => ['required', 'integer', 'min:1', 'max:12'],
                'start_date_year' => ['required', 'integer', 'min:'.(date('Y') - 50), 'max:'.(date('Y') + 10)],
                'end_date_day' => ['nullable', 'integer', 'min:1', 'max:31'],
                'end_date_month' => ['nullable', 'integer', 'min:1', 'max:12'],
                'end_date_year' => ['nullable', 'integer', 'min:'.(date('Y') - 50), 'max:'.(date('Y') + 10)],
                'is_current' => ['boolean'],
                'details' => ['nullable', 'string', 'max:2000'],
            ]);

            // Validate and combine start_date
            $startDateValidation = validateDateComponents(
                $validated['start_date_day'],
                $validated['start_date_month'],
                $validated['start_date_year']
            );

            if (! $startDateValidation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $startDateValidation['error'],
                ], 422);
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
                        return response()->json([
                            'success' => false,
                            'message' => $endDateValidation['error'],
                        ], 422);
                    }

                    $startDate = Carbon::createFromFormat('Y-m-d', $validated['start_date']);
                    $endDate = Carbon::createFromFormat('Y-m-d', $endDateValidation['date']);

                    if ($endDate->lte($startDate)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'End date must be after start date.',
                        ], 422);
                    }

                    $validated['end_date'] = $endDateValidation['date'];
                }
                unset($validated['end_date_day'], $validated['end_date_month'], $validated['end_date_year']);
            } else {
                $validated['end_date'] = null;
                unset($validated['end_date_day'], $validated['end_date_month'], $validated['end_date_year']);
            }

            $this->profileService->saveVolunteerExperience($profile, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Volunteer experience added successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove volunteer experience record (AJAX).
     */
    public function removeVolunteerExperienceAjax(string $id): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        $volunteerExperience = TalentVolunteerExperience::where('id', $id)
            ->where('talent_id', $profile->id)
            ->firstOrFail();

        try {
            $this->profileService->deleteVolunteerExperience($volunteerExperience);

            return response()->json([
                'success' => true,
                'message' => 'Volunteer experience removed successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add leadership experience (AJAX).
     */
    public function addLeadershipExperienceAjax(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        try {
            $validated = $request->validate([
                'organization' => ['required', 'string', 'max:255'],
                'title' => ['nullable', 'string', 'max:255'],
                'start_date_day' => ['required', 'integer', 'min:1', 'max:31'],
                'start_date_month' => ['required', 'integer', 'min:1', 'max:12'],
                'start_date_year' => ['required', 'integer', 'min:'.(date('Y') - 50), 'max:'.(date('Y') + 10)],
                'end_date_day' => ['nullable', 'integer', 'min:1', 'max:31'],
                'end_date_month' => ['nullable', 'integer', 'min:1', 'max:12'],
                'end_date_year' => ['nullable', 'integer', 'min:'.(date('Y') - 50), 'max:'.(date('Y') + 10)],
                'is_current' => ['boolean'],
                'details' => ['nullable', 'string', 'max:2000'],
            ]);

            // Validate and combine start_date
            $startDateValidation = validateDateComponents(
                $validated['start_date_day'],
                $validated['start_date_month'],
                $validated['start_date_year']
            );

            if (! $startDateValidation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $startDateValidation['error'],
                ], 422);
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
                        return response()->json([
                            'success' => false,
                            'message' => $endDateValidation['error'],
                        ], 422);
                    }

                    $startDate = Carbon::createFromFormat('Y-m-d', $validated['start_date']);
                    $endDate = Carbon::createFromFormat('Y-m-d', $endDateValidation['date']);

                    if ($endDate->lte($startDate)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'End date must be after start date.',
                        ], 422);
                    }

                    $validated['end_date'] = $endDateValidation['date'];
                }
                unset($validated['end_date_day'], $validated['end_date_month'], $validated['end_date_year']);
            } else {
                $validated['end_date'] = null;
                unset($validated['end_date_day'], $validated['end_date_month'], $validated['end_date_year']);
            }

            $this->profileService->saveLeadershipExperience($profile, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Leadership experience added successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove leadership experience record (AJAX).
     */
    public function removeLeadershipExperienceAjax(string $id): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        $leadershipExperience = TalentLeadershipExperience::where('id', $id)
            ->where('talent_id', $profile->id)
            ->firstOrFail();

        try {
            $this->profileService->deleteLeadershipExperience($leadershipExperience);

            return response()->json([
                'success' => true,
                'message' => 'Leadership experience removed successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add gigs/freelance (AJAX).
     */
    public function addGigsFreelanceAjax(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        try {
            $validated = $request->validate([
                'company' => ['required', 'string', 'max:255'],
                'title' => ['nullable', 'string', 'max:255'],
                'start_date_day' => ['required', 'integer', 'min:1', 'max:31'],
                'start_date_month' => ['required', 'integer', 'min:1', 'max:12'],
                'start_date_year' => ['required', 'integer', 'min:'.(date('Y') - 50), 'max:'.(date('Y') + 10)],
                'end_date_day' => ['nullable', 'integer', 'min:1', 'max:31'],
                'end_date_month' => ['nullable', 'integer', 'min:1', 'max:12'],
                'end_date_year' => ['nullable', 'integer', 'min:'.(date('Y') - 50), 'max:'.(date('Y') + 10)],
                'is_current' => ['boolean'],
                'details' => ['nullable', 'string', 'max:2000'],
            ]);

            // Validate and combine start_date
            $startDateValidation = validateDateComponents(
                $validated['start_date_day'],
                $validated['start_date_month'],
                $validated['start_date_year']
            );

            if (! $startDateValidation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $startDateValidation['error'],
                ], 422);
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
                        return response()->json([
                            'success' => false,
                            'message' => $endDateValidation['error'],
                        ], 422);
                    }

                    $startDate = Carbon::createFromFormat('Y-m-d', $validated['start_date']);
                    $endDate = Carbon::createFromFormat('Y-m-d', $endDateValidation['date']);

                    if ($endDate->lte($startDate)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'End date must be after start date.',
                        ], 422);
                    }

                    $validated['end_date'] = $endDateValidation['date'];
                }
                unset($validated['end_date_day'], $validated['end_date_month'], $validated['end_date_year']);
            } else {
                $validated['end_date'] = null;
                unset($validated['end_date_day'], $validated['end_date_month'], $validated['end_date_year']);
            }

            $this->profileService->saveGigsFreelance($profile, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Gigs/Freelance work added successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove gigs/freelance record (AJAX).
     */
    public function removeGigsFreelanceAjax(string $id): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $profile = $user->talentProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        $gigsFreelance = TalentGigsFreelance::where('id', $id)
            ->where('talent_id', $profile->id)
            ->firstOrFail();

        try {
            $this->profileService->deleteGigsFreelance($gigsFreelance);

            return response()->json([
                'success' => true,
                'message' => 'Gigs/Freelance work removed successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
