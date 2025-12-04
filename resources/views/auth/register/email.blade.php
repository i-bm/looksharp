@extends('layouts.auth.main')

@section('content')
<!-- Email Registration Page Start -->
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
                                "Looksharp changed everything.
                                One-tap applies, real companies, no more ghosting.<br>
                                Wish it existed sooner!"
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
                            Sign up with email
                        </h2>
                        <p
                            style="font-family: var(--font-suse); font-size: 16px; color: var(--text-color); line-height: 1.5;">
                            Enter your email address to get started. We'll send you a verification code.
                        </p>
                    </div>

                    @if(session('success'))
                    <div
                        style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-family: var(--font-suse); font-size: 14px;">
                        {{ session('success') }}
                    </div>
                    @endif

                    <!-- Registration Form -->
                    <form method="POST" action="{{ route('register.otp') }}">
                        @csrf

                        <input type="hidden" name="user_type" value="{{ $userType ?? 'talent' }}">

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

                        <!-- Submit Button -->
                        <button type="submit" class="primary-btn1 btn-hover"
                            style="width: 100%; justify-content: center; margin-bottom: 30px; border: none; padding: 18px;">
                            Continue
                        </button>
                    </form>

                    <!-- Back Link -->
                    <div style="text-align: center;">
                        <a href="{{ route('register', ['userType' => $displayUserType ?? 'talent']) }}"
                            style="color: var(--text-color); font-family: var(--font-suse); font-size: 16px; text-decoration: none;">
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
