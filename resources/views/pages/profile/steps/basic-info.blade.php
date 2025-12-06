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
            <div>
                <input type="file" name="photo" id="photo" accept="image/*" style="display: none;"
                    onchange="uploadPhoto(this)">
                <button type="button" onclick="document.getElementById('photo').click()" class="primary-btn1 btn-hover"
                    style="padding: 8px 16px; font-size: 14px;">
                    {{ $profile->profile_photo ? 'Change Photo' : 'Upload Photo' }}
                </button>
                <x-info-icon
                    text="Did you know that you're more likely to have your profile reviewed by an employer if you add a profile picture?"
                    width="250px" />
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
            <div>
                <input type="file" name="resume" id="resume" accept=".pdf,.doc,.docx" style="display: none;"
                    onchange="uploadResume(this)">
                <button type="button" onclick="document.getElementById('resume').click()" class="primary-btn1 btn-hover"
                    style="padding: 8px 16px; font-size: 14px;">
                    {{ $profile->resume_url ? 'Change Resume' : 'Upload Resume' }}
                </button>
            </div>
        </div>
        <p style="margin-top: 8px; font-size: 12px; color: #666;">
            Accepted formats: PDF, DOC, DOCX (Max 5MB)
        </p>
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
    function uploadPhoto(input) {
        if (input.files && input.files[0]) {
            const formData = new FormData();
            formData.append('photo', input.files[0]);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("talent.profile.photo.upload") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                return response.json().then(data => {
                    if (!response.ok) {
                        throw { data, status: response.status };
                    }
                    return data;
                });
            })
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    let errorMessage = data.error || 'Failed to upload photo';
                    if (data.errors && data.errors.photo) {
                        errorMessage = Array.isArray(data.errors.photo)
                            ? data.errors.photo.join(', ')
                            : data.errors.photo;
                    }
                    alert(errorMessage);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                let errorMessage = 'Failed to upload photo';
                if (error.data) {
                    if (error.data.errors && error.data.errors.photo) {
                        errorMessage = Array.isArray(error.data.errors.photo)
                            ? error.data.errors.photo.join(', ')
                            : error.data.errors.photo;
                    } else if (error.data.error) {
                        errorMessage = error.data.error;
                    }
                }
                alert(errorMessage);
            });
        }
    }

    function uploadResume(input) {
        if (input.files && input.files[0]) {
            const formData = new FormData();
            formData.append('resume', input.files[0]);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("talent.profile.resume.upload") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Failed to upload resume');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to upload resume');
            });
        }
    }
</script>