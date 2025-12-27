<?php

namespace App\Http\Controllers;

use App\Enums\BillingCycleEnum;
use App\Enums\EmployerCompanyStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\SubscriptionTierEnum;
use App\Enums\UserRoleEnum;
use App\Http\Requests\EmployerCompany\StoreEmployerCompanyRequest;
use App\Http\Requests\EmployerCompany\StoreTestimonialRequest;
use App\Http\Requests\EmployerCompany\SubmitEmployerCompanyRequest;
use App\Http\Requests\EmployerCompany\UpdateEmployerCompanyRequest;
use App\Http\Requests\EmployerCompany\UploadLogoRequest;
use App\Http\Requests\EmployerCompany\UploadPhotoRequest;
use App\Http\Requests\EmployerCompany\UploadVerificationDocumentRequest;
use App\Http\Requests\EmployerCompany\UploadVideoRequest;
use App\Http\Requests\Subscription\StoreSubscriptionRequest;
use App\Models\EmployerCompany;
use App\Models\Industry;
use App\Models\Subscription;
use App\Services\EmployerCompanyService;
use App\Services\PaymentService;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class EmployerProfileController extends Controller
{
    public function __construct(
        private EmployerCompanyService $employerCompanyService,
        private SubscriptionService $subscriptionService,
        private PaymentService $paymentService
    ) {
        $this->middleware('auth')->except('public', 'paymentWebhook');
        $this->middleware('role:'.UserRoleEnum::EMPLOYER->value)->except('public', 'paymentWebhook');
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

        $wizardProgress = $this->employerCompanyService->getWizardProgress($company);
        $industries = Industry::where('is_active', true)->orderBy('name')->get();

        // Load relationships for branding section
        $company->load(['photos', 'testimonials']);

        return view('pages.employer.company.show', [
            'company' => $company,
            'wizardProgress' => $wizardProgress,
            'industries' => $industries,
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

        // Redirect to show page which handles editing through inline partials
        return redirect()->route('employer.company.show');
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
            ->with(['photos', 'testimonials'])
            ->firstOrFail();

        return view('pages.employer.company.public', [
            'company' => $company,
        ]);
    }

    /**
     * Update basic info section (AJAX).
     */
    public function updateBasicInfo(Request $request): JsonResponse
    {
        $user = Auth::user();
        $company = $user->employerCompany();

        if (! $company) {
            return response()->json(['error' => 'Company profile not found.'], 404);
        }

        if (! $company->isEditableByEmployer()) {
            return response()->json(['error' => 'This company profile cannot be edited in its current status.'], 403);
        }

        // Normalize year_established: convert empty string to null
        $requestData = $request->all();
        if (isset($requestData['year_established']) && ($requestData['year_established'] === '' || trim($requestData['year_established']) === '')) {
            $requestData['year_established'] = null;
            $request->merge($requestData);
        }

        $validated = $request->validate([
            'legal_name' => ['required', 'string', 'max:255'],
            'trading_name' => ['nullable', 'string', 'max:255'],
            'industry' => ['required', 'string', 'max:255'],
            'other_industry' => ['nullable', 'string', 'max:255'],
            'company_size' => ['required', 'string', 'max:50'],
            'company_description' => ['nullable', 'string', 'max:5000'],
            'year_established' => ['nullable', 'integer', 'min:1900', 'max:'.date('Y')],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'], // 5MB max
        ]);

        // If "Others(Please Specify)" is selected and other_industry is provided, use other_industry
        if ($validated['industry'] === 'Others(Please Specify)' && ! empty($validated['other_industry'])) {
            $validated['industry'] = $validated['other_industry'];
        }
        unset($validated['other_industry']);

        try {
            // Handle logo upload if provided
            if ($request->hasFile('logo')) {
                $this->employerCompanyService->uploadLogo($user, $company, $request->file('logo'));
                Log::info('EmployerProfileController: logo uploaded with basic info update', [
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                ]);
            }

            // Remove logo from validated array as it's handled separately
            unset($validated['logo']);

            $updatedCompany = $this->employerCompanyService->updateCompanyByEmployer($user, $company, $validated);

            Log::info('EmployerProfileController: basic info updated via AJAX', [
                'user_id' => $user->id,
                'company_id' => $updatedCompany->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Basic information updated successfully.',
                'company' => $updatedCompany->fresh(),
                'completeness_score' => $updatedCompany->profile_completeness_score,
                'logo_url' => $updatedCompany->logo_url ? asset('storage/'.$updatedCompany->logo_url) : null,
            ]);
        } catch (\Exception $e) {
            Log::error('EmployerProfileController: updateBasicInfo failed', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update branding section (AJAX).
     */
    public function updateBranding(Request $request): JsonResponse
    {
        $user = Auth::user();
        $company = $user->employerCompany();

        if (! $company) {
            return response()->json(['error' => 'Company profile not found.'], 404);
        }

        if (! $company->isEditableByEmployer()) {
            return response()->json(['error' => 'This company profile cannot be edited in its current status.'], 403);
        }

        // Normalize year_established: convert empty string to null
        $requestData = $request->all();
        if (isset($requestData['year_established']) && ($requestData['year_established'] === '' || trim($requestData['year_established']) === '')) {
            $requestData['year_established'] = null;
            $request->merge($requestData);
        }

        $validated = $request->validate([
            'company_description' => ['nullable', 'string', 'max:5000'],
            'year_established' => ['nullable', 'integer', 'min:1900', 'max:'.date('Y')],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'], // 5MB max
            'photos.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:10240'], // 10MB max per photo
        ]);

        try {
            // Handle logo upload if provided
            if ($request->hasFile('logo')) {
                $this->employerCompanyService->uploadLogo($user, $company, $request->file('logo'));
                Log::info('EmployerProfileController: logo uploaded with branding update', [
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                ]);
            }

            // Handle photo uploads if provided
            if ($request->hasFile('photos')) {
                $uploadedPhotos = [];
                foreach ($request->file('photos') as $photoFile) {
                    try {
                        $photo = $this->employerCompanyService->uploadPhoto($user, $company, $photoFile);
                        $uploadedPhotos[] = $photo->id;
                        Log::info('EmployerProfileController: photo uploaded with branding update', [
                            'user_id' => $user->id,
                            'company_id' => $company->id,
                            'photo_id' => $photo->id,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('EmployerProfileController: failed to upload photo in branding update', [
                            'user_id' => $user->id,
                            'company_id' => $company->id,
                            'error' => $e->getMessage(),
                        ]);
                        // Continue with other photos even if one fails
                    }
                }
                Log::info('EmployerProfileController: photos uploaded with branding update', [
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                    'uploaded_count' => count($uploadedPhotos),
                ]);
            }

            // Remove logo and photos from validated array as they're handled separately
            unset($validated['logo'], $validated['photos']);

            $updatedCompany = $this->employerCompanyService->updateCompanyByEmployer($user, $company, $validated);

            Log::info('EmployerProfileController: branding updated via AJAX', [
                'user_id' => $user->id,
                'company_id' => $updatedCompany->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Branding information updated successfully.',
                'company' => $updatedCompany->fresh(),
                'completeness_score' => $updatedCompany->profile_completeness_score,
                'logo_url' => $updatedCompany->logo_url ? asset('storage/'.$updatedCompany->logo_url) : null,
            ]);
        } catch (\Exception $e) {
            Log::error('EmployerProfileController: updateBranding failed', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update contact & location section (AJAX).
     */
    public function updateContactLocation(Request $request): JsonResponse
    {
        $user = Auth::user();
        $company = $user->employerCompany();

        if (! $company) {
            return response()->json(['error' => 'Company profile not found.'], 404);
        }

        if (! $company->isEditableByEmployer()) {
            return response()->json(['error' => 'This company profile cannot be edited in its current status.'], 403);
        }

        $validated = $request->validate([
            'country' => ['nullable', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'state_or_region' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'official_email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
        ]);

        try {
            $updatedCompany = $this->employerCompanyService->updateCompanyByEmployer($user, $company, $validated);

            Log::info('EmployerProfileController: contact & location updated via AJAX', [
                'user_id' => $user->id,
                'company_id' => $updatedCompany->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contact & location information updated successfully.',
                'company' => $updatedCompany->fresh(),
                'completeness_score' => $updatedCompany->profile_completeness_score,
            ]);
        } catch (\Exception $e) {
            Log::error('EmployerProfileController: updateContactLocation failed', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update registration section (AJAX).
     */
    public function updateRegistration(Request $request): JsonResponse
    {
        $user = Auth::user();
        $company = $user->employerCompany();

        if (! $company) {
            return response()->json([
                'success' => false,
                'error' => 'Company profile not found.',
            ], 404);
        }

        if (! $company->isEditableByEmployer()) {
            return response()->json([
                'success' => false,
                'error' => 'This company profile cannot be edited in its current status.',
            ], 403);
        }

        // Check if files were uploaded (handles 413 errors before validation)
        $ghanaCardFile = $request->file('ghana_card');
        $businessRegistrationFile = $request->file('business_registration');

        // Check for 413 errors - if request has file fields but no actual files, it might be a size issue
        // This happens when the file exceeds PHP's upload_max_filesize or post_max_size
        $contentLength = $request->header('Content-Length');
        if ($contentLength && (int) $contentLength > 0) {
            // If we have content but no files, it might be a size issue
            if (! $ghanaCardFile && $request->has('ghana_card')) {
                $maxSize = ini_get('upload_max_filesize');
                $postMaxSize = ini_get('post_max_size');
                Log::warning('EmployerProfileController: Ghana Card upload failed - size limit exceeded', [
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                    'content_length' => $contentLength,
                    'upload_max_filesize' => $maxSize,
                    'post_max_size' => $postMaxSize,
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Ghana Card file is too large. Maximum size is 10MB. Server limit: '.$maxSize,
                ], 413);
            }

            if (! $businessRegistrationFile && $request->has('business_registration')) {
                $maxSize = ini_get('upload_max_filesize');
                $postMaxSize = ini_get('post_max_size');
                Log::warning('EmployerProfileController: Business Registration upload failed - size limit exceeded', [
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                    'content_length' => $contentLength,
                    'upload_max_filesize' => $maxSize,
                    'post_max_size' => $postMaxSize,
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Business Registration file is too large. Maximum size is 10MB. Server limit: '.$maxSize,
                ], 413);
            }
        }

        // Determine if files are required (if they haven't been uploaded yet)
        $ghanaCardRequired = ! $company->ghana_card_document_url;
        $businessRegistrationRequired = ! $company->business_registration_document_url;

        $validationRules = [
            'registration_number' => ['required', 'string', 'max:100'],
        ];

        // Make files required if they haven't been uploaded yet
        if ($ghanaCardRequired) {
            $validationRules['ghana_card'] = ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'];
        } else {
            $validationRules['ghana_card'] = ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'];
        }

        if ($businessRegistrationRequired) {
            $validationRules['business_registration'] = ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'];
        } else {
            $validationRules['business_registration'] = ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'];
        }

        try {
            $validated = $request->validate($validationRules);

            Log::info('EmployerProfileController: registration update started', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'has_ghana_card' => $request->hasFile('ghana_card'),
                'has_business_registration' => $request->hasFile('business_registration'),
                'has_registration_number' => isset($validated['registration_number']),
            ]);

            $updatedCompany = $company;

            // Update registration number if provided
            if (isset($validated['registration_number'])) {
                $updatedCompany = $this->employerCompanyService->updateCompanyByEmployer($user, $updatedCompany, [
                    'registration_number' => $validated['registration_number'],
                ]);
            }

            // Upload Ghana Card document if provided
            if ($request->hasFile('ghana_card')) {
                $file = $request->file('ghana_card');
                $updatedCompany = $this->employerCompanyService->uploadGhanaCardDocument($user, $updatedCompany, $file);
                Log::info('EmployerProfileController: Ghana Card document uploaded', [
                    'user_id' => $user->id,
                    'company_id' => $updatedCompany->id,
                    'file_size' => $file->getSize(),
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }

            // Upload Business Registration document if provided
            if ($request->hasFile('business_registration')) {
                $file = $request->file('business_registration');
                $updatedCompany = $this->employerCompanyService->uploadBusinessRegistrationDocument($user, $updatedCompany, $file);
                Log::info('EmployerProfileController: Business Registration document uploaded', [
                    'user_id' => $user->id,
                    'company_id' => $updatedCompany->id,
                    'file_size' => $file->getSize(),
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }

            Log::info('EmployerProfileController: registration updated via AJAX', [
                'user_id' => $user->id,
                'company_id' => $updatedCompany->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration information updated successfully.',
                'company' => $updatedCompany->fresh(),
                'completeness_score' => $updatedCompany->profile_completeness_score,
            ]);
        } catch (\Illuminate\Http\Exceptions\PostTooLargeException $e) {
            Log::error('EmployerProfileController: updateRegistration failed - PostTooLargeException', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'File is too large. Maximum size is 10MB. Please try a smaller file.',
            ], 413);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            $errorMessage = 'Validation failed.';

            // Check for file-related errors
            if (isset($errors['ghana_card'])) {
                $ghanaCardErrors = $errors['ghana_card'];
                if (is_array($ghanaCardErrors)) {
                    $errorMessage = implode(', ', $ghanaCardErrors);
                } else {
                    $errorMessage = $ghanaCardErrors;
                }

                if (str_contains(strtolower($errorMessage), 'max')) {
                    $errorMessage = 'Ghana Card file is too large. Maximum size is 10MB.';
                } elseif (str_contains(strtolower($errorMessage), 'mimes')) {
                    $errorMessage = 'Ghana Card: Invalid file type. Please upload a PDF, JPG, JPEG, or PNG file.';
                } elseif (str_contains(strtolower($errorMessage), 'required')) {
                    $errorMessage = 'Ghana Card document is required.';
                }
            } elseif (isset($errors['business_registration'])) {
                $businessRegErrors = $errors['business_registration'];
                if (is_array($businessRegErrors)) {
                    $errorMessage = implode(', ', $businessRegErrors);
                } else {
                    $errorMessage = $businessRegErrors;
                }

                if (str_contains(strtolower($errorMessage), 'max')) {
                    $errorMessage = 'Business Registration file is too large. Maximum size is 10MB.';
                } elseif (str_contains(strtolower($errorMessage), 'mimes')) {
                    $errorMessage = 'Business Registration: Invalid file type. Please upload a PDF, JPG, JPEG, or PNG file.';
                } elseif (str_contains(strtolower($errorMessage), 'required')) {
                    $errorMessage = 'Business Registration document is required.';
                }
            }

            Log::warning('EmployerProfileController: updateRegistration validation failed', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'errors' => $errors,
            ]);

            return response()->json([
                'success' => false,
                'error' => $errorMessage,
                'errors' => $errors,
            ], 422);
        } catch (\Exception $e) {
            Log::error('EmployerProfileController: updateRegistration failed', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMessage = 'Failed to update registration. Please try again.';
            if (str_contains($e->getMessage(), 'exceeded') || str_contains($e->getMessage(), 'too large')) {
                $errorMessage = 'File is too large. Maximum size is 10MB.';
            } elseif (str_contains($e->getMessage(), 'network') || str_contains($e->getMessage(), 'timeout')) {
                $errorMessage = 'Upload timed out. Please check your internet connection and try again.';
            }

            return response()->json([
                'success' => false,
                'error' => $errorMessage,
            ], 500);
        }
    }

    /**
     * Update primary contact section (AJAX).
     */
    public function updatePrimaryContact(Request $request): JsonResponse
    {
        $user = Auth::user();
        $company = $user->employerCompany();

        if (! $company) {
            return response()->json(['error' => 'Company profile not found.'], 404);
        }

        if (! $company->isEditableByEmployer()) {
            return response()->json(['error' => 'This company profile cannot be edited in its current status.'], 403);
        }

        $validated = $request->validate([
            'primary_contact_name' => ['required', 'string', 'max:255'],
            'primary_contact_title' => ['nullable', 'string', 'max:255'],
            'primary_contact_email' => ['nullable', 'email', 'max:255'],
            'primary_contact_phone' => ['required', 'string', 'max:20'],
        ]);

        try {
            $updatedCompany = $this->employerCompanyService->updateCompanyByEmployer($user, $company, $validated);

            Log::info('EmployerProfileController: primary contact updated via AJAX', [
                'user_id' => $user->id,
                'company_id' => $updatedCompany->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Primary contact information updated successfully.',
                'company' => $updatedCompany->fresh(),
                'completeness_score' => $updatedCompany->profile_completeness_score,
            ]);
        } catch (\Exception $e) {
            Log::error('EmployerProfileController: updatePrimaryContact failed', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Upload verification document (Ghana Card or Business Registration).
     */
    public function uploadVerificationDocument(UploadVerificationDocumentRequest $request): JsonResponse
    {
        $user = Auth::user();
        $company = $user->employerCompany();

        if (! $company) {
            return response()->json([
                'success' => false,
                'error' => 'Company profile not found.',
            ], 404);
        }

        // Check if file was uploaded (handles 413 errors before validation)
        if (! $request->hasFile('document')) {
            // Check if it's a size issue - if Content-Length header exists and is large, file might be too big
            $contentLength = $request->header('Content-Length');
            if ($contentLength && (int) $contentLength > 0) {
                $maxSize = ini_get('upload_max_filesize');
                $postMaxSize = ini_get('post_max_size');

                Log::warning('EmployerProfileController: File upload failed - size limit exceeded', [
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                    'content_length' => $contentLength,
                    'upload_max_filesize' => $maxSize,
                    'post_max_size' => $postMaxSize,
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'File is too large. Maximum size is 10MB. Server limit: '.$maxSize,
                ], 413);
            }

            return response()->json([
                'success' => false,
                'error' => 'No file was uploaded. Please select a file.',
            ], 422);
        }

        try {
            $validated = $request->validated();
            $type = $validated['type'];
            $file = $request->file('document');

            if ($type === 'ghana_card') {
                $updatedCompany = $this->employerCompanyService->uploadGhanaCardDocument($user, $company, $file);
            } else {
                $updatedCompany = $this->employerCompanyService->uploadBusinessRegistrationDocument($user, $company, $file);
            }

            Log::info('EmployerProfileController: verification document uploaded', [
                'user_id' => $user->id,
                'company_id' => $updatedCompany->id,
                'type' => $type,
                'file_size' => $file->getSize(),
                'file_name' => $file->getClientOriginalName(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Verification document uploaded successfully.',
                'company' => $updatedCompany->fresh(),
            ]);
        } catch (\Illuminate\Http\Exceptions\PostTooLargeException $e) {
            Log::error('EmployerProfileController: uploadVerificationDocument failed - PostTooLargeException', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'File is too large. Maximum size is 10MB. Please try a smaller file.',
            ], 413);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            $errorMessage = 'Validation failed.';

            if (isset($errors['document'])) {
                $documentErrors = $errors['document'];
                if (is_array($documentErrors)) {
                    $errorMessage = implode(', ', $documentErrors);
                } else {
                    $errorMessage = $documentErrors;
                }

                // Improve error messages
                if (str_contains(strtolower($errorMessage), 'max')) {
                    $errorMessage = 'File is too large. Maximum size is 10MB.';
                } elseif (str_contains(strtolower($errorMessage), 'mimes')) {
                    $errorMessage = 'Invalid file type. Please upload a PDF, JPG, JPEG, or PNG file.';
                }
            }

            Log::warning('EmployerProfileController: uploadVerificationDocument validation failed', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'errors' => $errors,
            ]);

            return response()->json([
                'success' => false,
                'error' => $errorMessage,
                'errors' => $errors,
            ], 422);
        } catch (\Exception $e) {
            Log::error('EmployerProfileController: uploadVerificationDocument failed', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMessage = 'Failed to upload document. Please try again.';
            if (str_contains($e->getMessage(), 'exceeded') || str_contains($e->getMessage(), 'too large')) {
                $errorMessage = 'File is too large. Maximum size is 10MB.';
            } elseif (str_contains($e->getMessage(), 'network') || str_contains($e->getMessage(), 'timeout')) {
                $errorMessage = 'Upload timed out. Please check your internet connection and try again.';
            }

            return response()->json([
                'success' => false,
                'error' => $errorMessage,
            ], 500);
        }
    }

    /**
     * Upload company logo.
     */
    public function uploadLogo(UploadLogoRequest $request): JsonResponse
    {
        $user = Auth::user();
        $company = $user->employerCompany();

        if (! $company) {
            return response()->json(['error' => 'Company profile not found.'], 404);
        }

        try {
            $file = $request->file('logo');
            $updatedCompany = $this->employerCompanyService->uploadLogo($user, $company, $file);

            Log::info('EmployerProfileController: logo uploaded', [
                'user_id' => $user->id,
                'company_id' => $updatedCompany->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Logo uploaded successfully.',
                'company' => $updatedCompany->fresh(),
                'logo_url' => $updatedCompany->logo_url ? asset('storage/'.$updatedCompany->logo_url) : null,
            ]);
        } catch (\Exception $e) {
            Log::error('EmployerProfileController: uploadLogo failed', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Upload company photo.
     */
    public function uploadPhoto(UploadPhotoRequest $request): JsonResponse
    {
        $user = Auth::user();
        $company = $user->employerCompany();

        if (! $company) {
            return response()->json(['error' => 'Company profile not found.'], 404);
        }

        try {
            $file = $request->file('photo');
            $caption = $request->validated()['caption'] ?? null;
            $photo = $this->employerCompanyService->uploadPhoto($user, $company, $file, $caption);

            Log::info('EmployerProfileController: photo uploaded', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'photo_id' => $photo->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo uploaded successfully.',
                'photo' => [
                    'id' => $photo->id,
                    'photo_url' => asset('storage/'.$photo->photo_url),
                    'caption' => $photo->caption,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('EmployerProfileController: uploadPhoto failed', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete company photo.
     */
    public function deletePhoto(Request $request, string $photoId): JsonResponse
    {
        $user = Auth::user();
        $company = $user->employerCompany();

        if (! $company) {
            return response()->json(['error' => 'Company profile not found.'], 404);
        }

        try {
            $this->employerCompanyService->deletePhoto($user, $company, $photoId);

            Log::info('EmployerProfileController: photo deleted', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'photo_id' => $photoId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('EmployerProfileController: deletePhoto failed', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'photo_id' => $photoId,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Upload company video.
     */
    public function uploadVideo(UploadVideoRequest $request): JsonResponse
    {
        $user = Auth::user();
        $company = $user->employerCompany();

        if (! $company) {
            return response()->json(['error' => 'Company profile not found.'], 404);
        }

        try {
            $file = $request->file('video');
            $updatedCompany = $this->employerCompanyService->uploadVideo($user, $company, $file);

            Log::info('EmployerProfileController: video uploaded', [
                'user_id' => $user->id,
                'company_id' => $updatedCompany->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Video uploaded successfully.',
                'company' => $updatedCompany->fresh(),
                'video_url' => $updatedCompany->video_url ? asset('storage/'.$updatedCompany->video_url) : null,
            ]);
        } catch (\Exception $e) {
            Log::error('EmployerProfileController: uploadVideo failed', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create testimonial.
     */
    public function storeTestimonial(StoreTestimonialRequest $request): JsonResponse
    {
        $user = Auth::user();
        $company = $user->employerCompany();

        if (! $company) {
            return response()->json(['error' => 'Company profile not found.'], 404);
        }

        try {
            $data = $request->validated();
            $photoFile = $request->hasFile('photo') ? $request->file('photo') : null;
            $testimonial = $this->employerCompanyService->createTestimonial($user, $company, $data, $photoFile);

            Log::info('EmployerProfileController: testimonial created', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'testimonial_id' => $testimonial->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Testimonial created successfully.',
                'testimonial' => [
                    'id' => $testimonial->id,
                    'employee_name' => $testimonial->employee_name,
                    'employee_title' => $testimonial->employee_title,
                    'testimonial' => $testimonial->testimonial,
                    'photo_url' => $testimonial->photo_url ? asset('storage/'.$testimonial->photo_url) : null,
                    'is_featured' => $testimonial->is_featured,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('EmployerProfileController: storeTestimonial failed', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete testimonial.
     */
    public function deleteTestimonial(Request $request, string $testimonialId): JsonResponse
    {
        $user = Auth::user();
        $company = $user->employerCompany();

        if (! $company) {
            return response()->json(['error' => 'Company profile not found.'], 404);
        }

        try {
            $this->employerCompanyService->deleteTestimonial($user, $company, $testimonialId);

            Log::info('EmployerProfileController: testimonial deleted', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'testimonial_id' => $testimonialId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Testimonial deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('EmployerProfileController: deleteTestimonial failed', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'testimonial_id' => $testimonialId,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show subscription selection page.
     */
    public function selectSubscription(): View|RedirectResponse
    {
        $user = Auth::user();
        $company = $user->employerCompany();

        if (! $company) {
            return redirect()->route('employer.company.edit')
                ->with('error', 'Please complete company profile setup first.');
        }

        $packages = config('subscriptions.packages', []);
        $currentSubscription = $company->subscription;

        return view('pages.employer.company.subscription-select', [
            'company' => $company,
            'packages' => $packages,
            'currentSubscription' => $currentSubscription,
        ]);
    }

    /**
     * Store subscription selection.
     */
    public function storeSubscription(StoreSubscriptionRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $company = $user->employerCompany();

        if (! $company) {
            return redirect()->route('employer.company.edit')
                ->with('error', 'Please complete company profile setup first.');
        }

        Log::info('EmployerProfileController: Storing subscription selection', [
            'user_id' => $user->id,
            'company_id' => $company->id,
            'tier' => $request->input('tier'),
            'billing_cycle' => $request->input('billing_cycle'),
        ]);

        try {
            $tier = SubscriptionTierEnum::from($request->input('tier'));
            $billingCycle = $request->has('billing_cycle') && $request->input('billing_cycle')
                ? BillingCycleEnum::from($request->input('billing_cycle'))
                : null;

            $subscription = $this->subscriptionService->createSubscription($company, $tier, $billingCycle);

            // If FREE tier, redirect to company show page
            if ($tier === SubscriptionTierEnum::FREE) {
                return redirect()->route('employer.company.show')
                    ->with('success', 'Free subscription activated successfully!');
            }

            // For paid tiers: initiate payment and redirect directly to Paystack
            try {
                // Use CARD as default - Paystack will show all payment options
                $paymentMethod = PaymentMethodEnum::CARD;
                $paymentData = []; // No additional data needed

                $paymentResponse = $this->subscriptionService->processPayment(
                    $subscription,
                    $paymentMethod,
                    $paymentData
                );

                if (isset($paymentResponse['authorization_url'])) {
                    return redirect($paymentResponse['authorization_url']);
                }

                throw new \Exception('Payment authorization URL not received');
            } catch (\Exception $e) {
                Log::error('EmployerProfileController: Failed to initiate payment', [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to initiate payment: '.$e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('EmployerProfileController: Failed to store subscription', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create subscription: '.$e->getMessage());
        }
    }

    /**
     * Handle Paystack payment callback.
     */
    public function paymentCallback(Request $request): RedirectResponse
    {
        $reference = $request->query('reference');

        if (empty($reference)) {
            return redirect()->route('employer.subscription.select')
                ->with('error', 'Invalid payment reference.');
        }

        Log::info('EmployerProfileController: Processing payment callback', [
            'reference' => $reference,
        ]);

        try {
            // Verify payment
            $verificationResult = $this->paymentService->verifyPayment($reference);

            if (! $verificationResult['success']) {
                return redirect()->route('employer.subscription.select')
                    ->with('error', 'Payment verification failed.');
            }

            // Find subscription by payment reference
            $subscription = Subscription::where('payment_reference', $reference)->first();

            if (! $subscription) {
                Log::warning('EmployerProfileController: Subscription not found for payment reference', [
                    'reference' => $reference,
                ]);

                return redirect()->route('employer.subscription.select')
                    ->with('error', 'Subscription not found.');
            }

            // Activate subscription if payment successful
            if ($verificationResult['status'] === 'success') {
                $this->subscriptionService->activateSubscription($subscription);

                return redirect()->route('employer.company.show')
                    ->with('success', 'Payment successful! Your subscription has been activated.');
            }

            return redirect()->route('employer.subscription.select')
                ->with('error', 'Payment was not successful. Please try again.');
        } catch (\Exception $e) {
            Log::error('EmployerProfileController: Payment callback processing failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('employer.subscription.select')
                ->with('error', 'Payment processing error. Please contact support.');
        }
    }

    /**
     * Handle Paystack webhook.
     */
    public function paymentWebhook(Request $request): JsonResponse
    {
        Log::info('EmployerProfileController: Received Paystack webhook', [
            'event' => $request->input('event'),
        ]);

        try {
            // Verify webhook signature
            $signature = $request->header('X-Paystack-Signature');
            $payload = $request->getContent();

            if (! $this->paymentService->verifyWebhookSignature($signature, $payload)) {
                Log::warning('EmployerProfileController: Invalid webhook signature');

                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Process webhook (handles subscription activation/failure internally)
            $this->paymentService->handleWebhook($request->all(), $this->subscriptionService);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('EmployerProfileController: Webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
