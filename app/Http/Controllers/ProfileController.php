<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use App\Models\Institution;
use App\Models\TalentEducation;
use App\Models\TalentSkill;
use App\Services\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->middleware('auth');
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
        return redirect()->route('profile.build.step', ['step' => $progress['current_step']]);
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
            return redirect()->route('profile.build.step', ['step' => $progress['current_step']]);
        }

        // Don't allow skipping ahead to incomplete steps
        if ($step > $progress['current_step']) {
            return redirect()->route('profile.build.step', ['step' => $progress['current_step']])
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

        // Check authorization - only talent users can build profiles
        if (! $user || ! $user->hasRole(UserRoleEnum::TALENT->value)) {
            return redirect()->route('profile.build')
                ->with('error', 'Unauthorized.');
        }

        try {
            switch ($step) {
                case 1:
                    $validated = $request->validate([
                        'first_name' => ['required', 'string', 'max:255'],
                        'last_name' => ['required', 'string', 'max:255'],
                        'date_of_birth' => ['required', 'date', 'before:today'],
                        'gender' => ['required', Rule::in(['male', 'female', 'other', 'prefer_not_to_say'])],
                        'location' => ['required', 'string', 'max:255'],
                        'bio' => ['nullable', 'string', 'max:1000'],
                    ]);
                    $this->profileService->saveBasicInfo($profile, $validated);
                    break;

                case 2:
                    $validated = $request->validate([
                        'institution_id' => ['nullable', 'uuid', 'exists:institutions,id'],
                        'degree_type' => ['required', Rule::enum(\App\Enums\DegreeTypeEnum::class)],
                        'field_of_study' => ['required', 'string', 'max:255'],
                        'start_date' => ['required', 'date'],
                        'end_date' => ['nullable', 'date', 'after:start_date'],
                        'is_current' => ['boolean'],
                        'gpa' => ['nullable', 'numeric', 'min:0', 'max:5'],
                        'is_primary' => ['boolean'],
                    ]);
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
                    return redirect()->route('profile.build')
                        ->with('error', 'Invalid step.');
            }

            $progress = $this->profileService->getWizardProgress($profile);

            // If all steps complete, redirect to completion page
            if ($progress['current_step'] > 4) {
                return redirect()->route('profile.complete')
                    ->with('success', 'Profile completed successfully!');
            }

            // Otherwise, go to next step
            return redirect()->route('profile.build.step', ['step' => $step + 1])
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

            return redirect()->route('profile.build.step', ['step' => 2])
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

            return redirect()->route('profile.build.step', ['step' => 3])
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
}
