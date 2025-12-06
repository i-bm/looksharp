@extends('layouts.auth.main')

@section('content')
<!-- Login Page Start -->
<div class="login-page auth-page">
    <div class="container-fluid p-0 auth-container-fluid">
        <div class="row g-0 auth-row">
            <!-- Left Panel - Dark Background -->
            <div class="col-lg-6 d-none d-lg-flex auth-side-panel"
                style="background-image: url('{{ asset('assets/img/feature-img-3.jpg') }}');">
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
                                "Looksharp changed everything.
                                One-tap applies, real companies, no more ghosting.<br>
                                Wish it existed sooner!"
                            </p>
                            <div>
                                <p class="auth-testimonial-name">Esther Nanegbe</p>
                                <p class="auth-testimonial-title">
                                    Master's IT for Business Student at University of Ghana</p>
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
                        <h2 class="auth-form-title">Looksharp, get hired!</h2>
                        <p class="auth-form-subtitle">
                            {{ config('app.name') }} is the go-to platform to land internships fast. Search
                            opportunities, get career advice, and connect directly with employers.
                        </p>
                    </div>

                    <!-- Login Form -->
                    @include('auth.login._form', ['userType' => $userType ?? 'talent'])

                    <!-- Sign Up Link -->
                    <div class="auth-form-link-container">
                        <p class="auth-form-link mb-0">
                            Don't have an account? <a href="{{ route('register', ['userType' => 'talent']) }}"
                                class="auth-form-link-primary">
                                Sign up
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection