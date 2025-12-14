@extends('layouts.auth.main')

@section('content')
<!-- Admin Login Page Start -->
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
                                "Manage and monitor the platform<br>
                                with powerful admin tools."
                            </p>
                            <div>
                                <p class="auth-testimonial-name">Administrator Portal</p>
                                <p class="auth-testimonial-title">
                                    Secure access to system administration</p>
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
                        <h2 class="auth-form-title">Admin Portal</h2>
                        <p class="auth-form-subtitle">
                            Enter your admin email address to receive a verification code.
                        </p>
                    </div>

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('admin.login.otp') }}">
                        @csrf

                        <div class="form-inner mb-20">
                            <label class="auth-label">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', '') }}"
                                placeholder="admin@example.com" required autocomplete="email" autofocus
                                class="form-input-default" onfocus="this.classList.add('form-input-focus')"
                                onblur="this.classList.remove('form-input-focus')">
                            @error('email')
                            <span class="invalid-feedback auth-error-message" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="primary-btn1 btn-hover auth-form-button">
                            Continue
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection