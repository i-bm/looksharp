@extends('layouts.auth.main')

@section('content')
<div class="container" style="padding: 40px 20px; max-width: 600px; margin: 0 auto;">

    <!-- Header with Logo -->
    <div
        style="display: flex; justify-content: center; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0;">
        <a href="{{ route('dashboard') }}" style="text-decoration: none;">
            <img src="{{ asset('assets/img/logo-red.png') }}" alt="Logo" style="max-height: 40px;">
        </a>
    </div>

    <!-- Title -->
    <div style="text-align: center; margin-bottom: 30px;">
        <h2
            style="font-family: var(--font-bricolageGrotesque); font-weight: 600; font-size: 28px; color: var(--title-color); margin-bottom: 10px;">
            Verify Your Student Email
        </h2>
        <p style="color: #666; font-size: 16px;">
            We've sent a 6-digit code to <strong>{{ $studentEmail }}</strong>. Please enter it below to verify your
            student email and complete your verification.
        </p>
    </div>

    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
        {{ session('error') }}
    </div>
    @endif

    <!-- OTP Verification Form -->
    <div style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <form method="POST" action="{{ route('talent.profile.verify-student-email') }}" id="otp-verify-form">
            @csrf

            <div style="margin-bottom: 20px;">
                <label for="otp" style="display: block; margin-bottom: 8px; font-weight: 500; font-size: 16px;">
                    Verification Code <span style="color: red;">*</span>
                </label>
                <input type="text" name="otp" id="otp" value="{{ old('otp', '') }}" placeholder="000000" required
                    autocomplete="off" autofocus maxlength="6" pattern="[0-9]{6}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 18px; text-align: center; letter-spacing: 8px;"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)">
                @error('otp')
                <span style="color: red; font-size: 14px; margin-top: 8px; display: block;">
                    {{ $message }}
                </span>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="primary-btn1 btn-hover"
                style="width: 100%; padding: 12px 24px; margin-bottom: 20px;">
                Verify Email
            </button>
        </form>

        <!-- Resend OTP Section -->
        <div style="text-align: center;" id="resend-section"
            data-resend-route="{{ route('talent.profile.resend-student-verification-otp') }}"
            data-countdown-seconds="{{ $countdownSeconds ?? 60 }}" data-otp-sent-at="{{ $otpSentAt ?? '' }}">
            <p style="color: #666; margin-bottom: 10px;">
                Didn't receive the code?
            </p>
            <form method="POST" action="{{ route('talent.profile.resend-student-verification-otp') }}" id="resend-form"
                style="display: inline;">
                @csrf
                <button type="submit" id="resend-otp-btn"
                    style="background: none; border: none; color: var(--primary-color2); font-weight: 600; font-size: 16px; cursor: pointer; padding: 0;"
                    disabled>
                    <span id="resend-text">Resend code</span>
                    <span id="resend-countdown" style="display: none;"></span>
                </button>
            </form>
            <div id="resend-error"
                style="display: none; color: var(--primary-color1); margin-top: 10px; font-size: 14px;"></div>
        </div>
    </div>

    <!-- Back to Verification -->
    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('talent.profile.build.step', ['step' => 4]) }}"
            style="color: #666; text-decoration: none; font-size: 14px;">
            ← Back to verification
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const resendBtn = document.getElementById('resend-otp-btn');
        const resendText = document.getElementById('resend-text');
        const resendCountdown = document.getElementById('resend-countdown');
        const resendForm = document.getElementById('resend-form');
        const resendSection = document.getElementById('resend-section');
        const countdownSeconds = parseInt(resendSection.dataset.countdownSeconds) || 60;
        const otpSentAt = resendSection.dataset.otpSentAt;

        let countdownInterval = null;

        function startCountdown(seconds) {
            resendBtn.disabled = true;
            resendText.style.display = 'none';
            resendCountdown.style.display = 'inline';

            let remaining = seconds;
            updateCountdown(remaining);

            countdownInterval = setInterval(function() {
                remaining--;
                updateCountdown(remaining);

                if (remaining <= 0) {
                    clearInterval(countdownInterval);
                    resendBtn.disabled = false;
                    resendText.style.display = 'inline';
                    resendCountdown.style.display = 'none';
                }
            }, 1000);
        }

        function updateCountdown(seconds) {
            const minutes = Math.floor(seconds / 60);
            const secs = seconds % 60;
            resendCountdown.textContent = `Resend in ${minutes}:${secs.toString().padStart(2, '0')}`;
        }

        // Calculate remaining time if OTP was already sent
        if (otpSentAt) {
            const sentTime = new Date(otpSentAt).getTime();
            const currentTime = new Date().getTime();
            const elapsed = Math.floor((currentTime - sentTime) / 1000);
            const remaining = Math.max(0, countdownSeconds - elapsed);

            if (remaining > 0) {
                startCountdown(remaining);
            }
        }

        // Handle resend form submission
        resendForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Disable button and show loading
            resendBtn.disabled = true;
            resendText.textContent = 'Sending...';

            // Submit form
            fetch(resendForm.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: new URLSearchParams(new FormData(resendForm))
            })
            .then(response => response.text())
            .then(html => {
                // Reload page to show success message and restart countdown
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                resendBtn.disabled = false;
                resendText.textContent = 'Resend code';
                document.getElementById('resend-error').style.display = 'block';
                document.getElementById('resend-error').textContent = 'Failed to resend code. Please try again.';
            });
        });
    });
</script>
@endsection
