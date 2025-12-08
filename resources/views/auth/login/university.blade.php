@extends('layouts.auth.main')

@section('content')
<!-- Login Page Start -->
<div class="login-page auth-page">
    <div class="container-fluid p-0 auth-container-fluid">
        <div class="row g-0 auth-row">
            <!-- Left Panel - Dark Background -->
            <div class="col-lg-6 d-none d-lg-flex auth-side-panel"
                style="background-image: url('{{ asset('assets/img/feature-img-5.jpg') }}');">
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
                                "Looksharp has transformed how we track student placements. The platform gives us
                                real-time insights into where our students are interning and helps us connect with
                                quality employers."
                            </p>
                            <div>
                                <p class="auth-testimonial-name">Career Services Officer</p>
                                <p class="auth-testimonial-title">Partner University</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel - White Background with Form -->
            <div class="col-lg-6 auth-form-panel">
                <div class="auth-form-wrapper">
                    <div class="auth-form-logo mb-5 text-center d-block d-lg-none">
                        <img src="{{ asset('assets/img/logo-red.png') }}" width="180" alt="Logo"
                            class="auth-form-logo-img">
                    </div>
                    <!-- Title -->
                    <div class="auth-form-header">
                        <h2 class="auth-form-title">
                            Manage student placements
                        </h2>
                        <p class="auth-form-subtitle">
                            Track student internships, connect with verified employers, access placement analytics, and
                            promote your institution to top companies.
                        </p>
                    </div>

                    <!-- Login Form -->
                    @include('auth.login._form', ['userType' => $userType ?? 'university_admin'])

                    <!-- Sign Up Link -->
                    <div class="auth-form-link-container">
                        <p class="auth-form-link mb-0">
                            Don't have an account? <a href="#" class="auth-form-link-primary">
                                Sign up
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Login Page End -->
@endsection