<form method="POST" action="{{ route('profile.build.save', ['step' => 1]) }}" enctype="multipart/form-data">
    @csrf

    <h2 style="font-family: var(--font-bricolageGrotesque); font-weight: 600; font-size: 24px; margin-bottom: 30px;">
        Basic Information
    </h2>

    <!-- Profile Photo Upload -->
    <div style="margin-bottom: 30px;">
        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Profile Photo</label>
        <div style="display: flex; align-items: center; gap: 20px;">
            @if($profile->profile_photo)
                <img src="{{ asset('storage/' . $profile->profile_photo) }}" 
                     alt="Profile Photo" 
                     style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
            @else
                <div style="width: 100px; height: 100px; border-radius: 50%; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                    <span style="color: #999;">No Photo</span>
                </div>
            @endif
            <div>
                <input type="file" name="photo" id="photo" accept="image/*" style="display: none;" onchange="uploadPhoto(this)">
                <button type="button" onclick="document.getElementById('photo').click()" 
                        class="primary-btn1 btn-hover" style="padding: 8px 16px; font-size: 14px;">
                    {{ $profile->profile_photo ? 'Change Photo' : 'Upload Photo' }}
                </button>
            </div>
        </div>
    </div>

    <!-- First Name -->
    <div style="margin-bottom: 20px;">
        <label for="first_name" style="display: block; margin-bottom: 8px; font-weight: 500;">
            First Name <span style="color: red;">*</span>
        </label>
        <input type="text" 
               name="first_name" 
               id="first_name" 
               value="{{ old('first_name', $profile->first_name) }}" 
               required
               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('first_name')
            <span style="color: red; font-size: 14px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Last Name -->
    <div style="margin-bottom: 20px;">
        <label for="last_name" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Last Name <span style="color: red;">*</span>
        </label>
        <input type="text" 
               name="last_name" 
               id="last_name" 
               value="{{ old('last_name', $profile->last_name) }}" 
               required
               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('last_name')
            <span style="color: red; font-size: 14px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Date of Birth -->
    <div style="margin-bottom: 20px;">
        <label for="date_of_birth" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Date of Birth <span style="color: red;">*</span>
        </label>
        <input type="date" 
               name="date_of_birth" 
               id="date_of_birth" 
               value="{{ old('date_of_birth', $profile->date_of_birth?->format('Y-m-d')) }}" 
               required
               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('date_of_birth')
            <span style="color: red; font-size: 14px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Gender -->
    <div style="margin-bottom: 20px;">
        <label for="gender" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Gender <span style="color: red;">*</span>
        </label>
        <select name="gender" 
                id="gender" 
                required
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            <option value="">Select Gender</option>
            <option value="male" {{ old('gender', $profile->gender) == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('gender', $profile->gender) == 'female' ? 'selected' : '' }}>Female</option>
            <option value="other" {{ old('gender', $profile->gender) == 'other' ? 'selected' : '' }}>Other</option>
            <option value="prefer_not_to_say" {{ old('gender', $profile->gender) == 'prefer_not_to_say' ? 'selected' : '' }}>Prefer not to say</option>
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
        <input type="text" 
               name="location" 
               id="location" 
               value="{{ old('location', $profile->location) }}" 
               required
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
        <textarea name="bio" 
                  id="bio" 
                  rows="4" 
                  placeholder="Tell us about yourself..."
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

        fetch('{{ route("profile.photo.upload") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Failed to upload photo');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to upload photo');
        });
    }
}
</script>

