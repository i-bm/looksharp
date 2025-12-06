/**
 * OTP Resend Countdown Timer
 * Handles countdown timer, resend button state, and throttle error handling
 */
(function() {
    'use strict';

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        const resendSection = document.getElementById('resend-section');
        if (!resendSection) {
            return; // Exit if resend section doesn't exist
        }

        const resendBtn = document.getElementById('resend-otp-btn');
        const resendText = document.getElementById('resend-text');
        const resendCountdown = document.getElementById('resend-countdown');
        const resendError = document.getElementById('resend-error');

        // Get configuration from data attributes
        const resendRoute = resendSection.getAttribute('data-resend-route');
        const countdownSeconds = parseInt(resendSection.getAttribute('data-countdown-seconds') || '60', 10);
        const otpSentAt = resendSection.getAttribute('data-otp-sent-at');
        const throttleRemaining = parseInt(resendSection.getAttribute('data-throttle-remaining') || '0', 10);

        let countdownInterval = null;
        let remainingSeconds = 0;

        /**
         * Format seconds into MM:SS or "X minute(s)" format
         */
        function formatTime(seconds) {
            if (seconds >= 60) {
                const minutes = Math.ceil(seconds / 60);
                return minutes + ' minute' + (minutes > 1 ? 's' : '');
            }
            return seconds + ' second' + (seconds !== 1 ? 's' : '');
        }

        /**
         * Update the countdown display
         */
        function updateCountdown() {
            if (remainingSeconds > 0) {
                resendText.style.display = 'none';
                resendCountdown.style.display = 'inline';
                resendCountdown.textContent = 'Resend in ' + formatTime(remainingSeconds);
                resendBtn.disabled = true;
                resendBtn.style.cursor = 'not-allowed';
                resendBtn.style.opacity = '0.6';
                remainingSeconds--;
            } else {
                clearInterval(countdownInterval);
                resendText.style.display = 'inline';
                resendCountdown.style.display = 'none';
                resendBtn.disabled = false;
                resendBtn.style.cursor = 'pointer';
                resendBtn.style.opacity = '1';
            }
        }

        /**
         * Start the countdown timer
         */
        function startCountdown(seconds) {
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }

            remainingSeconds = seconds;
            updateCountdown();

            countdownInterval = setInterval(function() {
                updateCountdown();
            }, 1000);
        }

        /**
         * Calculate remaining countdown time based on OTP sent timestamp
         */
        function calculateRemainingCountdown() {
            if (otpSentAt) {
                try {
                    const sentTime = new Date(otpSentAt);
                    const now = new Date();
                    const elapsedSeconds = Math.floor((now - sentTime) / 1000);
                    const remaining = Math.max(0, countdownSeconds - elapsedSeconds);
                    return remaining;
                } catch (e) {
                    console.error('Error parsing OTP sent timestamp:', e);
                    return 0;
                }
            }
            return 0;
        }

        /**
         * Handle throttle error by showing countdown
         */
        function handleThrottle(remainingSeconds) {
            if (remainingSeconds > 0) {
                resendError.style.display = 'block';
                resendError.textContent = 'Please wait ' + formatTime(remainingSeconds) + ' before requesting a new code.';
                startCountdown(remainingSeconds);
            }
        }

        /**
         * Hide error message
         */
        function hideError() {
            resendError.style.display = 'none';
            resendError.textContent = '';
        }

        /**
         * Handle resend button click
         */
        function handleResendClick() {
            if (resendBtn.disabled) {
                return; // Prevent clicks during countdown
            }

            // Disable button and show loading state
            resendBtn.disabled = true;
            resendText.textContent = 'Sending...';
            hideError();

            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = resendRoute;

            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput);
            } else {
                // Fallback: try to get from existing form
                const existingForm = document.getElementById('otp-verify-form');
                if (existingForm) {
                    const existingToken = existingForm.querySelector('input[name="_token"]');
                    if (existingToken) {
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = existingToken.value;
                        form.appendChild(csrfInput);
                    }
                }
            }

            // Submit form
            document.body.appendChild(form);
            form.submit();
        }

        // Initialize countdown on page load
        if (throttleRemaining > 0) {
            // If throttled, use throttle remaining time
            handleThrottle(throttleRemaining);
        } else {
            // Otherwise, calculate from OTP sent time
            const remaining = calculateRemainingCountdown();
            if (remaining > 0) {
                startCountdown(remaining);
            }
        }

        // Attach click handler
        if (resendBtn) {
            resendBtn.addEventListener('click', handleResendClick);
        }
    });
})();

