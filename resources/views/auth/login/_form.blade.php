<form method="POST" action="{{ route('login.otp') }}">
    @csrf

    <input type="hidden" name="user_type" value="{{ $userType ?? null }}">

    <div class="form-inner mb-20">
        <label style="color: var(--title-color);">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email', '') }}"
            placeholder="esther.nanegbe@ug.edu.gh" required autocomplete="email" autofocus
            style="border: 1px solid #E0E0E0; background: var(--white-color); border-radius: 8px;"
            onfocus="this.style.borderColor='var(--primary-color2)'" onblur="this.style.borderColor='#E0E0E0'">
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
