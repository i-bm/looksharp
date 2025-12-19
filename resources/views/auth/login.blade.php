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
                                <p class="auth-testimonial-name">Student</p>
                                <p class="auth-testimonial-title">
                                    University of Ghana</p>
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
                            Looksharp, get hired!
                        </h2>
                        <p class="auth-form-subtitle">
                            {{ config('app.name') }} is the go-to platform to land internships fast. Search
                            opportunities, get career advice, and connect directly with employers.
                        </p>
                    </div>

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-inner mb-20">
                            <label class="auth-label">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', '') }}"
                                placeholder="esther.nanegbe@ug.edu.gh" required autocomplete="email" autofocus
                                class="form-input-default" onfocus="this.classList.add('form-input-focus')"
                                onblur="this.classList.remove('form-input-focus')">
                            @error('email')
                            <span class="invalid-feedback auth-error-message" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-inner mb-20" style="position: relative;">
                            <label class="auth-label">Password</label>
                            <input type="password" name="password" id="password" required
                                autocomplete="current-password" placeholder="••••••••" class="form-input-default"
                                style="padding-right: 45px; border-color: var(--primary-color2);"
                                onfocus="this.classList.add('form-input-focus')"
                                onblur="this.classList.remove('form-input-focus')">
                            <i class="bi bi-eye-slash" id="togglePassword"
                                style="position: absolute; right: 20px; bottom: 21px; color: var(--text-color); cursor: pointer;"></i>
                            @error('password')
                            <span class="invalid-feedback auth-error-message" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <!-- Forgot Password and Remember Me -->
                        <div class="flex justify-between items-center mb-30 gap-15" style="flex-wrap: wrap;">
                            <a href="{{ Route::has('password.request') ? route('password.request') : '#' }}"
                                class="text-primary2"
                                style="font-family: var(--font-suse); font-weight: 600; font-size: 16px; text-decoration: none;">
                                Forgot password?
                            </a>
                            <div class="flex items-center gap-10">
                                <label for="remember" class="text-text-color cursor-pointer"
                                    style="font-family: var(--font-suse); font-size: 16px; margin: 0; order: 2;">
                                    Remember sign in details
                                </label>
                                <div style="position: relative; width: 44px; height: 24px; order: 1;">
                                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked'
                                        : 'checked' }} style="position: absolute; opacity: 0; width: 0; height: 0;">
                                    <label for="remember" class="toggle-switch"
                                        style="display: block; width: 44px; height: 24px; background: var(--primary-color2); border-radius: 12px; cursor: pointer; position: relative; transition: all 0.3s;">
                                        <span
                                            style="position: absolute; top: 2px; left: 2px; width: 20px; height: 20px; background: var(--white-color); border-radius: 50%; transition: all 0.3s; transform: translateX(20px);"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Login Button -->
                        <button type="submit" class="primary-btn1 btn-hover auth-form-button">
                            Log in
                        </button>

                        <!-- Or Separator -->
                        {{-- <div style="text-align: center; margin: 30px 0; position: relative;">
                            <span
                                style="font-family: var(--font-suse); font-size: 16px; color: var(--text-color); background: var(--white-color); padding: 0 15px; position: relative; z-index: 1; display: inline-block;">
                                Or
                            </span>
                            <div
                                style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: var(--borders-color); z-index: 0;">
                            </div>
                        </div>

                        <!-- Google Button -->
                        <a href="#"
                            style="display: flex; align-items: center; justify-content: center; gap: 12px; width: 100%; padding: 16px; border: 1px solid var(--borders-color); border-radius: 10px; background: var(--white-color); text-decoration: none; transition: all 0.3s; margin-bottom: 30px;">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19.6 10.2273C19.6 9.51818 19.5364 8.83636 19.4182 8.18182H10V12.05H15.3818C15.15 13.3 14.4455 14.3591 13.3864 15.0682V17.5773H16.6182C18.5091 15.8364 19.6 13.2727 19.6 10.2273Z"
                                    fill="#4285F4" />
                                <path
                                    d="M10 20C12.7 20 14.9636 19.1045 16.6182 17.5773L13.3864 15.0682C12.4909 15.6682 11.3455 16.0227 10 16.0227C7.39545 16.0227 5.19091 14.2636 4.40455 11.9H1.06364V14.4909C2.70909 17.7591 6.09091 20 10 20Z"
                                    fill="#34A853" />
                                <path
                                    d="M4.40455 11.9C4.20455 11.3 4.09091 10.6591 4.09091 10C4.09091 9.34091 4.20455 8.7 4.40455 8.1V5.50909H1.06364C0.386364 6.85909 0 8.38636 0 10C0 11.6136 0.386364 13.1409 1.06364 14.4909L4.40455 11.9Z"
                                    fill="#FBBC05" />
                                <path
                                    d="M10 3.97727C11.4682 3.97727 12.7864 4.48182 13.8227 5.47273L16.6909 2.60455C14.9591 0.990909 12.6955 0 10 0C6.09091 0 2.70909 2.24091 1.06364 5.50909L4.40455 8.1C5.19091 5.73636 7.39545 3.97727 10 3.97727Z"
                                    fill="#EA4335" />
                            </svg>
                            <span
                                style="font-family: var(--font-suse); font-size: 16px; font-weight: 600; color: var(--title-color);">Continue
                                with Google</span>
                        </a> --}}

                        <!-- Sign Up Link -->
                        <div class="auth-form-link-container">
                            <p class="auth-form-link mb-0">
                                Don't have an account? <a href="#" class="auth-form-link-primary">
                                    Sign up
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle password visibility
    document.getElementById('togglePassword')?.addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this;

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    });

    // Toggle switch functionality
    document.getElementById('remember')?.addEventListener('change', function() {
        const toggleLabel = document.querySelector('.toggle-switch');
        const span = toggleLabel?.querySelector('span');
        if (this.checked) {
            if (span) span.style.transform = 'translateX(20px)';
            if (toggleLabel) toggleLabel.style.background = 'var(--primary-color2)';
        } else {
            if (span) span.style.transform = 'translateX(0)';
            if (toggleLabel) toggleLabel.style.background = '#ccc';
        }
    });
</script>
<!-- Login Page End -->
@endsection