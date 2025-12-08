<h2 style="font-family: var(--font-bricolageGrotesque); font-weight: 600; font-size: 24px; margin-bottom: 30px;">
    Verification
</h2>

<p style="color: #666;">
    Upload a verification document to apply for paid opportunities. Accepted documents: Ghana Card, Student ID, or
    Passport.
</p>
<p class="text-red-500 mb-4 fw-bold">NB: If you are still in school, upload your student ID card.</p>

@if($profile->verification_document_url)
<div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 30px;">
    <strong>✓ Verification Document Uploaded</strong><br>
    <span style="font-size: 14px;">Status: {{ ucfirst($profile->verification_status) }}</span>
    @if($profile->verification_type)
    <br><span style="font-size: 14px;">Type: {{ ucfirst(str_replace('_', ' ', $profile->verification_type)) }}</span>
    @endif
</div>
@endif

<form method="POST" action="{{ route('talent.profile.build.save', ['step' => 4]) }}" enctype="multipart/form-data">
    @csrf

    <!-- Verification Type -->
    <div style="margin-bottom: 20px;">
        <label for="verification_type" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Verification Document Type <span style="color: red;">*</span>
        </label>
        <select name="verification_type" id="verification_type" required
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            <option value="">Select Document Type</option>
            <option value="ghana_card" {{ old('verification_type', $profile->verification_type) == 'ghana_card' ?
                'selected' : '' }}>Ghana Card</option>
            <option value="student_id" {{ old('verification_type', $profile->verification_type) == 'student_id' ?
                'selected' : '' }}>Student ID</option>
            <option value="passport" {{ old('verification_type', $profile->verification_type) == 'passport' ? 'selected'
                : '' }}>Passport</option>
        </select>
        @error('verification_type')
        <span style="color: red; font-size: 14px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Verification Document -->
    <div style="margin-bottom: 30px;">
        <label for="verification_document" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Upload Document <span style="color: red;">*</span>
        </label>
        <input type="file" name="verification_document" id="verification_document" accept=".pdf,.jpg,.jpeg,.png"
            required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;"
            onchange="validateVerificationDocument(this)">
        <small style="color: #666; font-size: 14px;">Accepted formats: PDF, JPG, PNG (Max 5MB)</small>
        <!-- Error Display -->
        <div id="verification-error"
            style="display: none; margin-top: 8px; padding: 10px; background: #fee; border: 1px solid #fcc; border-radius: 4px; color: #c33;">
            <span id="verification-error-text"></span>
        </div>
        @error('verification_document')
        <div
            style="margin-top: 8px; padding: 10px; background: #fee; border: 1px solid #fcc; border-radius: 4px; color: #c33;">
            <span>{{ $message }}</span>
        </div>
        @enderror
    </div>

    <!-- Navigation Buttons -->
    <div style="display: flex; justify-content: space-between; gap: 20px;">
        <a href="{{ route('talent.profile.build.step', ['step' => 3]) }}" class="primary-btn1 btn-hover"
            style="text-decoration: none; padding: 12px 24px; display: inline-block;">
            Previous
        </a>
        <button type="submit" id="verification-submit-btn" class="primary-btn1 btn-hover" style="padding: 12px 24px;">
            {{ $profile->verification_document_url ? 'Update & Complete' : 'Upload & Complete' }}
        </button>
    </div>
</form>

<script>
    // Helper function to format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    // Helper function to show error
    function showVerificationError(message) {
        const errorDiv = document.getElementById('verification-error');
        const errorText = document.getElementById('verification-error-text');
        if (errorDiv && errorText) {
            errorText.textContent = message;
            errorDiv.style.display = 'block';
        }
    }

    // Helper function to hide error
    function hideVerificationError() {
        const errorDiv = document.getElementById('verification-error');
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    }

    function validateVerificationDocument(input) {
        if (!input.files || !input.files[0]) {
            return;
        }

        const file = input.files[0];
        const maxSize = 5 * 1024 * 1024; // 5MB in bytes
        const fileExtension = file.name.split('.').pop().toLowerCase();
        const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];

        // Hide previous errors
        hideVerificationError();

        // Client-side validation
        if (!allowedExtensions.includes(fileExtension)) {
            showVerificationError('Invalid file type. Please upload a PDF, JPG, JPEG, or PNG file.');
            input.value = '';
            return false;
        }

        if (file.size > maxSize) {
            const fileSizeFormatted = formatFileSize(file.size);
            const maxSizeFormatted = formatFileSize(maxSize);
            showVerificationError(`File is too large (${fileSizeFormatted}). Maximum size is ${maxSizeFormatted}.`);
            input.value = '';
            return false;
        }

        return true;
    }

    // Validate on form submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const fileInput = document.getElementById('verification_document');
        if (fileInput && fileInput.files && fileInput.files[0]) {
            if (!validateVerificationDocument(fileInput)) {
                e.preventDefault();
                return false;
            }
        }
    });
</script>