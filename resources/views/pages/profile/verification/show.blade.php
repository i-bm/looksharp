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
            $hasDocument = !empty($profile->verification_document_url);
            $isVerified = $profile->verification_status === 'verified';
        @endphp

        <div class="talent-verification-status-card">
            <div class="talent-verification-status-left">
                <div class="talent-verification-status-title">Current status</div>
                <div class="talent-verification-status-meta">
                    @if($isVerified)
                        <span class="tv-pill tv-pill-verified"><i class="bi bi-patch-check-fill"></i> Verified</span>
                    @elseif($hasDocument)
                        <span class="tv-pill tv-pill-pending"><i class="bi bi-hourglass-split"></i> Pending review</span>
                    @else
                        <span class="tv-pill tv-pill-neutral"><i class="bi bi-shield"></i> Not started</span>
                    @endif
                </div>
            </div>
            <div class="talent-verification-status-right">
                @if($isVerified)
                    <span class="talent-verification-muted">You’re all set.</span>
                @elseif($hasDocument)
                    <span class="talent-verification-muted">We’re reviewing your document.</span>
                @else
                    <span class="talent-verification-muted">Upload a document to start verification.</span>
                @endif
            </div>
        </div>

        <div class="talent-verification-grid">
            <section class="talent-verification-card">
                <div class="talent-verification-card-header">
                    <h2 class="talent-verification-card-title">
                        <i class="bi bi-mortarboard"></i> Student verification
                    </h2>
                    <p class="talent-verification-card-subtitle">Upload your student ID and verify your student email via OTP.</p>
                </div>

                <form method="POST" action="{{ route('talent.profile.verification.student.submit') }}" enctype="multipart/form-data" class="talent-verification-form">
                    @csrf

                    <div class="tv-field">
                        <label class="tv-label" for="student_id">Student ID</label>
                        <input class="tv-input" type="text" id="student_id" name="student_id" value="{{ old('student_id', $profile->student_id) }}" placeholder="Enter your student ID">
                        @error('student_id')<div class="tv-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="tv-field">
                        <label class="tv-label" for="student_email">Student email</label>
                        <input class="tv-input" type="email" id="student_email" name="student_email" value="{{ old('student_email', $profile->student_email) }}" placeholder="name@your-school.edu">
                        @error('student_email')<div class="tv-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="tv-field">
                        <label class="tv-label" for="student_verification_document">Student ID document</label>
                        <input class="tv-input" type="file" id="student_verification_document" name="verification_document" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="tv-help">Accepted: PDF/JPG/PNG (max 5MB)</div>
                        @error('verification_document')<div class="tv-error">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="tv-btn tv-btn-primary">
                        Send OTP
                    </button>
                </form>
            </section>

            <section class="talent-verification-card">
                <div class="talent-verification-card-header">
                    <h2 class="talent-verification-card-title">
                        <i class="bi bi-person-badge"></i> Identity verification
                    </h2>
                    <p class="talent-verification-card-subtitle">Upload a Ghana Card or Passport for review.</p>
                </div>

                <form method="POST" action="{{ route('talent.profile.verification.document.submit') }}" enctype="multipart/form-data" class="talent-verification-form">
                    @csrf

                    <div class="tv-field">
                        <label class="tv-label" for="verification_type">Document type</label>
                        <select class="tv-input" id="verification_type" name="verification_type">
                            <option value="">Select document type</option>
                            <option value="ghana_card" {{ old('verification_type') === 'ghana_card' ? 'selected' : '' }}>Ghana Card</option>
                            <option value="passport" {{ old('verification_type') === 'passport' ? 'selected' : '' }}>Passport</option>
                        </select>
                        @error('verification_type')<div class="tv-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="tv-field">
                        <label class="tv-label" for="verification_document">Document</label>
                        <input class="tv-input" type="file" id="verification_document" name="verification_document" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="tv-help">Accepted: PDF/JPG/PNG (max 5MB)</div>
                        @error('verification_document')<div class="tv-error">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="tv-btn tv-btn-primary">
                        Upload for review
                    </button>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection

