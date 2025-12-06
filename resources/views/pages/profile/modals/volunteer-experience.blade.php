<x-modal id="volunteer-experience-modal" title="Edit Volunteer Experiences" size="large">
    <div>
        <!-- Existing Volunteer Experiences -->
        @if($profile->volunteerExperiences->count() > 0)
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 15px; color: var(--title-color);">Your Volunteer Experiences ({{ $profile->volunteerExperiences->count() }})</h3>
            @foreach($profile->volunteerExperiences as $volunteer)
            <div
                style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>{{ $volunteer->organization }}</strong><br>
                    <span style="color: #666; font-size: 14px;">
                        <i class="bi bi-calendar3"></i>
                        {{ $volunteer->start_date?->format('M Y') }}
                        @if($volunteer->is_current)
                        - Present
                        @elseif($volunteer->end_date)
                        - {{ $volunteer->end_date->format('M Y') }}
                        @endif
                    </span>
                </div>
                <button type="button" onclick="deleteVolunteerExperience('{{ $volunteer->id }}')"
                    style="background: #f44336; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">
                    Remove
                </button>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Add Volunteer Experience Form -->
        <form id="add-volunteer-form" onsubmit="submitAddVolunteerExperience(event)">
            @csrf
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 20px; color: var(--title-color);">{{ $profile->volunteerExperiences->count() > 0 ? 'Add Another Volunteer Experience' : 'Add Volunteer Experience' }}</h3>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Organization <span style="color: #F53003;">*</span>
                </label>
                <input type="text" name="organization" id="volunteer-organization" required
                    placeholder="e.g., Red Cross, Local Community Center"
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
                'prefix' => 'volunteer_start_date',
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
                    <input type="checkbox" name="is_current" id="volunteer-is-current" value="1" onchange="toggleVolunteerEndDate(this)">
                    <span style="font-weight: 500;">Currently volunteering</span>
                </label>
            </div>

            <div id="volunteer-end-date-container" style="margin-bottom: 20px;">
                @include('pages.profile.partials.date-selector', [
                'label' => 'End Date',
                'required' => false,
                'prefix' => 'volunteer_end_date',
                'dayValue' => '',
                'monthValue' => '',
                'yearValue' => '',
                'yearOptions' => $yearOptions,
                'errorKey' => 'end_date'
                ])
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Details
                </label>
                <textarea name="details" id="volunteer-details" rows="3"
                    placeholder="Describe your volunteer work and contributions..."
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 25px;">
                <button type="button" onclick="closeModal('volunteer-experience-modal')" 
                    style="padding: 10px 20px; border: 2px solid #ddd; border-radius: 4px; background: white; color: var(--text-color); cursor: pointer; font-size: 14px;">
                    Close
                </button>
                <button type="submit" class="primary-btn1 btn-hover"
                    style="padding: 10px 20px; border: none; cursor: pointer; font-size: 14px;">
                    <i class="bi bi-plus-circle"></i> Add Volunteer Experience
                </button>
            </div>
        </form>
    </div>
</x-modal>

