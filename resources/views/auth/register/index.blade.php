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
                            Create your account
                        </h2>
                        <p class="auth-form-subtitle">
                            Enter your information to get started. We'll send you a verification code to create your
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
                            <label class="auth-label">First Name</label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', '') }}"
                                placeholder="John" required autocomplete="given-name" autofocus
                                class="form-input-default" onfocus="this.classList.add('form-input-focus')"
                                onblur="this.classList.remove('form-input-focus')">
                            @error('first_name')
                            <span class="invalid-feedback auth-error-message" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-inner mb-20">
                            <label class="auth-label">Last Name</label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', '') }}"
                                placeholder="Doe" required autocomplete="family-name" class="form-input-default"
                                onfocus="this.classList.add('form-input-focus')"
                                onblur="this.classList.remove('form-input-focus')">
                            @error('last_name')
                            <span class="invalid-feedback auth-error-message" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-inner mb-20">
                            <label class="auth-label">Phone Number <span class="text-muted">(Optional)</span></label>
                            <input type="tel" name="phone_number" id="phone_number"
                                value="{{ old('phone_number', '') }}" placeholder="+233 XX XXX XXXX" autocomplete="tel"
                                class="form-input-default" onfocus="this.classList.add('form-input-focus')"
                                onblur="this.classList.remove('form-input-focus')">
                            @error('phone_number')
                            <span class="invalid-feedback auth-error-message" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-inner mb-20">
                            <label class="auth-label">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', '') }}"
                                placeholder="email@gmail.com" required autocomplete="email" class="form-input-default"
                                onfocus="this.classList.add('form-input-focus')"
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