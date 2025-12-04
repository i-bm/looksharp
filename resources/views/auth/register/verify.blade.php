@extends('layouts.auth.main')

@section('content')
<!-- OTP Verification Page Start -->
<div class="login-page" style="height: 100vh; display: flex; align-items: center; background: #f5f5f5;">
    <div class="container-fluid p-0" style="height: 100%;">
        <div class="row g-0" style="height: 100%;">
            <!-- Left Panel - Dark Background -->
            <div class="col-lg-6 d-none d-lg-flex"
                style="background-image: url('{{ asset('assets/img/feature-img-6.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative; padding: 40px; display: flex; flex-direction: column; justify-content: space-between;">
                <!-- Dark Overlay for better text readability at bottom -->
                <div
                    style="position: absolute; bottom: 0; left: 0; right: 0; height: 40%; background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.7) 50%, transparent 100%); z-index: 0;">
                </div>
                <div
                    style="position: relative; z-index: 1; height: 100%; display: flex; flex-direction: column; justify-content: space-between;">
                    <!-- Logo -->
                    <div>
                        <img src="{{ asset('assets/img/logo-white.png') }}" alt="Logo" style="max-width: 150px;">
                    </div>
                </div>
            </div>

            <!-- Right Panel - White Background with Form -->
            <div class="col-lg-6"
                style="background: var(--white-color); padding: 40px 20px; display: flex; align-items: center; justify-content: center; min-height: 100vh;">
                <div style="max-width: 450px; width: 100%;">
                    <!-- Title -->
                    <div style="margin-bottom: 40px;">
                        <h2
                            style="font-family: var(--font-bricolageGrotesque); font-weight: 600; font-size: 32px; color: var(--title-color); margin-bottom: 12px; line-height: 1.2;">
                            Enter verification code
                        </h2>
                        <p
                            style="font-family: var(--font-suse); font-size: 16px; color: var(--text-color); line-height: 1.5;">
                            We've sent a 6-digit code to <strong>{{ $email }}</strong>. Please enter it below to
                            complete your registration.
                        </p>
                    </div>

                    @if(session('success'))
                    <div
                        style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-family: var(--font-suse); font-size: 14px;">
                        {{ session('success') }}
                    </div>
                    @endif

                    <!-- OTP Verification Form -->
                    <form method="POST" action="{{ route('register.verify') }}">
                        @csrf

                        <input type="hidden" name="email" value="{{ $email }}">
                        <input type="hidden" name="user_type" value="{{ $userType ?? 'talent' }}">

                        <div class="form-inner mb-20">
                            <label style="color: var(--title-color);">Verification Code</label>
                            <input type="text" name="otp" id="otp" value="{{ old('otp', '') }}" placeholder="000000"
                                required autocomplete="off" autofocus maxlength="6" pattern="[0-9]{6}"
                                style="border: 1px solid #E0E0E0; background: var(--white-color); border-radius: 8px; text-align: center; font-size: 24px; letter-spacing: 8px; font-weight: 600;"
                                onfocus="this.style.borderColor='var(--primary-color2)'"
                                onblur="this.style.borderColor='#E0E0E0'"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)">
                            @error('otp')
                            <span class="invalid-feedback" role="alert"
                                style="display: block; color: var(--primary-color1); margin-top: 5px;">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="primary-btn1 btn-hover"
                            style="width: 100%; justify-content: center; margin-bottom: 20px; border: none; padding: 18px;">
                            Verify & Create Account
                        </button>

                        <!-- Resend OTP -->
                        <div style="text-align: center; margin-bottom: 20px;">
                            <p
                                style="font-family: var(--font-suse); font-size: 16px; color: var(--text-color); margin: 0;">
                                Didn't receive the code?
                            <form method="POST" action="{{ route('register.otp') }}" style="display: inline;">
                                @csrf
                                <input type="hidden" name="email" value="{{ $email }}">
                                <input type="hidden" name="user_type" value="{{ $userType ?? 'talent' }}">
                                <button type="submit"
                                    style="background: none; border: none; color: var(--primary-color2); font-weight: 600; text-decoration: none; cursor: pointer; font-family: var(--font-suse); font-size: 16px; padding: 0;">
                                    Resend code
                                </button>
                            </form>
                            </p>
                        </div>

                        <!-- Back to Registration -->
                        <div style="text-align: center;">
                            <a href="{{ route('register.email', ['user_type' => $userType ?? 'talent']) }}"
                                style="color: var(--text-color); font-family: var(--font-suse); font-size: 16px; text-decoration: none;">
                                ← Back to registration
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- OTP Verification Page End -->
@endsection

