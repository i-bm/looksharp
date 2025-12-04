@extends('layouts.auth.main')

@section('content')
<!-- Login Page Start -->
<div class="login-page" style="height: 100vh; display: flex; align-items: center; background: #f5f5f5;">
    <div class="container-fluid p-0" style="height: 100%;">
        <div class="row g-0" style="height: 100%;">
            <!-- Left Panel - Dark Background -->
            <div class="col-lg-6 d-none d-lg-flex"
                style="background-image: url('{{ asset('assets/img/feature-img-4.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative; padding: 40px; display: flex; flex-direction: column; justify-content: space-between;">
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
                                "We've found amazing talent through Looksharp. The quality of applicants is outstanding,
                                and the platform makes recruitment so much easier."
                            </p>
                            <div>
                                <p
                                    style="font-family: var(--font-bricolageGrotesque); font-size: 18px; font-weight: 600; margin-bottom: 5px; color: var(--white-color);">
                                    HR Manager</p>
                                <p
                                    style="font-family: var(--font-suse); font-size: 14px; color: rgba(255, 255, 255, 0.7);">
                                    Leading Tech Company in Accra</p>
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
                            Find the best talent
                        </h2>
                        <p
                            style="font-family: var(--font-suse); font-size: 16px; color: var(--text-color); line-height: 1.5;">
                            Post internships and entry-level positions, connect with verified students and graduates,
                            and build your team with Ghana's top talent.
                        </p>
                    </div>

                    <!-- Login Form -->
                    @include('auth.login._form', ['userType' => $userType ?? 'employer'])

                    <!-- Sign Up Link -->
                    <div style="text-align: center;">
                        <p style="font-family: var(--font-suse); font-size: 16px; color: var(--text-color); margin: 0;">
                            Don't have an account? <a href="#"
                                style="color: var(--primary-color2); font-weight: 600; text-decoration: none;">
                                Sign up
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Login Page End -->
@endsection
