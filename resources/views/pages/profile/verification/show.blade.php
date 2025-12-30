@extends('layouts.dashboard.main')

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/talent-verification.css') }}">
@endpush

<div class="talent-verification-page">
    <div class="talent-verification-container">
        <div class="talent-verification-header">
            <div>
                <h1 class="talent-verification-title">Verification</h1>
                <p class="talent-verification-subtitle">Get verified to build trust with employers.</p>
            </div>
            <a href="{{ route('talent.profile.show') }}" class="talent-verification-back">
                <i class="bi bi-arrow-left"></i>
                Back to profile
            </a>
        </div>

        @if(session('success'))
            <div class="talent-verification-alert talent-verification-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="talent-verification-alert talent-verification-alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->has('error'))
            <div class="talent-verification-alert talent-verification-alert-error">
                {{ $errors->first('error') }}
            </div>
        @endif

        @php
            $status = $profile->verification_status ?: 'not_started';
            $hasStudentDocument = !empty($profile->student_verification_document_url);
            $hasIdentityDocument = !empty($profile->verification_document_url);
            $hasBothDocuments = $hasStudentDocument && $hasIdentityDocument;
            $isVerified = $profile->verification_status === 'verified';
            $isPending = $profile->verification_status === 'pending';
            $canEdit = in_array($status, [null, 'not_started', 'rejected']);
        @endphp

        <div class="talent-verification-status-card">
            <div class="talent-verification-status-left">
                <div class="talent-verification-status-title">Current status</div>
                <div class="talent-verification-status-meta">
                    @if($isVerified)
                        <span class="tv-pill tv-pill-verified"><i class="bi bi-patch-check-fill"></i> Verified</span>
                    @elseif($isPending || $hasBothDocuments)
                        <span class="tv-pill tv-pill-pending"><i class="bi bi-hourglass-split"></i> Pending review</span>
                    @else
                        <span class="tv-pill tv-pill-neutral"><i class="bi bi-shield"></i> Not started</span>
                    @endif
                </div>
            </div>
            <div class="talent-verification-status-right">
                @if($isVerified)
                    <span class="talent-verification-muted">You're all set.</span>
                @elseif($isPending || $hasBothDocuments)
                    <span class="talent-verification-muted">We're reviewing your documents.</span>
                @else
                    <span class="talent-verification-muted">Submit both verifications to start review.</span>
                @endif
            </div>
        </div>

        <div class="talent-verification-grid">
            <section class="talent-verification-card">
                <div class="talent-verification-card-header">
                    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                        <div>
                            <h2 class="talent-verification-card-title">
                                <i class="bi bi-mortarboard"></i> Student verification
                            </h2>
                            <p class="talent-verification-card-subtitle">Provide your current student ID number and a supporting document to verify your enrollment.</p>
                        </div>
                        @if($hasStudentDocument)
                            <span class="tv-pill tv-pill-success" style="background: rgba(16, 185, 129, 0.12); color: #065f46; border-color: rgba(16, 185, 129, 0.3);">
                                <i class="bi bi-check-circle"></i> Submitted
                            </span>
                        @endif
                    </div>
                </div>

                @if($canEdit)
                    <form method="POST" action="{{ route('talent.profile.verification.student.submit') }}" enctype="multipart/form-data" class="talent-verification-form">
                        @csrf

                        <div class="tv-field">
                            <label class="tv-label" for="student_id">Student ID Number</label>
                            <input class="tv-input" type="text" id="student_id" name="student_id" value="{{ old('student_id', $profile->student_id) }}" placeholder="e.g. 1234567" {{ !$canEdit ? 'disabled' : '' }}>
                            @error('student_id')<div class="tv-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="tv-field">
                            <label class="tv-label" for="student_email">Student Email</label>
                            <input class="tv-input" type="email" id="student_email" name="student_email" value="{{ old('student_email', $profile->student_email) }}" placeholder="e.g. student@university.edu.gh" {{ !$canEdit ? 'disabled' : '' }}>
                            @error('student_email')<div class="tv-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="tv-field">
                            <label class="tv-label" for="student_verification_document">Upload Student ID Document</label>
                            <input class="tv-input" type="file" id="student_verification_document" name="verification_document" accept=".pdf,.jpg,.jpeg,.png" {{ !$canEdit ? 'disabled' : '' }}>
                            <div class="tv-help">PDF, JPG, PNG up to 5MB</div>
                            @error('verification_document')<div class="tv-error">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="tv-btn tv-btn-primary" {{ !$canEdit ? 'disabled' : '' }}>
                            Submit
                        </button>
                    </form>
                @else
                    <div class="talent-verification-form">
                        <div class="tv-field">
                            <label class="tv-label">Student ID Number</label>
                            <div class="tv-input tv-input-readonly">{{ $profile->student_id ?? 'Not provided' }}</div>
                        </div>

                        <div class="tv-field">
                            <label class="tv-label">Student Email</label>
                            <div class="tv-input tv-input-readonly">{{ $profile->student_email ?? 'Not provided' }}</div>
                        </div>

                        <div class="tv-field">
                            <label class="tv-label">Student ID Document</label>
                            @if($hasStudentDocument)
                                <div class="tv-document-preview">
                                    <a href="{{ route('talent.profile.verification.document.download', ['type' => 'student']) }}" target="_blank" class="tv-document-link">
                                        <i class="bi bi-file-earmark-pdf"></i> View Document
                                    </a>
                                </div>
                            @else
                                <div class="tv-input tv-input-readonly">No document uploaded</div>
                            @endif
                        </div>
                    </div>
                @endif
            </section>

            <section class="talent-verification-card">
                <div class="talent-verification-card-header">
                    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                        <div>
                            <h2 class="talent-verification-card-title">
                                <i class="bi bi-person-badge"></i> Identity verification
                            </h2>
                            <p class="talent-verification-card-subtitle">Upload a government-issued ID (Ghana Card or Passport) for identity review.</p>
                        </div>
                        @if($hasIdentityDocument)
                            <span class="tv-pill tv-pill-success" style="background: rgba(16, 185, 129, 0.12); color: #065f46; border-color: rgba(16, 185, 129, 0.3);">
                                <i class="bi bi-check-circle"></i> Submitted
                            </span>
                        @endif
                    </div>
                </div>

                @if($canEdit)
                    <form method="POST" action="{{ route('talent.profile.verification.document.submit') }}" enctype="multipart/form-data" class="talent-verification-form">
                        @csrf

                        <div class="tv-field">
                            <label class="tv-label" for="verification_type">Document Type</label>
                            <select class="tv-input" id="verification_type" name="verification_type" {{ !$canEdit ? 'disabled' : '' }}>
                                <option value="">Select document type</option>
                                <option value="ghana_card" {{ old('verification_type', $profile->verification_type) === 'ghana_card' ? 'selected' : '' }}>Ghana Card</option>
                                <option value="passport" {{ old('verification_type', $profile->verification_type) === 'passport' ? 'selected' : '' }}>Passport</option>
                            </select>
                            @error('verification_type')<div class="tv-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="tv-field">
                            <label class="tv-label" for="identity_id_number">
                                <span id="identity_id_label">
                                    @if(old('verification_type', $profile->verification_type) === 'ghana_card')
                                        Ghana Card Number
                                    @elseif(old('verification_type', $profile->verification_type) === 'passport')
                                        Passport Number
                                    @else
                                        ID Number
                                    @endif
                                </span>
                            </label>
                            <input class="tv-input" type="text" id="identity_id_number" name="identity_document_number" value="{{ old('identity_document_number', $profile->identity_document_number) }}" placeholder="Enter ID number" {{ !$canEdit ? 'disabled' : '' }}>
                            @error('identity_document_number')<div class="tv-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="tv-field">
                            <label class="tv-label" for="verification_document">Upload Identity Document</label>
                            <input class="tv-input" type="file" id="verification_document" name="verification_document" accept=".pdf,.jpg,.jpeg,.png" {{ !$canEdit ? 'disabled' : '' }}>
                            <div class="tv-help">PDF, JPG, PNG up to 5MB</div>
                            @error('verification_document')<div class="tv-error">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="tv-btn tv-btn-primary" {{ !$canEdit ? 'disabled' : '' }}>
                            Submit
                        </button>
                    </form>
                @else
                    <div class="talent-verification-form">
                        <div class="tv-field">
                            <label class="tv-label">Document Type</label>
                            <div class="tv-input tv-input-readonly">
                                {{ $profile->verification_type ? ucfirst(str_replace('_', ' ', $profile->verification_type)) : 'Not provided' }}
                            </div>
                        </div>

                        <div class="tv-field">
                            <label class="tv-label">
                                @if($profile->verification_type === 'ghana_card')
                                    Ghana Card Number
                                @elseif($profile->verification_type === 'passport')
                                    Passport Number
                                @else
                                    ID Number
                                @endif
                            </label>
                            <div class="tv-input tv-input-readonly">{{ $profile->identity_document_number ?? 'Not provided' }}</div>
                        </div>

                        <div class="tv-field">
                            <label class="tv-label">Identity Document</label>
                            @if($hasIdentityDocument)
                                <div class="tv-document-preview">
                                    <a href="{{ route('talent.profile.verification.document.download', ['type' => 'identity']) }}" target="_blank" class="tv-document-link">
                                        <i class="bi bi-file-earmark-pdf"></i> View Document
                                    </a>
                                </div>
                            @else
                                <div class="tv-input tv-input-readonly">No document uploaded</div>
                            @endif
                        </div>
                    </div>
                @endif
            </section>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--borders-color);">
            <div style="display: flex; align-items: center; gap: 8px; color: var(--text-color); font-size: 12px;">
                <i class="bi bi-info-circle"></i>
                <span>Your information is securely stored and only used for verification purposes.</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update ID number label based on document type selection
    const verificationTypeSelect = document.getElementById('verification_type');
    const identityIdLabel = document.getElementById('identity_id_label');
    const identityIdInput = document.getElementById('identity_id_number');

    if (verificationTypeSelect && identityIdLabel && identityIdInput) {
        verificationTypeSelect.addEventListener('change', function() {
            const selectedType = this.value;

            if (selectedType === 'ghana_card') {
                identityIdLabel.textContent = 'Ghana Card Number';
                identityIdInput.placeholder = 'Enter Ghana Card number';
            } else if (selectedType === 'passport') {
                identityIdLabel.textContent = 'Passport Number';
                identityIdInput.placeholder = 'Enter Passport number';
            } else {
                identityIdLabel.textContent = 'ID Number';
                identityIdInput.placeholder = 'Enter ID number';
            }
        });
    }
});
</script>
@endpush
@endsection

