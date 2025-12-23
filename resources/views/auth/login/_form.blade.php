<form method="POST" action="{{ route('login.otp') }}">
    @csrf

    <div class="form-inner mb-20">
        <label class="auth-label">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email', '') }}"
            placeholder="Enter your email address" required autocomplete="email" autofocus class="form-input-default"
            onfocus="this.classList.add('form-input-focus')" onblur="this.classList.remove('form-input-focus')">
        @error('email')
        <span class="invalid-feedback auth-error-message" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <!-- Submit Button -->
    <button type="submit" class="primary-btn1 btn-hover auth-form-button">
        Continue
    </button>
</form>