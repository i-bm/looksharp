@extends('layouts.auth.main')

@section('content')
<!-- Login Page Start -->
<div class="login-page" style="height: 100vh; display: flex; align-items: center; background: #f5f5f5;">
    <div class="container-fluid p-0" style="height: 100%;">
        <div class="row g-0" style="height: 100%;">
            <!-- Left Panel - Dark Background -->
            <div class="col-lg-6 d-none d-lg-flex"
                style="background-image: url('{{ asset('assets/img/feature-img-3.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative; padding: 40px; display: flex; flex-direction: column; justify-content: space-between;">
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

                    <!-- Image and Testimonial -->
                    <div
                        style="flex: 1; display: flex; flex-direction: column; justify-content: flex-end; color: var(--white-color);">

                        <div style="text-align: left; width: 100%; max-width: 400px;">
                            <p
                                style="font-family: var(--font-suse); font-size: 20px; font-weight: 500; line-height: 1.6; margin-bottom: 20px; color: var(--white-color);">
                                “Looksharp changed everything.
                                One-tap applies, real companies, no more ghosting.<br>
                                Wish it existed sooner!”
                            </p>
                            <div>
                                <p
                                    style="font-family: var(--font-bricolageGrotesque); font-size: 18px; font-weight: 600; margin-bottom: 5px; color: var(--white-color);">
                                    Esther Nanegbe</p>
                                <p
                                    style="font-family: var(--font-suse); font-size: 14px; color: rgba(255, 255, 255, 0.7);">
                                    Master's IT for Business Student at University of Ghana</p>
                            </div>
                        </div>
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
                            Looksharp, get hired!
                        </h2>
                        <p
                            style="font-family: var(--font-suse); font-size: 16px; color: var(--text-color); line-height: 1.5;">
                            {{ config('app.name') }} is the go-to platform to land internships fast. Search
                            opportunities, get career advice, and connect directly with employers.
                        </p>
                    </div>

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-inner mb-20">
                            <label style="color: var(--title-color);">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', '') }}"
                                placeholder="esther.nanegbe@ug.edu.gh" required autocomplete="email" autofocus
                                style="border: 1px solid #E0E0E0; background: var(--white-color); border-radius: 8px;"
                                onfocus="this.style.borderColor='var(--primary-color2)'"
                                onblur="this.style.borderColor='#E0E0E0'">
                            @error('email')
                            <span class="invalid-feedback" role="alert"
                                style="display: block; color: var(--primary-color1); margin-top: 5px;">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-inner mb-20" style="position: relative;">
                            <label style="color: var(--title-color);">Password</label>
                            <input type="password" name="password" id="password" required
                                autocomplete="current-password" placeholder="••••••••"
                                style="border: 1px solid var(--primary-color2); background: var(--white-color); padding-right: 45px; border-radius: 8px;"
                                onfocus="this.style.borderColor='var(--primary-color2)'"
                                onblur="this.style.borderColor='var(--primary-color2)'">
                            <i class="bi bi-eye-slash" id="togglePassword"
                                style="position: absolute; right: 20px; bottom: 21px; color: var(--text-color); cursor: pointer;"></i>
                            @error('password')
                            <span class="invalid-feedback" role="alert"
                                style="display: block; color: var(--primary-color1); margin-top: 5px;">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <!-- Forgot Password and Remember Me -->
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px;">
                            <a href="{{ Route::has('password.request') ? route('password.request') : '#' }}"
                                style="color: var(--primary-color2); font-family: var(--font-suse); font-weight: 600; font-size: 16px; text-decoration: none;">
                                Forgot password?
                            </a>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <label for="remember"
                                    style="font-family: var(--font-suse); font-size: 16px; color: var(--text-color); margin: 0; cursor: pointer; order: 2;">
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
                        <button type="submit" class="primary-btn1 btn-hover"
                            style="width: 100%; justify-content: center; margin-bottom: 30px;  border: none; padding: 18px;">
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
                        <div style="text-align: center;">
                            <p
                                style="font-family: var(--font-suse); font-size: 16px; color: var(--text-color); margin: 0;">
                                Don't have an account? <a href="#"
                                    style="color: var(--primary-color2); font-weight: 600; text-decoration: none;">
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
