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

<form method="POST" action="{{ route('profile.build.save', ['step' => 4]) }}" enctype="multipart/form-data">
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
            required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        <small style="color: #666; font-size: 14px;">Accepted formats: PDF, JPG, PNG (Max 5MB)</small>
        @error('verification_document')
        <span style="color: red; font-size: 14px; display: block; margin-top: 5px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Navigation Buttons -->
    <div style="display: flex; justify-content: space-between; gap: 20px;">
        <a href="{{ route('profile.build.step', ['step' => 3]) }}" class="primary-btn1 btn-hover"
            style="text-decoration: none; padding: 12px 24px; display: inline-block;">
            Previous
        </a>
        <button type="submit" class="primary-btn1 btn-hover" style="padding: 12px 24px;">
            {{ $profile->verification_document_url ? 'Update & Complete' : 'Upload & Complete' }}
        </button>
    </div>
</form>
