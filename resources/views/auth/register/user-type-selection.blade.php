@extends('layouts.auth.main')

@section('content')
<!-- User Type Selection Page Start -->
<div class="login-page auth-page">
    <div class="container-fluid p-0 auth-container-fluid">
        <div class="row g-0 auth-row">
            <!-- Left Panel - Dark Background -->
            @include('auth.partials.auth-sidebar-slider')

            <!-- Right Panel - White Background with Form -->
            <div class="col-lg-6 auth-form-panel">
                <div class="auth-form-wrapper">
                    <div class="text-center mb-4 d-lg-none">
                        <img src="{{ asset('assets/img/logo-red.png') }}" alt="Logo" width="200">
                    </div>
                    <!-- Title -->
                    <div class="auth-form-header">
                        <h2 class="auth-form-title">
                            Select your account type
                        </h2>
                        <p class="auth-form-subtitle">
                            We've verified your email ({{ $email }}). Please select the type of account you want to
                            create.
                        </p>
                    </div>

                    @if(session('success'))
                    <div class="success-message">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session('info'))
                    <div class="success-message">
                        {{ session('info') }}
                    </div>
                    @endif

                    <!-- User Type Selection Form -->
                    <form method="POST" action="{{ route('register.select-type.store') }}" id="user-type-form">
                        @csrf

                        <div class="user-type-selection-container">
                            <!-- Talent Option -->
                            <label class="user-type-card" for="user_type_talent">
                                <input type="radio" name="user_type" id="user_type_talent" value="talent" required>
                                <div class="user-type-card-content">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-type-icon">
                                            <i class="bi bi-person-badge"></i>
                                        </div>
                                        <h3 class="user-type-title">Talent</h3>
                                    </div>

                                    <p class="user-type-description">
                                        I'm a student or graduate looking for internships, attachments, and job
                                        opportunities.
                                    </p>
                                </div>
                            </label>

                            <!-- Employer Option -->
                            <label class="user-type-card" for="user_type_employer">
                                <input type="radio" name="user_type" id="user_type_employer" value="employer" required>
                                <div class="user-type-card-content">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-type-icon">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <h3 class="user-type-title">Employer</h3>
                                    </div>
                                    <p class="user-type-description">
                                        I represent a company looking to hire talented students and graduates.
                                    </p>
                                </div>
                            </label>

                            <!-- University Option -->
                            <label class="user-type-card" for="user_type_university">
                                <input type="radio" name="user_type" id="user_type_university" value="university"
                                    required>
                                <div class="user-type-card-content">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-type-icon">
                                            <i class="bi bi-mortarboard"></i>
                                        </div>
                                        <h3 class="user-type-title">University</h3>
                                    </div>
                                    <p class="user-type-description">
                                        I'm from a university or institution managing student placements and career
                                        services.
                                    </p>
                                </div>
                            </label>
                        </div>

                        @error('user_type')
                        <span class="invalid-feedback auth-error-message" role="alert"
                            style="display: block; margin-top: 15px;">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                        <!-- Submit Button -->
                        <button type="submit" class="primary-btn1 btn-hover auth-form-button" id="submit-btn" disabled>
                            Continue
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- User Type Selection Page End -->
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user-type-selection.css') }}">
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('user-type-form');
        const submitBtn = document.getElementById('submit-btn');
        const radioButtons = form.querySelectorAll('input[type="radio"][name="user_type"]');
        const cards = form.querySelectorAll('.user-type-card');

        // Enable submit button when a type is selected
        radioButtons.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    submitBtn.disabled = false;
                    // Remove active class from all cards
                    cards.forEach(card => card.classList.remove('active'));
                    // Add active class to selected card
                    this.closest('.user-type-card').classList.add('active');
                }
            });
        });

        // Allow clicking on card to select
        cards.forEach(card => {
            card.addEventListener('click', function(e) {
                if (e.target.tagName !== 'INPUT') {
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                        radio.dispatchEvent(new Event('change'));
                    }
                }
            });
        });
    });
</script>
@endpush