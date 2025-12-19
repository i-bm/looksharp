@extends('layouts.auth.main')

@section('content')
<!-- Registration Page Start -->
<div class="login-page auth-page">
    <div class="container-fluid p-0 auth-container-fluid">
        <div class="row g-0 auth-row">
            <!-- Left Panel - Dark Background -->
            <div class="col-lg-6 d-none d-lg-flex auth-side-panel"
                style="background-image: url('{{ asset('assets/img/feature-img-4.jpg') }}');">
                <!-- Dark Overlay for better text readability at bottom -->
                <div class="auth-side-panel-overlay"></div>
                <div class="auth-side-panel-content">
                    <!-- Logo -->
                    <div>
                        <img src="{{ asset('assets/img/logo-white.png') }}" alt="Logo" class="auth-logo">
                    </div>

                    <!-- Image and Testimonial -->
                    <div class="auth-testimonial-container">
                        <div class="auth-testimonial-wrapper">
                            <p class="auth-testimonial-quote">
                                "Looksharp has transformed our hiring process.
                                We connect with top talent faster and more efficiently than ever before."
                            </p>
                            <div>
                                <p class="auth-testimonial-name">HR Team</p>
                                <p class="auth-testimonial-title">
                                    Leading Tech Company in Ghana</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel - White Background with Form -->
            <div class="col-lg-6 auth-form-panel">
                <div class="auth-form-wrapper">
                    <div class="text-center mb-4 d-lg-none">
                        <img src="{{ asset('assets/img/logo-red.png') }}" alt="Logo" width="200">
                    </div>
                    <!-- Title -->
                    <div class="auth-form-header">
                        <h2 class="auth-form-title">
                            Create your employer account
                        </h2>
                        <p class="auth-form-subtitle">
                            Join leading companies in Ghana to find the best talent for your internships, attachments,
                            and entry-level positions.
                        </p>
                    </div>

                    <!-- Registration Method Selection -->
                    <div style="margin-bottom: 30px;">
                        <!-- Email Registration Button -->
                        <a href="{{ route('register.email') }}" class="primary-btn1 btn-hover auth-form-button"
                            style="text-decoration: none; display: block; text-align: center;">
                            Continue with Email
                        </a>

                        <!-- Future: Google and Apple buttons (commented out for now) -->
                        <!--
                        <button type="button" class="primary-btn2 btn-hover auth-form-button-secondary">
                            Continue with Google
                        </button>

                        <button type="button" class="primary-btn2 btn-hover auth-form-button-secondary">
                            Continue with Apple
                        </button>

                        <div class="text-center" style="margin: 20px 0;">
                            <span class="text-text-color" style="font-family: var(--font-suse); font-size: 14px;">OR</span>
                        </div>

                        <a href="{{ route('register.email') }}"
                            class="primary-btn2 btn-hover auth-form-button-secondary">
                            Continue with Phone Number
                        </a>
                        -->
                    </div>

                    <!-- Login Link -->
                    <div class="auth-form-link-container">
                        <p class="auth-form-link mb-0">
                            Already have an account? <a href="{{ route('login', $displayUserType ?? 'employer') }}"
                                class="auth-form-link-primary">
                                Sign in
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Registration Page End -->
@endsection