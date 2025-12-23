@extends('layouts.auth.main')

@section('content')
<!-- Email Registration Page Start -->
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
                            Sign up with email1
                        </h2>
                        <p class="auth-form-subtitle">
                            Enter your email address to get started. We'll send you a verification code to create your
                            account.
                        </p>
                    </div>

                    @if(session('success'))
                    <div class="success-message">
                        {{ session('success') }}
                    </div>
                    @endif

                    <!-- Registration Form -->
                    <form method="POST" action="{{ route('register.otp') }}">
                        @csrf

                        <div class="form-inner mb-20">
                            <label class="auth-label">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', '') }}"
                                placeholder="email@gmail.com" required autocomplete="email" autofocus
                                class="form-input-default" onfocus="this.classList.add('form-input-focus')"
                                onblur="this.classList.remove('form-input-focus')">
                            @error('email')
                            <span class="invalid-feedback auth-error-message" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <div class="form-inner2 two">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        name="consent_to_privacy_policy" id="consent_to_privacy_policy" required>
                                    <label class="form-check-label" for="consent_to_privacy_policy">
                                        I consent to my data being processed according to the
                                        <a href="#" target="_blank">privacy policy</a>
                                    </label>
                                </div>
                                @error('consent_to_privacy_policy')
                                <span class="invalid-feedback auth-error-message" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <!-- Submit Button -->
                        <button type="submit" class="primary-btn1 btn-hover auth-form-button">
                            Continue
                        </button>
                    </form>

                    <!-- Login Link -->
                    <div class="auth-form-link-container">
                        <p class="auth-form-link mb-0">
                            Already have an account? <a href="{{ route('login') }}" class="auth-form-link-primary">
                                Sign in
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Email Registration Page End -->
@endsection