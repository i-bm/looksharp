@extends('layouts.auth.main')

@section('content')
<!-- Login Page Start -->
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
                                "We've found amazing talent through Looksharp. The quality of applicants is outstanding,
                                and the platform makes recruitment so much easier."
                            </p>
                            <div>
                                <p class="auth-testimonial-name">HR Manager</p>
                                <p class="auth-testimonial-title">Leading Tech Company in Accra</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel - White Background with Form -->
            <div class="col-lg-6 auth-form-panel">
                <div class="auth-form-wrapper">
                    <!-- Title -->
                    <div class="auth-form-header">
                        <h2 class="auth-form-title">
                            Find the best talent
                        </h2>
                        <p class="auth-form-subtitle">
                            Post internships and entry-level positions, connect with verified students and graduates,
                            and build your team with Ghana's top talent.
                        </p>
                    </div>

                    <!-- Login Form -->
                    @include('auth.login._form', ['userType' => $userType ?? 'employer'])

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
