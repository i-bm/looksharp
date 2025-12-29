<x-modal id="work-preferences-modal" title="Edit Work Preferences" size="large">
    <form id="work-preferences-form"
        onsubmit="submitProfileForm(event, 'work-preferences-form', '{{ route('talent.profile.work-preferences.update') }}', 'work-preferences-modal')">
        @csrf
        @method('PUT')

        @php
        $selectedAreaIds = $profile->careerInterestAreas->pluck('id')->toArray();
        @endphp

        <!-- Career Interest Areas Section -->
        <div style="margin-bottom: 30px;">
            <label
                style="display: block; margin-bottom: 15px; font-weight: 600; color: var(--title-color); font-size: 16px;">
                Career Interest Areas
            </label>

            <div style="display: flex; flex-direction: column; gap: 20px;">
                @foreach($careerInterestAreas as $parentArea)
                <div class="job-category-group" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px;">
                    <label
                        style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px; cursor: pointer; font-weight: 500;">
                        @php
                        $parentHasSelected = false;
                        $childIds = $parentArea->children->pluck('id')->toArray();
                        foreach ($childIds as $childId) {
                        if (in_array($childId, $selectedAreaIds)) {
                        $parentHasSelected = true;
                        break;
                        }
                        }
                        @endphp
                        <input type="checkbox" class="category-parent" data-category="{{ $parentArea->id }}"
                            onchange="toggleCategoryGroup('{{ $parentArea->id }}', this.checked)" {{ $parentHasSelected
                            ? 'checked' : '' }}>
                        <span>{{ $parentArea->name }}</span>
                    </label>
                    @if($parentArea->children->count() > 0)
                    <div class="category-children"
                        style="margin-left: 30px; display: flex; flex-direction: column; gap: 8px;">
                        @foreach($parentArea->children as $childArea)
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px;">
                            <input type="checkbox" name="career_interest_areas[]" value="{{ $childArea->id }}"
                                class="category-child" data-parent="{{ $parentArea->id }}"
                                onchange="updateParentCheckbox('{{ $parentArea->id }}')" {{ in_array($childArea->id,
                            $selectedAreaIds) ? 'checked' : '' }}>
                            <span>{{ $childArea->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                Availability
            </label>
            <select name="availability" id="availability"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                <option value="">Select Availability</option>
                <option value="full_time" {{ $profile->availability?->value === 'full_time' ? 'selected' : ''
                    }}>Full-time</option>
                <option value="part_time" {{ $profile->availability?->value === 'part_time' ? 'selected' : ''
                    }}>Part-time</option>
                <option value="internship" {{ $profile->availability?->value === 'internship' ? 'selected' : ''
                    }}>Internship</option>
                <option value="contract" {{ $profile->availability?->value === 'contract' ? 'selected' : '' }}>Contract
                </option>
                <option value="flexible" {{ $profile->availability?->value === 'flexible' ? 'selected' : '' }}>Flexible
                </option>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                Availability Details
            </label>
            <textarea name="availability_details" id="availability-details" rows="3"
                placeholder="e.g., Available starting June 2025, Flexible hours preferred"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical;">{{ $profile->availability_details }}</textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                Preferred Location
            </label>
            <select name="preferred_location" id="preferred-location"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                <option value="">Select Location Preference</option>
                <option value="remote" {{ $profile->preferred_location?->value === 'remote' ? 'selected' : '' }}>Remote
                </option>
                <option value="hybrid" {{ $profile->preferred_location?->value === 'hybrid' ? 'selected' : '' }}>Hybrid
                </option>
                <option value="on_site" {{ $profile->preferred_location?->value === 'on_site' ? 'selected' : ''
                    }}>On-site</option>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                Salary Expectations (GHS)
            </label>
            <input type="number" name="salary_expectations" id="salary-expectations"
                value="{{ $profile->salary_expectations }}" placeholder="e.g., 3000" step="0.01" min="0"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 25px;">
            <button type="button" onclick="closeModal('work-preferences-modal')"
                style="padding: 10px 20px; border: 2px solid #ddd; border-radius: 4px; background: white; color: var(--text-color); cursor: pointer; font-size: 14px;">
                Cancel
            </button>
            <button type="submit" class="primary-btn1 btn-hover"
                style="padding: 10px 20px; border: none; cursor: pointer; font-size: 14px;">
                <i class="bi bi-check-lg"></i> Save Changes
            </button>
        </div>
    </form>
</x-modal>
