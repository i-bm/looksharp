<x-modal id="work-history-modal" title="Edit Work History" size="large">
    <div>
        <!-- Existing Work History -->
        @if($profile->workHistory->count() > 0)
        <div class="mb-30">
            <h3 class="modal-section-title">Your Work History ({{ $profile->workHistory->count() }})</h3>
            @foreach($profile->workHistory as $work)
            <div class="modal-item">
                <div class="modal-item-content">
                    <strong>{{ $work->position }}</strong> at <strong>{{ $work->company }}</strong><br>
                    <span class="modal-item-meta">
                        <i class="bi bi-calendar3"></i>
                        {{ $work->start_date?->format('M Y') }}
                        @if($work->is_current)
                        - Present
                        @elseif($work->end_date)
                        - {{ $work->end_date->format('M Y') }}
                        @endif
                    </span>
                    @if($work->location)
                    <br><span class="modal-item-meta"><i class="bi bi-geo-alt"></i> {{ $work->location }}</span>
                    @endif
                </div>
                <button type="button" onclick="deleteWorkHistory('{{ $work->id }}')" class="modal-btn-remove">
                    Remove
                </button>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Add Work History Form -->
        <form id="add-work-history-form" onsubmit="submitAddWorkHistory(event)">
            @csrf
            <h3 class="modal-section-title">{{ $profile->workHistory->count() > 0 ? 'Add Another Work Experience' : 'Add Work Experience' }}</h3>

            <div class="modal-form-grid">
                <div class="modal-form-field">
                    <label class="modal-label">
                        Company <span class="required-indicator">*</span>
                    </label>
                    <input type="text" name="company" id="work-company" required
                        placeholder="e.g., ABC Company" class="modal-input">
                </div>

                <div class="modal-form-field">
                    <label class="modal-label">
                        Position/Title <span class="required-indicator">*</span>
                    </label>
                    <input type="text" name="position" id="work-position" required
                        placeholder="e.g., Barista, Babysitter, Intern" class="modal-input">
                </div>
            </div>

            <div class="modal-form-field">
                <label class="modal-label">
                    Description
                </label>
                <textarea name="description" id="work-description" rows="3"
                    placeholder="Describe your responsibilities and achievements..." class="modal-textarea"></textarea>
            </div>

            <div class="modal-form-field">
                <label class="modal-label">
                    Location
                </label>
                <input type="text" name="location" id="work-location" placeholder="e.g., Accra, Ghana"
                    class="modal-input">
            </div>

            @php
            $currentYear = (int) date('Y');
            $yearOptions = getYearOptions($currentYear - 50, $currentYear + 10);
            @endphp

            <div style="margin-bottom: 20px;">
                @include('pages.profile.partials.date-selector', [
                'label' => 'Start Date',
                'required' => true,
                'prefix' => 'work_start_date',
                'dayValue' => '',
                'monthValue' => '',
                'yearValue' => '',
                'yearOptions' => $yearOptions,
                'errorKey' => 'start_date'
                ])
            </div>

            <div class="modal-form-field">
                <input type="hidden" name="is_current" value="0">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_current" id="work-is-current" value="1" onchange="toggleWorkEndDate(this)">
                    <span class="checkbox-label-text">Currently working here</span>
                </label>
            </div>

            <div id="work-end-date-container" class="modal-form-field">
                @include('pages.profile.partials.date-selector', [
                'label' => 'End Date',
                'required' => false,
                'prefix' => 'work_end_date',
                'dayValue' => '',
                'monthValue' => '',
                'yearValue' => '',
                'yearOptions' => $yearOptions,
                'errorKey' => 'end_date'
                ])
            </div>

            <div class="modal-form-actions">
                <button type="button" onclick="closeModal('work-history-modal')" class="modal-btn-close">
                    Close
                </button>
                <button type="submit" class="primary-btn1 btn-hover" style="padding: 10px 20px; font-size: 14px;">
                    <i class="bi bi-plus-circle"></i> Add Work Experience
                </button>
            </div>
        </form>
    </div>
</x-modal>

