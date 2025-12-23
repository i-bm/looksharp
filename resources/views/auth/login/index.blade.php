@extends('layouts.auth.main')

@section('content')
<!-- Login Page Start -->
<div class="login-page auth-page">
    <div class="container-fluid p-0 auth-container-fluid">
        <div class="row g-0 auth-row">
            <!-- Left Panel - Slider with Multiple User Types -->
            @include('auth.partials.auth-sidebar-slider')
            <!-- Right Panel - White Background with Form -->
            <div class="col-lg-6 auth-form-panel">
                <div class="auth-form-wrapper">
                    <div class="text-center mb-4 d-lg-none">
                        <img src="{{ asset('assets/img/logo-red.png') }}" alt="Logo" width="200">
                    </div>
                    <!-- Title -->
                    <div class="auth-form-header">
                        <h2 class="auth-form-title">Welcome back!</h2>
                        <p class="auth-form-subtitle">
                            Enter your email address to continue. We'll send you a verification code to sign in.
                        </p>
                    </div>

                    <!-- Login Form -->
                    @include('auth.login._form')

                    <!-- Sign Up Link -->
                    <div class="auth-form-link-container">
                        <p class="auth-form-link mb-0">
                            Don't have an account? <a href="{{ route('register') }}" class="auth-form-link-primary">
                                Sign up
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection