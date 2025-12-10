<x-modal id="education-modal" title="Edit Education" size="large">
    <div>
        <!-- Existing Education -->
        @if($profile->education->count() > 0)
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 15px; color: var(--title-color);">Your
                Education Records ({{ $profile->education->count() }})</h3>
            @foreach($profile->education as $edu)
            <div
                style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>{{ $edu->institution?->name ?? 'Institution not set' }}</strong><br>
                    <span>{{ ucfirst(str_replace('_', ' ', $edu->degree_type->value)) }} in {{ $edu->field_of_study
                        }}</span>
                    @if($edu->level)
                    <span style="color: #666; font-size: 14px;"> - {{ $edu->level }}</span>
                    @endif
                    @if($edu->is_current)
                    <span
                        style="background: #4CAF50; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin-left: 10px;">Current</span>
                    @endif
                    @if($edu->is_primary)
                    <span
                        style="background: #2196F3; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin-left: 10px;">Primary</span>
                    @endif
                </div>
                <button type="button" onclick="deleteEducation('{{ $edu->id }}')"
                    style="background: #f44336; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">
                    Remove
                </button>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Add Education Form -->
        <form id="add-education-form" onsubmit="submitAddEducation(event)">
            @csrf
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 20px; color: var(--title-color);">{{
                $profile->education->count() > 0 ? 'Add Another Education' : 'Add Education' }}</h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Institution
                    </label>
                    <select name="institution_id" id="education-institution-id"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                        <option value="">Select Institution</option>
                        @foreach($institutions as $institution)
                        <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Degree Type <span style="color: #F53003;">*</span>
                    </label>
                    <select name="degree_type" id="education-degree-type" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                        <option value="">Select Degree Type</option>
                        <option value="certificate">Certificate</option>
                        <option value="diploma">Diploma</option>
                        <option value="bachelors">Bachelor's</option>
                        <option value="masters">Master's</option>
                        <option value="phd">PhD</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Field of Study <span style="color: #F53003;">*</span>
                </label>
                <input type="text" name="field_of_study" id="education-field-of-study" required
                    placeholder="e.g., Computer Science, Business Administration"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Level (Optional)
                </label>
                <input type="text" name="level" id="education-level" placeholder="e.g., Level 100, 200"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
            </div>

            @php
            $currentYear = (int) date('Y');
            $yearOptions = getYearOptions($currentYear - 50, $currentYear + 10);
            @endphp

            <div style="margin-bottom: 20px;">
                @include('pages.profile.partials.date-selector', [
                'label' => 'Start Date',
                'required' => true,
                'prefix' => 'education_start_date',
                'dayValue' => '',
                'monthValue' => '',
                'yearValue' => '',
                'yearOptions' => $yearOptions,
                'errorKey' => 'start_date'
                ])
            </div>

            <div style="margin-bottom: 20px;">
                <input type="hidden" name="is_current" value="0">
                <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
                    <input type="checkbox" name="is_current" id="education-is-current" value="1"
                        onchange="toggleEducationEndDate(this)">
                    <span style="font-weight: 500;">Currently enrolled</span>
                </label>
            </div>

            <div id="education-end-date-container" style="margin-bottom: 20px;">
                @include('pages.profile.partials.date-selector', [
                'label' => 'End Date',
                'required' => false,
                'prefix' => 'education_end_date',
                'dayValue' => '',
                'monthValue' => '',
                'yearValue' => '',
                'yearOptions' => $yearOptions,
                'errorKey' => 'end_date'
                ])
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="is_primary" id="education-is-primary" value="1">
                        <span style="font-weight: 500;">Mark as primary education</span>
                    </label>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        GPA (Optional)
                    </label>
                    <input type="number" name="gpa" id="education-gpa" step="0.01" min="0" max="5"
                        placeholder="e.g., 3.5"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 25px;">
                <button type="button" onclick="closeModal('education-modal')"
                    style="padding: 10px 20px; border: 2px solid #ddd; border-radius: 4px; background: white; color: var(--text-color); cursor: pointer; font-size: 14px;">
                    Close
                </button>
                <button type="submit" class="primary-btn1 btn-hover"
                    style="padding: 10px 20px; border: none; cursor: pointer; font-size: 14px;">
                    <i class="bi bi-plus-circle"></i> Add Education
                </button>
            </div>
        </form>
    </div>
</x-modal>
