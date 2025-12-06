<x-modal id="leadership-experience-modal" title="Edit Leadership Experiences" size="large">
    <div>
        <!-- Existing Leadership Experiences -->
        @if($profile->leadershipExperiences->count() > 0)
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 15px; color: var(--title-color);">Your Leadership Experiences ({{ $profile->leadershipExperiences->count() }})</h3>
            @foreach($profile->leadershipExperiences as $leadership)
            <div
                style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>{{ $leadership->organization }}</strong>
                    @if($leadership->title)
                    <br><span style="color: #666; font-size: 14px;">{{ $leadership->title }}</span>
                    @endif
                    <br><span style="color: #666; font-size: 14px;">
                        <i class="bi bi-calendar3"></i>
                        {{ $leadership->start_date?->format('M Y') }}
                        @if($leadership->is_current)
                        - Present
                        @elseif($leadership->end_date)
                        - {{ $leadership->end_date->format('M Y') }}
                        @endif
                    </span>
                </div>
                <button type="button" onclick="deleteLeadershipExperience('{{ $leadership->id }}')"
                    style="background: #f44336; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">
                    Remove
                </button>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Add Leadership Experience Form -->
        <form id="add-leadership-form" onsubmit="submitAddLeadershipExperience(event)">
            @csrf
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 20px; color: var(--title-color);">{{ $profile->leadershipExperiences->count() > 0 ? 'Add Another Leadership Experience' : 'Add Leadership Experience' }}</h3>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Organization <span style="color: #F53003;">*</span>
                </label>
                <input type="text" name="organization" id="leadership-organization" required
                    placeholder="e.g., Student Council, Sports Club"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Title/Position (Optional)
                </label>
                <input type="text" name="title" id="leadership-title"
                    placeholder="e.g., President, Vice President, Team Captain"
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
                'prefix' => 'leadership_start_date',
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
                    <input type="checkbox" name="is_current" id="leadership-is-current" value="1" onchange="toggleLeadershipEndDate(this)">
                    <span style="font-weight: 500;">Currently in this role</span>
                </label>
            </div>

            <div id="leadership-end-date-container" style="margin-bottom: 20px;">
                @include('pages.profile.partials.date-selector', [
                'label' => 'End Date',
                'required' => false,
                'prefix' => 'leadership_end_date',
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
                <textarea name="details" id="leadership-details" rows="3"
                    placeholder="Describe your leadership role and achievements..."
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 25px;">
                <button type="button" onclick="closeModal('leadership-experience-modal')" 
                    style="padding: 10px 20px; border: 2px solid #ddd; border-radius: 4px; background: white; color: var(--text-color); cursor: pointer; font-size: 14px;">
                    Close
                </button>
                <button type="submit" class="primary-btn1 btn-hover"
                    style="padding: 10px 20px; border: none; cursor: pointer; font-size: 14px;">
                    <i class="bi bi-plus-circle"></i> Add Leadership Experience
                </button>
            </div>
        </form>
    </div>
</x-modal>

