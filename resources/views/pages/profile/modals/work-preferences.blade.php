<x-modal id="work-preferences-modal" title="Edit Work Preferences" size="large">
    <form id="work-preferences-form"
        onsubmit="submitWorkPreferencesForm(event, 'work-preferences-form', '{{ route('talent.profile.work-preferences.update') }}', 'work-preferences-modal')">
        @csrf
        @method('PUT')

        @php
        $selectedAreaIds = $profile->careerInterestAreas->pluck('id')->toArray();
        $selectedCities = $profile->preferredCities->map(function($city) {
            return ['id' => $city->id, 'name' => $city->name];
        })->toArray();

        // Get all children (not parents) for autocomplete
        $allChildren = collect();
        foreach($careerInterestAreas as $parentArea) {
            $allChildren = $allChildren->merge($parentArea->children);
        }
        @endphp

        <!-- Career Interest Areas Section -->
        <div style="margin-bottom: 30px;">
            <label
                style="display: block; margin-bottom: 15px; font-weight: 600; color: var(--title-color); font-size: 16px;">
                Career Interest Areas
            </label>

            <div id="career-interest-tags-container" class="career-interest-tags-container">
                <div class="career-interest-tags-wrapper">
                    <input type="text" class="career-interest-input" placeholder="Type to add career interests...">
                </div>
                <div class="career-interest-suggestions"></div>
                <input type="hidden" id="career-interest-areas-hidden" name="career_interest_areas" value="{{ json_encode($selectedAreaIds) }}">
            </div>

            <!-- Hidden checkboxes for JavaScript to read available options -->
            <div style="display: none;">
                @foreach($allChildren as $childArea)
                <input type="checkbox" class="category-child" value="{{ $childArea->id }}" data-name="{{ $childArea->name }}">
                <span>{{ $childArea->name }}</span>
                @endforeach
            </div>
        </div>

        <!-- Preferred Cities Section -->
        <div style="margin-bottom: 30px;">
            <label
                style="display: block; margin-bottom: 15px; font-weight: 600; color: var(--title-color); font-size: 16px;">
                Preferred Location (Cities)
            </label>

            <div id="preferred-city-tags-container" class="preferred-city-tags-container">
                <div class="preferred-city-tags-wrapper">
                    <input type="text" class="preferred-city-input" placeholder="Type to add cities...">
                </div>
                <div class="preferred-city-suggestions"></div>
                <input type="hidden" id="preferred-cities-hidden" name="preferred_cities" value="{{ json_encode($selectedCities) }}">
            </div>
        </div>

        <!-- Work Model (Preferred Location Type) -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                Work Model
            </label>
            <div style="display: flex; flex-direction: row; gap: 16px; flex-wrap: wrap;">
                @foreach($workModels ?? [] as $workModel)
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px;">
                    <input type="checkbox" name="work_models[]" value="{{ $workModel->id }}"
                           {{ $profile->workModels->contains($workModel->id) ? 'checked' : '' }}>
                    <span>{{ $workModel->display_name }}</span>
                </label>
                @endforeach
            </div>
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
