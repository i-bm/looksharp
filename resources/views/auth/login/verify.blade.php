@extends('layouts.auth.main')

@section('content')
<!-- OTP Verification Page Start -->
<div class="login-page auth-page">
    <div class="container-fluid p-0 auth-container-fluid">
        <div class="row g-0 auth-row">
            <!-- Left Panel - Dark Background -->
            <div class="col-lg-6 d-none d-lg-flex auth-side-panel"
                style="background-image: url('{{ asset('assets/img/feature-img-6.jpg') }}'); padding: 40px;">
                <!-- Dark Overlay for better text readability at bottom -->
                <div class="auth-side-panel-overlay"></div>
                <div class="auth-side-panel-content">
                    <!-- Logo -->
                    <div>
                        <img src="{{ asset('assets/img/logo-white.png') }}" alt="Logo" class="auth-logo">
                    </div>
                </div>
            </div>

            <!-- Right Panel - White Background with Form -->
            <div class="col-lg-6 auth-form-panel">
                <div class="auth-form-wrapper">
                    <!-- Title -->
                    <div class="auth-form-header">
                        <h2 class="auth-form-title">
                            Enter verification code
                        </h2>
                        <p class="auth-form-subtitle">
                            We've sent a 6-digit code to <strong>{{ $email }}</strong>. Please enter it below to
                            continue.
                        </p>
                    </div>

                    @if(session('success'))
                    <div class="success-message">
                        {{ session('success') }}
                    </div>
                    @endif

                    <!-- OTP Verification Form -->
                    <form method="POST" action="{{ route('login.verify') }}" id="otp-verify-form">
                        @csrf

                        <input type="hidden" name="email" value="{{ $email }}">

                        <div class="form-inner mb-20">
                            <label class="auth-label">Verification Code</label>
                            <input type="text" name="otp" id="otp" value="{{ old('otp', '') }}" placeholder="000000"
                                required autocomplete="off" autofocus maxlength="6" pattern="[0-9]{6}"
                                class="auth-otp-input" onfocus="this.classList.add('form-input-focus')"
                                onblur="this.classList.remove('form-input-focus')"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)">
                            @error('otp')
                            <span class="invalid-feedback auth-error-message" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="primary-btn1 btn-hover auth-form-button"
                            style="margin-bottom: 20px;">
                            Verify & Continue
                        </button>
                    </form>

                    <!-- Resend OTP Section (outside main form) -->
                    <div class="text-center" style="margin-bottom: 20px;" id="resend-section"
                        data-resend-route="{{ route('login.otp') }}"
                        data-countdown-seconds="{{ $countdownSeconds ?? 60 }}" data-otp-sent-at="{{ $otpSentAt ?? '' }}"
                        data-throttle-remaining="{{ isset($throttleInfo['remaining_seconds']) ? $throttleInfo['remaining_seconds'] : 0 }}">
                        <p class="auth-form-link mb-0">
                            Didn't receive the code?
                            <button type="button" id="resend-otp-btn" class="border-none bg-white cursor-pointer"
                                style="color: var(--primary-color2); font-weight: 600; font-family: var(--font-suse); font-size: 16px; padding: 0;">
                                <span id="resend-text">Resend code</span>
                                <span id="resend-countdown" class="hidden"></span>
                            </button>
                        </p>
                        <div id="resend-error" class="hidden"
                            style="color: var(--primary-color1); margin-top: 10px; font-size: 14px;">
                        </div>
                    </div>

                    <!-- Back to Login -->
                    <div class="auth-form-link-container">
                        <a href="{{ route('login') }}" class="auth-form-link">
                            ← Back to login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- OTP Verification Page End -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/otp-resend.js') }}"></script>
@endpush