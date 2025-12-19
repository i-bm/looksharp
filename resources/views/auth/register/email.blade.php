@extends('layouts.auth.main')

@section('content')
<!-- Email Registration Page Start -->
<div class="login-page auth-page">
    <div class="container-fluid p-0 auth-container-fluid">
        <div class="row g-0 auth-row">
            <!-- Left Panel - Dark Background -->
            <div class="col-lg-6 d-none d-lg-flex auth-side-panel"
                style="background-image: url('{{ asset('assets/img/feature-img-' . ($userType === 'employer' ? '4' : ($userType === 'university_admin' ? '5' : '3')) . '.jpg') }}');">
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
                            @if($userType === 'employer')
                            <p class="auth-testimonial-quote">
                                "We've found amazing talent through Looksharp. The quality of applicants is outstanding,
                                and the platform makes recruitment so much easier."
                            </p>
                            <div>
                                <p class="auth-testimonial-name">HR Manager</p>
                                <p class="auth-testimonial-title">Leading Tech Company in Accra</p>
                            </div>
                            @elseif($userType === 'university_admin')
                            <p class="auth-testimonial-quote">
                                "Looksharp has transformed how we track student placements. The platform gives us
                                real-time insights into where our students are interning and helps us connect with
                                quality employers."
                            </p>
                            <div>
                                <p class="auth-testimonial-name">Career Services Officer</p>
                                <p class="auth-testimonial-title">Partner University</p>
                            </div>
                            @else
                            <p class="auth-testimonial-quote">
                                "Looksharp changed everything.
                                One-tap applies, real companies, no more ghosting.<br>
                                Wish it existed sooner!"
                            </p>
                            <div>
                                <p class="auth-testimonial-name">Student</p>
                                <p class="auth-testimonial-title">
                                    University of Ghana</p>
                            </div>
                            @endif
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
                            Sign up with email
                        </h2>
                        <p class="auth-form-subtitle">
                            @if($userType === 'employer')
                            Enter your company email address to get started. We'll send you a verification code to
                            create your employer account.
                            @elseif($userType === 'university_admin')
                            Enter your university email address to get started. We'll send you a verification code to
                            create your university account.
                            @else
                            Enter your email address to get started. We'll send you a verification code.
                            @endif
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
                            @php
                            $emailPlaceholder = $userType === 'employer'
                            ? 'hr@company.com'
                            : ($userType === 'university_admin'
                            ? 'careerservices@university.edu.gh'
                            : 'email@gmail.com');
                            @endphp
                            <input type="email" name="email" id="email" value="{{ old('email', '') }}"
                                placeholder="{{ $emailPlaceholder }}" required autocomplete="email" autofocus
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

                    <!-- Back Link -->
                    <div class="auth-form-link-container">
                        <a href="{{ route('register', $displayUserType ?? 'talent') }}" class="auth-form-link">
                            ← Back to registration
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Email Registration Page End -->
@endsection