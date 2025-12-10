<h2 style="font-family: var(--font-bricolageGrotesque); font-weight: 600; font-size: 24px; margin-bottom: 30px;">
    Verification
</h2>

<p style="color: #666; margin-bottom: 20px;">
    Please select your current status and complete the verification process.
</p>

@if($profile->verification_document_url && $profile->verification_status === 'verified')
<div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 30px;">
    <strong>✓ Verification Complete</strong><br>
    <span style="font-size: 14px;">Your profile has been verified.</span>
</div>
@elseif($profile->verification_document_url)
<div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 4px; margin-bottom: 30px;">
    <strong>Verification Document Uploaded</strong><br>
    <span style="font-size: 14px;">Status: {{ ucfirst($profile->verification_status) }}</span>
</div>
@endif

<form method="POST" action="{{ route('talent.profile.build.save', ['step' => 4]) }}" enctype="multipart/form-data"
    id="verification-form">
    @csrf

    <!-- Current Status Selection -->
    <div style="margin-bottom: 30px;">
        <label style="display: block; margin-bottom: 12px; font-weight: 500; font-size: 16px;">
            What's your current status? <span style="color: red;">*</span>
        </label>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach($currentStatusOptions as $status)
            <label
                style="display: flex; align-items: center; padding: 12px; border: 2px solid #ddd; border-radius: 4px; cursor: pointer; transition: all 0.2s;"
                onmouseover="this.style.borderColor='var(--primary-color2)'"
                onmouseout="if(document.querySelector('input[name=current_status][value={{ $status->value }}]').checked) { this.style.borderColor='var(--primary-color2)'; } else { this.style.borderColor='#ddd'; }">
                <input type="radio" name="current_status" value="{{ $status->value }}" id="status_{{ $status->value }}"
                    {{ old('current_status', $profile->current_status?->value) == $status->value ? 'checked' : '' }}
                style="margin-right: 12px; width: 18px; height: 18px; cursor: pointer;"
                onchange="toggleStudentFields()">
                <span style="font-size: 16px; cursor: pointer;">
                    @if($status->value === 'student')
                    Student
                    @elseif($status->value === 'nss')
                    NSS (National Service)
                    @elseif($status->value === 'recent_graduate')
                    Recent Graduate
                    @endif
                </span>
            </label>
            @endforeach
        </div>
        @error('current_status')
        <span style="color: red; font-size: 14px; margin-top: 8px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Student Fields (shown when Student is selected) -->
    <div id="student-fields"
        style="display: none; margin-bottom: 30px; padding: 20px; background: #f9f9f9; border-radius: 4px;">
        <h3 style="font-size: 18px; margin-bottom: 20px; color: var(--title-color);">Student Information</h3>

        <!-- Student ID -->
        <div style="margin-bottom: 20px;">
            <label for="student_id" style="display: block; margin-bottom: 8px; font-weight: 500;">
                Student ID <span style="color: red;">*</span>
            </label>
            <input type="text" name="student_id" id="student_id" value="{{ old('student_id', $profile->student_id) }}"
                placeholder="Enter your student ID"
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            @error('student_id')
            <span style="color: red; font-size: 14px; margin-top: 8px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Student Email -->
        <div style="margin-bottom: 20px;">
            <label for="student_email" style="display: block; margin-bottom: 8px; font-weight: 500;">
                Student Email <span style="color: red;">*</span>
            </label>
            <input type="email" name="student_email" id="student_email"
                value="{{ old('student_email', $profile->student_email) }}"
                placeholder="Enter your student email (e.g., name@ug.edu.gh)"
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            <small style="color: #666; font-size: 14px; margin-top: 4px; display: block;">
                Your student email must match your institution's email domain.
            </small>
            @error('student_email')
            <span style="color: red; font-size: 14px; margin-top: 8px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Student ID Card Upload -->
        <div style="margin-bottom: 20px;">
            <label for="verification_document" style="display: block; margin-bottom: 8px; font-weight: 500;">
                Upload Student ID Card <span style="color: red;">*</span>
            </label>
            <input type="file" name="verification_document" id="verification_document" accept=".pdf,.jpg,.jpeg,.png"
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;"
                onchange="validateVerificationDocument(this)">
            <small style="color: #666; font-size: 14px;">Accepted formats: PDF, JPG, PNG (Max 5MB)</small>
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
    </div>

    <!-- Non-Student Verification Fields (shown when NSS or Recent Graduate is selected) -->
    <div id="non-student-fields" style="display: none; margin-bottom: 30px;">
        <!-- Verification Type -->
        <div style="margin-bottom: 20px;">
            <label for="verification_type" style="display: block; margin-bottom: 8px; font-weight: 500;">
                Verification Document Type <span style="color: red;">*</span>
            </label>
            <select name="verification_type" id="verification_type"
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                <option value="">Select Document Type</option>
                <option value="ghana_card" {{ old('verification_type', $profile->verification_type) == 'ghana_card' ?
                    'selected' : '' }}>Ghana Card</option>
                <option value="passport" {{ old('verification_type', $profile->verification_type) == 'passport' ?
                    'selected' : '' }}>Passport</option>
            </select>
            @error('verification_type')
            <span style="color: red; font-size: 14px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Verification Document -->
        <div style="margin-bottom: 20px;">
            <label for="verification_document_non_student"
                style="display: block; margin-bottom: 8px; font-weight: 500;">
                Upload Document <span style="color: red;">*</span>
            </label>
            <input type="file" name="verification_document" id="verification_document_non_student"
                accept=".pdf,.jpg,.jpeg,.png"
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;"
                onchange="validateVerificationDocument(this)">
            <small style="color: #666; font-size: 14px;">Accepted formats: PDF, JPG, PNG (Max 5MB)</small>
            <div id="verification-error-non-student"
                style="display: none; margin-top: 8px; padding: 10px; background: #fee; border: 1px solid #fcc; border-radius: 4px; color: #c33;">
                <span id="verification-error-text-non-student"></span>
            </div>
            @error('verification_document')
            <div
                style="margin-top: 8px; padding: 10px; background: #fee; border: 1px solid #fcc; border-radius: 4px; color: #c33;">
                <span>{{ $message }}</span>
            </div>
            @enderror
        </div>
    </div>

    @error('error')
    <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
        {{ $message }}
    </div>
    @enderror

    <!-- Navigation Buttons -->
    <div style="display: flex; justify-content: space-between; gap: 20px;">
        <a href="{{ route('talent.profile.build.step', ['step' => 3]) }}" class="primary-btn1 btn-hover"
            style="text-decoration: none; padding: 12px 24px; display: inline-block;">
            Previous
        </a>
        <button type="submit" id="verification-submit-btn" class="primary-btn1 btn-hover" style="padding: 12px 24px;">
            {{ $profile->verification_document_url ? 'Update & Complete' : 'Submit & Complete' }}
        </button>
    </div>
</form>

<script>
    // Toggle student fields based on status selection
    function toggleStudentFields() {
        const studentRadio = document.querySelector('input[name="current_status"][value="student"]');
        const nssRadio = document.querySelector('input[name="current_status"][value="nss"]');
        const recentGraduateRadio = document.querySelector('input[name="current_status"][value="recent_graduate"]');
        const studentFields = document.getElementById('student-fields');
        const nonStudentFields = document.getElementById('non-student-fields');

        if (studentRadio && studentRadio.checked) {
            studentFields.style.display = 'block';
            nonStudentFields.style.display = 'none';
            // Make student fields required
            document.getElementById('student_id').required = true;
            document.getElementById('student_email').required = true;
            const studentFileInput = document.getElementById('verification_document');
            if (studentFileInput) {
                studentFileInput.required = true;
                studentFileInput.disabled = false;
            }
            // Make non-student fields not required and disable them
            document.getElementById('verification_type').required = false;
            const nonStudentFileInput = document.getElementById('verification_document_non_student');
            if (nonStudentFileInput) {
                nonStudentFileInput.required = false;
                nonStudentFileInput.disabled = true;
                nonStudentFileInput.value = ''; // Clear the file input
            }
        } else if ((nssRadio && nssRadio.checked) || (recentGraduateRadio && recentGraduateRadio.checked)) {
            studentFields.style.display = 'none';
            nonStudentFields.style.display = 'block';
            // Make student fields not required and disable them
            document.getElementById('student_id').required = false;
            document.getElementById('student_email').required = false;
            const studentFileInput = document.getElementById('verification_document');
            if (studentFileInput) {
                studentFileInput.required = false;
                studentFileInput.disabled = true;
                studentFileInput.value = ''; // Clear the file input
            }
            // Make non-student fields required
            document.getElementById('verification_type').required = true;
            const nonStudentFileInput = document.getElementById('verification_document_non_student');
            if (nonStudentFileInput) {
                nonStudentFileInput.required = true;
                nonStudentFileInput.disabled = false;
            }
        } else {
            studentFields.style.display = 'none';
            nonStudentFields.style.display = 'none';
            // Disable both file inputs when no status is selected
            const studentFileInput = document.getElementById('verification_document');
            const nonStudentFileInput = document.getElementById('verification_document_non_student');
            if (studentFileInput) {
                studentFileInput.disabled = true;
                studentFileInput.value = '';
            }
            if (nonStudentFileInput) {
                nonStudentFileInput.disabled = true;
                nonStudentFileInput.value = '';
            }
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleStudentFields();
    });

    // Helper function to format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    // Helper function to show error
    function showVerificationError(message, isStudent = true) {
        const errorDiv = isStudent ? document.getElementById('verification-error') : document.getElementById('verification-error-non-student');
        const errorText = isStudent ? document.getElementById('verification-error-text') : document.getElementById('verification-error-text-non-student');
        if (errorDiv && errorText) {
            errorText.textContent = message;
            errorDiv.style.display = 'block';
        }
    }

    // Helper function to hide error
    function hideVerificationError(isStudent = true) {
        const errorDiv = isStudent ? document.getElementById('verification-error') : document.getElementById('verification-error-non-student');
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

        // Determine if this is student or non-student field
        const isStudent = input.id === 'verification_document';

        // Hide previous errors
        hideVerificationError(isStudent);

        // Client-side validation
        if (!allowedExtensions.includes(fileExtension)) {
            showVerificationError('Invalid file type. Please upload a PDF, JPG, JPEG, or PNG file.', isStudent);
            input.value = '';
            return false;
        }

        if (file.size > maxSize) {
            const fileSizeFormatted = formatFileSize(file.size);
            const maxSizeFormatted = formatFileSize(maxSize);
            showVerificationError(`File is too large (${fileSizeFormatted}). Maximum size is ${maxSizeFormatted}.`, isStudent);
            input.value = '';
            return false;
        }

        return true;
    }

    // Validate on form submit
    document.getElementById('verification-form').addEventListener('submit', function(e) {
        const studentRadio = document.querySelector('input[name="current_status"][value="student"]');
        const fileInput = studentRadio && studentRadio.checked
            ? document.getElementById('verification_document')
            : document.getElementById('verification_document_non_student');

        if (fileInput && fileInput.files && fileInput.files[0]) {
            if (!validateVerificationDocument(fileInput)) {
                e.preventDefault();
                return false;
            }
        }
    });
</script>