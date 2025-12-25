@extends('layouts.dashboard.main')

@php
/** @var \App\Models\EmployerCompany $company */
$isEditable = $company->isEditableByEmployer();

// Helper function to check if field is not empty
$isFieldNotEmpty = function($value) {
return !empty($value) && trim($value) !== '';
};

// Determine section completion status
$basicInfoComplete = $isFieldNotEmpty($company->legal_name);
$contactLocationComplete = $isFieldNotEmpty($company->city) || $isFieldNotEmpty($company->address)
|| $isFieldNotEmpty($company->phone_number) || $isFieldNotEmpty($company->official_email)
|| $isFieldNotEmpty($company->website) || $isFieldNotEmpty($company->linkedin_url);
$registrationComplete = $isFieldNotEmpty($company->registration_number);
$verificationComplete = $isFieldNotEmpty($company->ghana_card_document_url) &&
$isFieldNotEmpty($company->business_registration_document_url);
$primaryContactComplete = $isFieldNotEmpty($company->primary_contact_name) ||
$isFieldNotEmpty($company->primary_contact_email);
$brandingComplete = $isFieldNotEmpty($company->logo_url) || $isFieldNotEmpty($company->company_description) ||
$company->photos->count() > 0;
@endphp

@section('content')
<div class="dashboard-container">
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <h2 class="dashboard-card-title" style="font-family: var(--font-bricolageGrotesque);">{{
                    $company->legal_name ?: 'Company Profile' }}</h2>
                <p class="dashboard-card-subtitle" style="font-family: var(--font-suse);">
                    Company profile status: <strong>{{ ucfirst(str_replace('_', ' ', $company->status)) }}</strong>
                    @if($company->profile_completeness_score !== null)
                    <span class="ms-2">• Profile completeness: <strong style="color: var(--primary-color1);">{{
                            $company->profile_completeness_score }}%</strong></span>
                    @endif
                </p>
            </div>
            <div class="d-flex gap-2">
                @if($company->isApproved())
                <a href="{{ route('employer.company.public', ['id' => $company->id]) }}" class="primary-btn1"
                    target="_blank" rel="noopener">
                    <span>View Public</span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 0L9.41 1.41L3.83 7H16V9H3.83L9.41 14.59L8 16L0 8L8 0Z" fill="currentColor" />
                    </svg>
                </a>
                @endif
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert"
            style="font-family: var(--font-suse); border-radius: 8px; border: none;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert"
            style="font-family: var(--font-suse); border-radius: 8px; border: none;">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert"
            style="font-family: var(--font-suse); border-radius: 8px; border: none;">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if($company->status === \App\Enums\EmployerCompanyStatusEnum::NEEDS_CHANGES->value)
        <div class="alert alert-warning" style="font-family: var(--font-suse); border-radius: 8px; border: none;">
            <strong>Changes requested:</strong>
            <div class="mt-2">{{ $company->review_notes }}</div>
        </div>
        @endif

        @if($company->status === \App\Enums\EmployerCompanyStatusEnum::SUBMITTED->value)
        <div class="alert alert-info" style="font-family: var(--font-suse); border-radius: 8px; border: none;">
            Your company profile is pending review. You'll be notified once it's approved.
        </div>
        @endif

        @if($company->status === \App\Enums\EmployerCompanyStatusEnum::APPROVED->value)
        <div class="alert alert-success" style="font-family: var(--font-suse); border-radius: 8px; border: none;">
            Your company is approved. You can now access employer features.
        </div>
        @endif

        @if($company->status === \App\Enums\EmployerCompanyStatusEnum::REJECTED->value)
        <div class="alert alert-danger" style="font-family: var(--font-suse); border-radius: 8px; border: none;">
            <strong>Rejected:</strong>
            <div class="mt-2">{{ $company->review_notes }}</div>
        </div>
        @endif

        @if($company->status === \App\Enums\EmployerCompanyStatusEnum::SUSPENDED->value)
        <div class="alert alert-danger" style="font-family: var(--font-suse); border-radius: 8px; border: none;">
            <strong>Suspended:</strong>
            <div class="mt-2">{{ $company->review_notes }}</div>
        </div>
        @endif

        <!-- Company Profile Sections -->
        <div class="company-profile-sections">
            <!-- Basic Company Information Section -->
            <div class="company-section-card" data-section="basic-info">
                <div class="company-section-header">
                    <div class="company-section-title-wrapper">
                        <span class="company-section-status-icon">
                            @if($basicInfoComplete)
                            <i class="bi bi-check-circle-fill text-success"></i>
                            @else
                            <i class="bi bi-circle text-muted"></i>
                            @endif
                        </span>
                        <h3 class="company-section-title">Basic Company Information</h3>
                    </div>
                    <div class="company-section-actions">
                        @if($isEditable)
                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit-section"
                            data-section="basic-info">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        @endif
                        <button type="button" class="btn btn-sm btn-link btn-toggle-section" data-section="basic-info">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>
                </div>
                <div class="company-section-content" data-section="basic-info">
                    <div class="company-section-view">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div><strong>Legal Name</strong></div>
                                <div>{{ $company->legal_name ?: '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div><strong>Trading Name</strong></div>
                                <div>{{ $company->trading_name ?: '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div><strong>Industry</strong></div>
                                <div>{{ $company->industry ?: '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div><strong>Company Size</strong></div>
                                <div>{{ $company->company_size ?: '—' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="company-section-edit" style="display: none;">
                        @include('pages.employer.company.partials.basic-info-edit', ['company' => $company, 'industries'
                        => $industries])
                    </div>
                </div>
            </div>

            <!-- Contact & Location Information Section -->
            <div class="company-section-card" data-section="contact-location">
                <div class="company-section-header">
                    <div class="company-section-title-wrapper">
                        <span class="company-section-status-icon">
                            @if($contactLocationComplete)
                            <i class="bi bi-check-circle-fill text-success"></i>
                            @else
                            <i class="bi bi-exclamation-circle text-warning"></i>
                            @endif
                        </span>
                        <h3 class="company-section-title">Contact & Location Information</h3>
                    </div>
                    <div class="company-section-actions">
                        @if($isEditable)
                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit-section"
                            data-section="contact-location">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        @endif
                        <button type="button" class="btn btn-sm btn-link btn-toggle-section"
                            data-section="contact-location">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>
                </div>
                <div class="company-section-content" data-section="contact-location">
                    <div class="company-section-view">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div><strong>Country</strong></div>
                                <div>{{ $company->country ?: '—' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div><strong>State or Region</strong></div>
                                <div>{{ $company->state_or_region ?: '—' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div><strong>City</strong></div>
                                <div>{{ $company->city ?: '—' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div><strong>Phone Number</strong></div>
                                <div>{{ $company->phone_number ?: '—' }}</div>
                            </div>
                            <div class="col-12">
                                <div><strong>Address</strong></div>
                                <div>{{ $company->address ?: '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div><strong>Official Email</strong></div>
                                <div>{{ $company->official_email ?: '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div><strong>Website</strong></div>
                                <div>
                                    @if($company->website)
                                    <a href="{{ $company->website }}" target="_blank" rel="noopener">{{
                                        $company->website }}</a>
                                    @else
                                    —
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <div><strong>LinkedIn URL</strong></div>
                                <div>
                                    @if($company->linkedin_url)
                                    <a href="{{ $company->linkedin_url }}" target="_blank" rel="noopener">{{
                                        $company->linkedin_url }}</a>
                                    @else
                                    —
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="company-section-edit" style="display: none;">
                        @include('pages.employer.company.partials.contact-location-edit', ['company' => $company])
                    </div>
                </div>
            </div>

            <!-- Registration & Verification Section -->
            <div class="company-section-card" data-section="registration">
                <div class="company-section-header">
                    <div class="company-section-title-wrapper">
                        <span class="company-section-status-icon">
                            @if($registrationComplete)
                            <i class="bi bi-check-circle-fill text-success"></i>
                            @else
                            <i class="bi bi-circle text-muted"></i>
                            @endif
                        </span>
                        <h3 class="company-section-title">Registration & Verification</h3>
                    </div>
                    <div class="company-section-actions">
                        @if($isEditable)
                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit-section"
                            data-section="registration">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        @endif
                        <button type="button" class="btn btn-sm btn-link btn-toggle-section"
                            data-section="registration">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>
                </div>
                <div class="company-section-content" data-section="registration">
                    <div class="company-section-view">
                        <div class="row g-3">
                            <div class="col-12">
                                <div><strong>Registration Number / TIN</strong></div>
                                <div>{{ $company->registration_number ?: '—' }}</div>
                            </div>
                            @if($company->ghana_card_document_url || $company->business_registration_document_url)
                            <div class="col-12 mt-3">
                                <div><strong>Verification Status</strong></div>
                                <div>
                                    @if($company->verification_status === 'verified')
                                    <span class="badge bg-success">Verified</span>
                                    @elseif($company->verification_status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                    @else
                                    <span class="badge bg-warning">Pending Review</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <div><strong>Verification Documents</strong></div>
                                <div class="mt-2">
                                    @if($company->ghana_card_document_url)
                                    <span class="badge bg-success me-2"><i class="bi bi-check-circle"></i> Ghana
                                        Card</span>
                                    @else
                                    <span class="badge bg-secondary me-2"><i class="bi bi-x-circle"></i> Ghana
                                        Card</span>
                                    @endif
                                    @if($company->business_registration_document_url)
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Business
                                        Registration</span>
                                    @else
                                    <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Business
                                        Registration</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="company-section-edit" style="display: none;">
                        @include('pages.employer.company.partials.registration-edit', ['company' => $company])
                    </div>
                </div>
            </div>

            <!-- Branding Section -->
            <div class="company-section-card" data-section="branding">
                <div class="company-section-header">
                    <div class="company-section-title-wrapper">
                        <span class="company-section-status-icon">
                            @if($brandingComplete)
                            <i class="bi bi-check-circle-fill text-success"></i>
                            @else
                            <i class="bi bi-circle text-muted"></i>
                            @endif
                        </span>
                        <h3 class="company-section-title">Company Branding</h3>
                    </div>
                    <div class="company-section-actions">
                        @if($isEditable)
                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit-section"
                            data-section="branding">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        @endif
                        <button type="button" class="btn btn-sm btn-link btn-toggle-section" data-section="branding">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>
                </div>
                <div class="company-section-content" data-section="branding">
                    <div class="company-section-view">
                        <div class="row g-3">
                            @if($company->logo_url)
                            <div class="col-12">
                                <div><strong>Logo</strong></div>
                                <img src="{{ asset('storage/'.$company->logo_url) }}" alt="Company Logo"
                                    style="max-width: 200px; max-height: 200px; border-radius: 8px;">
                            </div>
                            @endif
                            @if($company->company_description)
                            <div class="col-12">
                                <div><strong>Company Description</strong></div>
                                <div>{{ $company->company_description }}</div>
                            </div>
                            @endif
                            @if($company->year_established)
                            <div class="col-12">
                                <div><strong>Year Established</strong></div>
                                <div>{{ $company->year_established }}</div>
                            </div>
                            @endif
                            @if($company->video_url)
                            <div class="col-12">
                                <div><strong>Company Video</strong></div>
                                <video controls style="max-width: 100%; border-radius: 8px;"
                                    src="{{ asset('storage/'.$company->video_url) }}"></video>
                            </div>
                            @endif
                            @if($company->photos->count() > 0)
                            <div class="col-12">
                                <div><strong>Company Photos ({{ $company->photos->count() }})</strong></div>
                                <div class="row g-2 mt-2">
                                    @foreach($company->photos->take(4) as $photo)
                                    <div class="col-md-3">
                                        <img src="{{ asset('storage/'.$photo->photo_url) }}" class="img-thumbnail"
                                            alt="Company Photo" style="height: 100px; object-fit: cover;">
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @if($company->testimonials->count() > 0)
                            <div class="col-12">
                                <div><strong>Employee Testimonials ({{ $company->testimonials->count() }})</strong>
                                </div>
                                <div class="mt-2">
                                    @foreach($company->testimonials->take(2) as $testimonial)
                                    <div class="card mb-2">
                                        <div class="card-body p-2">
                                            <strong>{{ $testimonial->employee_name }}</strong>
                                            @if($testimonial->employee_title)
                                            <span class="text-muted small"> - {{ $testimonial->employee_title }}</span>
                                            @endif
                                            <p class="mb-0 small">{{ Str::limit($testimonial->testimonial, 100) }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @if(!$company->logo_url && !$company->company_description && !$company->year_established &&
                            !$company->video_url && $company->photos->count() === 0 && $company->testimonials->count()
                            === 0)
                            <div class="col-12">
                                <p class="text-muted">No branding information added yet.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="company-section-edit" style="display: none;">
                        @include('pages.employer.company.partials.branding-edit', ['company' => $company])
                    </div>
                </div>
            </div>

            <!-- Primary Contact Information Section -->
            <div class="company-section-card" data-section="primary-contact">
                <div class="company-section-header">
                    <div class="company-section-title-wrapper">
                        <span class="company-section-status-icon">
                            @if($primaryContactComplete)
                            <i class="bi bi-check-circle-fill text-success"></i>
                            @else
                            <i class="bi bi-exclamation-circle text-warning"></i>
                            @endif
                        </span>
                        <h3 class="company-section-title">Primary Contact Information</h3>
                    </div>
                    <div class="company-section-actions">
                        @if($isEditable)
                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit-section"
                            data-section="primary-contact">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        @endif
                        <button type="button" class="btn btn-sm btn-link btn-toggle-section"
                            data-section="primary-contact">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>
                </div>
                <div class="company-section-content" data-section="primary-contact">
                    <div class="company-section-view">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div><strong>Primary Contact Name</strong></div>
                                <div>{{ $company->primary_contact_name ?: '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div><strong>Primary Contact Title</strong></div>
                                <div>{{ $company->primary_contact_title ?: '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div><strong>Primary Contact Email</strong></div>
                                <div>{{ $company->primary_contact_email ?: '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div><strong>Primary Contact Phone</strong></div>
                                <div>{{ $company->primary_contact_phone ?: '—' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="company-section-edit" style="display: none;">
                        @include('pages.employer.company.partials.primary-contact-edit', ['company' => $company])
                    </div>
                </div>
            </div>
        </div>

        @if($isEditable)
        <hr class="my-4" style="border-color: var(--borders-color);">
        <form method="POST" action="{{ route('employer.company.submit') }}" class="d-inline">
            @csrf
            <button type="submit" id="submit-for-approval-btn" class="primary-btn1" disabled
                data-mandatory-fields='["industry", "company_size", "city", "state_or_region", "registration_number", "primary_contact_name", "primary_contact_phone"]'>
                Submit for Approval
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 0L9.41 1.41L3.83 7H16V9H3.83L9.41 14.59L8 16L0 8L8 0Z" fill="currentColor" />
                </svg>
                <span></span>
            </button>
            <p class="mt-3 mb-0" style="font-family: var(--font-suse); font-size: 14px; color: var(--text-color);">Once
                submitted, your profile is locked until admin review.</p>
        </form>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Pass routes to JavaScript
    window.employerCompanyRoutes = {
        verificationDocumentUpload: '{{ route("employer.company.verification-document.upload") }}',
        logoUpload: '{{ route("employer.company.logo.upload") }}',
        photoUpload: '{{ route("employer.company.photo.upload") }}',
        photoDelete: '{{ route("employer.company.photo.delete", ["photoId" => ":photoId"]) }}',
        videoUpload: '{{ route("employer.company.video.upload") }}',
        testimonialStore: '{{ route("employer.company.testimonial.store") }}',
        testimonialDelete: '{{ route("employer.company.testimonial.delete", ["testimonialId" => ":testimonialId"]) }}',
        basicInfoUpdate: '{{ route("employer.company.basic-info.update") }}'
    };
    
    // Pass company data for validation
    window.companyData = {
        industry: @json($company->industry ?? ''),
        company_size: @json($company->company_size ?? ''),
        city: @json($company->city ?? ''),
        state_or_region: @json($company->state_or_region ?? ''),
        registration_number: @json($company->registration_number ?? ''),
        primary_contact_name: @json($company->primary_contact_name ?? ''),
        primary_contact_phone: @json($company->primary_contact_phone ?? '')
    };
</script>
<script src="{{ asset('assets/js/employer-company.js') }}"></script>
@endpush
@endsection