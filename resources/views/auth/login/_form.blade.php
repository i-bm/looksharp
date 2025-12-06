<form method="POST" action="{{ route('login.otp') }}">
    @csrf

    <input type="hidden" name="user_type" value="{{ $userType ?? null }}">

    <div class="form-inner mb-20">
        <label class="auth-label">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email', '') }}"
            placeholder="esther.nanegbe@ug.edu.gh" required autocomplete="email" autofocus
            class="form-input-default"
            onfocus="this.classList.add('form-input-focus')"
            onblur="this.classList.remove('form-input-focus')">
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
