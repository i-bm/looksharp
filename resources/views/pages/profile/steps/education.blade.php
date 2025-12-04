<h2 style="font-family: var(--font-bricolageGrotesque); font-weight: 600; font-size: 24px; margin-bottom: 30px;">
    Education
</h2>

<!-- Existing Education Records -->
@if($education->count() > 0)
    <div style="margin-bottom: 30px;">
        <h3 style="font-size: 18px; margin-bottom: 15px;">Your Education</h3>
        @foreach($education as $edu)
            <div style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>{{ $edu->institution?->name ?? 'Institution not set' }}</strong><br>
                    <span>{{ ucfirst($edu->degree_type->value) }} in {{ $edu->field_of_study }}</span>
                    @if($edu->is_current)
                        <span style="background: #4CAF50; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin-left: 10px;">Current</span>
                    @endif
                    @if($edu->is_primary)
                        <span style="background: #2196F3; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin-left: 10px;">Primary</span>
                    @endif
                </div>
                <form method="POST" action="{{ route('profile.education.remove', ['id' => $edu->id]) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Are you sure you want to remove this education record?')" 
                            style="background: #f44336; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">
                        Remove
                    </button>
                </form>
            </div>
        @endforeach
    </div>
@endif

<!-- Add Education Form -->
<form method="POST" action="{{ route('profile.build.save', ['step' => 2]) }}">
    @csrf

    <h3 style="font-size: 18px; margin-bottom: 20px;">{{ $education->count() > 0 ? 'Add Another Education' : 'Add Education' }}</h3>

    <!-- Institution -->
    <div style="margin-bottom: 20px;">
        <label for="institution_id" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Institution
        </label>
        <select name="institution_id" 
                id="institution_id" 
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            <option value="">Select Institution</option>
            @foreach($institutions as $institution)
                <option value="{{ $institution->id }}" {{ old('institution_id') == $institution->id ? 'selected' : '' }}>
                    {{ $institution->name }}
                </option>
            @endforeach
        </select>
        @error('institution_id')
            <span style="color: red; font-size: 14px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Degree Type -->
    <div style="margin-bottom: 20px;">
        <label for="degree_type" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Degree Type <span style="color: red;">*</span>
        </label>
        <select name="degree_type" 
                id="degree_type" 
                required
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            <option value="">Select Degree Type</option>
            <option value="certificate" {{ old('degree_type') == 'certificate' ? 'selected' : '' }}>Certificate</option>
            <option value="diploma" {{ old('degree_type') == 'diploma' ? 'selected' : '' }}>Diploma</option>
            <option value="bachelors" {{ old('degree_type') == 'bachelors' ? 'selected' : '' }}>Bachelor's</option>
            <option value="masters" {{ old('degree_type') == 'masters' ? 'selected' : '' }}>Master's</option>
            <option value="phd" {{ old('degree_type') == 'phd' ? 'selected' : '' }}>PhD</option>
        </select>
        @error('degree_type')
            <span style="color: red; font-size: 14px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Field of Study -->
    <div style="margin-bottom: 20px;">
        <label for="field_of_study" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Field of Study <span style="color: red;">*</span>
        </label>
        <input type="text" 
               name="field_of_study" 
               id="field_of_study" 
               value="{{ old('field_of_study') }}" 
               required
               placeholder="e.g., Computer Science, Business Administration"
               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('field_of_study')
            <span style="color: red; font-size: 14px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Start Date -->
    <div style="margin-bottom: 20px;">
        <label for="start_date" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Start Date <span style="color: red;">*</span>
        </label>
        <input type="date" 
               name="start_date" 
               id="start_date" 
               value="{{ old('start_date') }}" 
               required
               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('start_date')
            <span style="color: red; font-size: 14px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- End Date / Is Current -->
    <div style="margin-bottom: 20px;">
        <label style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
            <input type="checkbox" 
                   name="is_current" 
                   id="is_current" 
                   value="1"
                   {{ old('is_current') ? 'checked' : '' }}
                   onchange="toggleEndDate()">
            <span>Currently Enrolled</span>
        </label>
    </div>

    <div style="margin-bottom: 20px;" id="end_date_container">
        <label for="end_date" style="display: block; margin-bottom: 8px; font-weight: 500;">
            End Date / Expected Graduation
        </label>
        <input type="date" 
               name="end_date" 
               id="end_date" 
               value="{{ old('end_date') }}"
               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('end_date')
            <span style="color: red; font-size: 14px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- GPA -->
    <div style="margin-bottom: 20px;">
        <label for="gpa" style="display: block; margin-bottom: 8px; font-weight: 500;">
            GPA (Optional)
        </label>
        <input type="number" 
               name="gpa" 
               id="gpa" 
               value="{{ old('gpa') }}" 
               step="0.01"
               min="0"
               max="5"
               placeholder="e.g., 3.5"
               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('gpa')
            <span style="color: red; font-size: 14px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Is Primary -->
    <div style="margin-bottom: 30px;">
        <label style="display: flex; align-items: center; gap: 10px;">
            <input type="checkbox" 
                   name="is_primary" 
                   id="is_primary" 
                   value="1"
                   {{ old('is_primary') ? 'checked' : '' }}>
            <span>Mark as Primary Education</span>
        </label>
    </div>

    <!-- Navigation Buttons -->
    <div style="display: flex; justify-content: space-between; gap: 20px;">
        <a href="{{ route('profile.build.step', ['step' => 1]) }}" 
           class="primary-btn1 btn-hover" 
           style="text-decoration: none; padding: 12px 24px; display: inline-block;">
            Previous
        </a>
        <button type="submit" class="primary-btn1 btn-hover" style="padding: 12px 24px;">
            {{ $education->count() > 0 ? 'Add Education & Continue' : 'Save & Continue' }}
        </button>
    </div>
</form>

<script>
function toggleEndDate() {
    const isCurrent = document.getElementById('is_current').checked;
    const endDateContainer = document.getElementById('end_date_container');
    const endDateInput = document.getElementById('end_date');
    
    if (isCurrent) {
        endDateContainer.style.display = 'none';
        endDateInput.value = '';
    } else {
        endDateContainer.style.display = 'block';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleEndDate();
});
</script>

