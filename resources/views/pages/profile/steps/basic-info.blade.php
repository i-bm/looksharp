<form method="POST" action="{{ route('talent.profile.build.save', ['step' => 1]) }}" enctype="multipart/form-data">
    @csrf

    <h2 class="text-2xl font-bold text-title-color mb-30">
        Basic Information
    </h2>

    <!-- Profile Photo Upload -->
    <div class="mb-30">
        <label class="block mb-2 font-medium">Profile Photo <span style="color: red;">*</span></label>
        <div style="display: flex; align-items: center; gap: 20px;">
            @if($profile->profile_photo)
            <img src="{{ asset('storage/' . $profile->profile_photo) }}" alt="Profile Photo"
                style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
            @else
            <div
                style="width: 100px; height: 100px; border-radius: 50%; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                <span style="color: #999;">No Photo</span>
            </div>
            @endif
            <div style="flex: 1;">
                <input type="file" name="photo" id="photo" accept="image/*" style="display: none;"
                    onchange="uploadPhoto(this)">
                <button type="button" id="photo-upload-btn" onclick="document.getElementById('photo').click()"
                    class="primary-btn1 btn-hover" style="padding: 8px 16px; font-size: 14px;">
                    {{ $profile->profile_photo ? 'Change Photo' : 'Upload Photo' }}
                </button>
                <x-info-icon
                    text="Did you know that you're more likely to have your profile reviewed by an employer if you add a profile picture?"
                    width="250px" />
                <p style="margin-top: 8px; font-size: 12px; color: #666;">
                    Accepted formats: JPG, JPEG, PNG (Max 2MB)
                </p>
                <!-- Error Display -->
                <div id="photo-error"
                    style="display: none; margin-top: 8px; padding: 10px; background: #fee; border: 1px solid #fcc; border-radius: 4px; color: #c33;">
                    <span id="photo-error-text"></span>
                </div>
                <!-- Loading Indicator -->
                <div id="photo-loading" style="display: none; margin-top: 8px; color: #666; font-size: 14px;">
                    <span>Uploading...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Resume Upload (Optional) -->
    <div style="margin-bottom: 30px;">
        <label class="block mb-2 font-medium">Resume (Optional)</label>
        <div style="display: flex; align-items: center; gap: 20px;">
            @if($profile->resume_url)
            <div
                style="display: flex; align-items: center; gap: 10px; padding: 10px; background: #f0f0f0; border-radius: 4px;">
                <svg style="width: 24px; height: 24px; fill: #4CAF50;" viewBox="0 0 24 24">
                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
                </svg>
                <span style="color: #4CAF50; font-weight: 500;">Resume uploaded</span>
            </div>
            @else
            <div style="padding: 10px; background: #f0f0f0; border-radius: 4px; color: #999;">
                No resume uploaded
            </div>
            @endif
            <div style="flex: 1;">
                <input type="file" name="resume" id="resume" accept=".pdf,.doc,.docx" style="display: none;"
                    onchange="uploadResume(this)">
                <button type="button" id="resume-upload-btn" onclick="document.getElementById('resume').click()"
                    class="primary-btn1 btn-hover" style="padding: 8px 16px; font-size: 14px;">
                    {{ $profile->resume_url ? 'Change Resume' : 'Upload Resume' }}
                </button>
                <p style="margin-top: 8px; font-size: 12px; color: #666;">
                    Accepted formats: PDF, DOC, DOCX (Max 5MB)
                </p>
                <!-- Error Display -->
                <div id="resume-error"
                    style="display: none; margin-top: 8px; padding: 10px; background: #fee; border: 1px solid #fcc; border-radius: 4px; color: #c33;">
                    <span id="resume-error-text"></span>
                </div>
                <!-- Loading Indicator -->
                <div id="resume-loading" style="display: none; margin-top: 8px; color: #666; font-size: 14px;">
                    <span>Uploading...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- First Name -->
    <div style="margin-bottom: 20px;">
        <label for="first_name" style="display: block; margin-bottom: 8px; font-weight: 500;">
            First Name <span style="color: red;">*</span>
        </label>
        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $profile->first_name) }}"
            required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('first_name')
        <span style="color: red; font-size: 14px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Last Name -->
    <div style="margin-bottom: 20px;">
        <label for="last_name" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Last Name <span style="color: red;">*</span>
        </label>
        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $profile->last_name) }}" required
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('last_name')
        <span style="color: red; font-size: 14px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Date of Birth -->
    @php
    $dobValues = getDateComponentValues('dob', $profile->date_of_birth);
    $currentYear = (int) date('Y');
    $dobYearOptions = getYearOptions($currentYear - 100, $currentYear - 13);
    @endphp
    @include('pages.profile.partials.date-selector', [
    'label' => 'Date of Birth',
    'required' => true,
    'prefix' => 'dob',
    'dayValue' => $dobValues['day'],
    'monthValue' => $dobValues['month'],
    'yearValue' => $dobValues['year'],
    'yearOptions' => $dobYearOptions,
    'errorKey' => 'date_of_birth'
    ])

    <!-- Gender -->
    <div style="margin-bottom: 20px;" class="form-inner">
        <label for="gender" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Gender <span style="color: red;">*</span>
        </label>
        <select name="gender" id="gender" required
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            <option value="">Select Gender</option>
            <option value="male" {{ old('gender', $profile->gender) == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('gender', $profile->gender) == 'female' ? 'selected' : '' }}>Female</option>
            <option value="other" {{ old('gender', $profile->gender) == 'other' ? 'selected' : '' }}>Other</option>
            <option value="prefer_not_to_say" {{ old('gender', $profile->gender) == 'prefer_not_to_say' ? 'selected' :
                '' }}>Prefer not to say</option>
        </select>
        @error('gender')
        <span style="color: red; font-size: 14px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Location -->
    <div style="margin-bottom: 20px;">
        <label for="location" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Location (City/Region in Ghana) <span style="color: red;">*</span>
        </label>
        <input type="text" name="location" id="location" value="{{ old('location', $profile->location) }}" required
            placeholder="e.g., Accra, Kumasi, Tamale"
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('location')
        <span style="color: red; font-size: 14px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Phone Number -->
    <div style="margin-bottom: 20px;">
        <label for="phone_number" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Phone Number (Optional)
        </label>
        <input type="tel" name="phone_number" id="phone_number"
            value="{{ old('phone_number', $profile->phone_number) }}"
            placeholder="e.g., +233 24 123 4567 or 024 123 4567"
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('phone_number')
        <span style="color: red; font-size: 14px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Bio -->
    <div style="margin-bottom: 30px;">
        <label for="bio" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Bio (Optional)
        </label>
        <textarea name="bio" id="bio" rows="4" placeholder="Tell us about yourself..."
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; resize: vertical;">{{ old('bio', $profile->bio) }}</textarea>
        @error('bio')
        <span style="color: red; font-size: 14px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Navigation Buttons -->
    <div style="display: flex; justify-content: space-between; gap: 20px;">
        <div></div>
        <button type="submit" class="primary-btn1 btn-hover" style="padding: 12px 24px;">
            Save & Continue
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
    function showError(elementId, textId, message) {
        const errorDiv = document.getElementById(elementId);
        const errorText = document.getElementById(textId);
        if (errorDiv && errorText) {
            errorText.textContent = message;
            errorDiv.style.display = 'block';
        }
    }

    // Helper function to hide error
    function hideError(elementId) {
        const errorDiv = document.getElementById(elementId);
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    }

    // Helper function to show loading
    function showLoading(elementId) {
        const loadingDiv = document.getElementById(elementId);
        if (loadingDiv) {
            loadingDiv.style.display = 'block';
        }
    }

    // Helper function to hide loading
    function hideLoading(elementId) {
        const loadingDiv = document.getElementById(elementId);
        if (loadingDiv) {
            loadingDiv.style.display = 'none';
        }
    }

    function uploadPhoto(input) {
        if (!input.files || !input.files[0]) {
            return;
        }

        const file = input.files[0];
        const maxSize = 2 * 1024 * 1024; // 2MB in bytes
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];

        // Hide previous errors
        hideError('photo-error');
        hideLoading('photo-loading');

        // Client-side validation
        if (!allowedTypes.includes(file.type)) {
            showError('photo-error', 'photo-error-text', 'Invalid file type. Please upload a JPG, JPEG, or PNG image.');
            input.value = '';
            return;
        }

        if (file.size > maxSize) {
            const fileSizeFormatted = formatFileSize(file.size);
            const maxSizeFormatted = formatFileSize(maxSize);
            showError('photo-error', 'photo-error-text', `File is too large (${fileSizeFormatted}). Maximum size is ${maxSizeFormatted}.`);
            input.value = '';
            return;
        }

        // Show loading
        showLoading('photo-loading');
        const uploadBtn = document.getElementById('photo-upload-btn');
        if (uploadBtn) {
            uploadBtn.disabled = true;
            uploadBtn.textContent = 'Uploading...';
        }

        const formData = new FormData();
        formData.append('photo', file);
        formData.append('_token', '{{ csrf_token() }}');

        // Create AbortController for timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 60000); // 60 second timeout

        fetch('{{ route("talent.profile.photo.upload") }}', {
            method: 'POST',
            body: formData,
            signal: controller.signal
        })
        .then(response => {
            clearTimeout(timeoutId);
            return response.json().then(data => {
                if (!response.ok) {
                    throw { data, status: response.status };
                }
                return data;
            });
        })
        .then(data => {
            hideLoading('photo-loading');
            if (uploadBtn) {
                uploadBtn.disabled = false;
                uploadBtn.textContent = '{{ $profile->profile_photo ? "Change Photo" : "Upload Photo" }}';
            }
            if (data.success) {
                location.reload();
            } else {
                let errorMessage = data.error || 'Failed to upload photo';
                if (data.errors && data.errors.photo) {
                    errorMessage = Array.isArray(data.errors.photo)
                        ? data.errors.photo.join(', ')
                        : data.errors.photo;
                }
                showError('photo-error', 'photo-error-text', errorMessage);
            }
        })
        .catch(error => {
            clearTimeout(timeoutId);
            hideLoading('photo-loading');
            if (uploadBtn) {
                uploadBtn.disabled = false;
                uploadBtn.textContent = '{{ $profile->profile_photo ? "Change Photo" : "Upload Photo" }}';
            }
            console.error('Error:', error);

            let errorMessage = 'Failed to upload photo';
            if (error.name === 'AbortError') {
                errorMessage = 'Upload timed out. Please check your internet connection and try again.';
            } else if (error.data) {
                if (error.data.errors && error.data.errors.photo) {
                    errorMessage = Array.isArray(error.data.errors.photo)
                        ? error.data.errors.photo.join(', ')
                        : error.data.errors.photo;
                } else if (error.data.error) {
                    errorMessage = error.data.error;
                } else if (error.status === 413) {
                    errorMessage = 'File is too large. Maximum size is 2MB.';
                } else if (error.status === 0) {
                    errorMessage = 'Network error. Please check your internet connection and try again.';
                }
            }
            showError('photo-error', 'photo-error-text', errorMessage);
        });
    }

    function uploadResume(input) {
        if (!input.files || !input.files[0]) {
            return;
        }

        const file = input.files[0];
        const maxSize = 5 * 1024 * 1024; // 5MB in bytes
        const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

        // Hide previous errors
        hideError('resume-error');
        hideLoading('resume-loading');

        // Client-side validation
        const fileExtension = file.name.split('.').pop().toLowerCase();
        if (!['pdf', 'doc', 'docx'].includes(fileExtension)) {
            showError('resume-error', 'resume-error-text', 'Invalid file type. Please upload a PDF, DOC, or DOCX file.');
            input.value = '';
            return;
        }

        if (file.size > maxSize) {
            const fileSizeFormatted = formatFileSize(file.size);
            const maxSizeFormatted = formatFileSize(maxSize);
            showError('resume-error', 'resume-error-text', `File is too large (${fileSizeFormatted}). Maximum size is ${maxSizeFormatted}.`);
            input.value = '';
            return;
        }

        // Show loading
        showLoading('resume-loading');
        const uploadBtn = document.getElementById('resume-upload-btn');
        if (uploadBtn) {
            uploadBtn.disabled = true;
            uploadBtn.textContent = 'Uploading...';
        }

        const formData = new FormData();
        formData.append('resume', file);
        formData.append('_token', '{{ csrf_token() }}');

        // Create AbortController for timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 120000); // 120 second timeout for larger files

        fetch('{{ route("talent.profile.resume.upload") }}', {
            method: 'POST',
            body: formData,
            signal: controller.signal
        })
        .then(response => {
            clearTimeout(timeoutId);
            return response.json().then(data => {
                if (!response.ok) {
                    throw { data, status: response.status };
                }
                return data;
            });
        })
        .then(data => {
            hideLoading('resume-loading');
            if (uploadBtn) {
                uploadBtn.disabled = false;
                uploadBtn.textContent = '{{ $profile->resume_url ? "Change Resume" : "Upload Resume" }}';
            }
            if (data.success) {
                location.reload();
            } else {
                const errorMessage = data.error || 'Failed to upload resume';
                showError('resume-error', 'resume-error-text', errorMessage);
            }
        })
        .catch(error => {
            clearTimeout(timeoutId);
            hideLoading('resume-loading');
            if (uploadBtn) {
                uploadBtn.disabled = false;
                uploadBtn.textContent = '{{ $profile->resume_url ? "Change Resume" : "Upload Resume" }}';
            }
            console.error('Error:', error);

            let errorMessage = 'Failed to upload resume';
            if (error.name === 'AbortError') {
                errorMessage = 'Upload timed out. Please check your internet connection and try again.';
            } else if (error.data) {
                if (error.data.error) {
                    errorMessage = error.data.error;
                } else if (error.status === 413) {
                    errorMessage = 'File is too large. Maximum size is 5MB.';
                } else if (error.status === 0) {
                    errorMessage = 'Network error. Please check your internet connection and try again.';
                }
            }
            showError('resume-error', 'resume-error-text', errorMessage);
        });
    }
</script>