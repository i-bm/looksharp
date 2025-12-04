@extends('layouts.auth.main')

@section('content')
<!-- Registration Page Start -->
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
                            Create your account
                        </h2>
                        <p
                            style="font-family: var(--font-suse); font-size: 16px; color: var(--text-color); line-height: 1.5;">
                            Join thousands of students and graduates finding their dream internships and early career
                            opportunities.
                        </p>
                    </div>

                    <!-- Registration Method Selection -->
                    <div style="margin-bottom: 30px;">
                        <!-- Email Registration Button -->
                        <a href="{{ route('register.email', ['user_type' => $userType ?? 'talent']) }}"
                            class="primary-btn1 btn-hover"
                            style="width: 100%; justify-content: center; margin-bottom: 15px; border: none; padding: 18px; text-decoration: none; display: block; text-align: center;">
                            Continue with Email
                        </a>

                        <!-- Future: Google and Apple buttons (commented out for now) -->
                        <!--
                        <button type="button" class="primary-btn2 btn-hover"
                            style="width: 100%; justify-content: center; margin-bottom: 15px; border: 1px solid #E0E0E0; padding: 18px;">
                            Continue with Google
                        </button>

                        <button type="button" class="primary-btn2 btn-hover"
                            style="width: 100%; justify-content: center; margin-bottom: 15px; border: 1px solid #E0E0E0; padding: 18px;">
                            Continue with Apple
                        </button>

                        <div style="text-align: center; margin: 20px 0;">
                            <span style="color: var(--text-color); font-family: var(--font-suse); font-size: 14px;">OR</span>
                        </div>

                        <a href="{{ route('register.email', ['user_type' => $userType ?? 'talent']) }}"
                            class="primary-btn2 btn-hover"
                            style="width: 100%; justify-content: center; margin-bottom: 15px; border: 1px solid #E0E0E0; padding: 18px; text-decoration: none; display: block; text-align: center;">
                            Continue with Phone Number
                        </a>
                        -->
                    </div>

                    <!-- Login Link -->
                    <div style="text-align: center;">
                        <p style="font-family: var(--font-suse); font-size: 16px; color: var(--text-color); margin: 0;">
                            Already have an account? <a
                                href="{{ route('login', ['userType' => $displayUserType ?? 'talent']) }}"
                                style="color: var(--primary-color2); font-weight: 600; text-decoration: none;">
                                Sign in
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Registration Page End -->
@endsection
