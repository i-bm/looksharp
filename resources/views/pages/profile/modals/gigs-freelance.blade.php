<x-modal id="gigs-freelance-modal" title="Edit Gigs / Freelance" size="large">
    <div>
        <!-- Existing Gigs/Freelance -->
        @if($profile->gigsFreelance->count() > 0)
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 15px; color: var(--title-color);">Your Gigs/Freelance Work ({{ $profile->gigsFreelance->count() }})</h3>
            @foreach($profile->gigsFreelance as $gig)
            <div
                style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>{{ $gig->company }}</strong>
                    @if($gig->title)
                    <br><span style="color: #666; font-size: 14px;">{{ $gig->title }}</span>
                    @endif
                    <br><span style="color: #666; font-size: 14px;">
                        <i class="bi bi-calendar3"></i>
                        {{ $gig->start_date?->format('M Y') }}
                        @if($gig->is_current)
                        - Present
                        @elseif($gig->end_date)
                        - {{ $gig->end_date->format('M Y') }}
                        @endif
                    </span>
                </div>
                <button type="button" onclick="deleteGigsFreelance('{{ $gig->id }}')"
                    style="background: #f44336; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">
                    Remove
                </button>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Add Gigs/Freelance Form -->
        <form id="add-gigs-freelance-form" onsubmit="submitAddGigsFreelance(event)">
            @csrf
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 20px; color: var(--title-color);">{{ $profile->gigsFreelance->count() > 0 ? 'Add Another Gig/Freelance Work' : 'Add Gigs/Freelance Work' }}</h3>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Company/Client <span style="color: #F53003;">*</span>
                </label>
                <input type="text" name="company" id="gigs-company" required
                    placeholder="e.g., ABC Company, Freelance Client"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Title/Project Name (Optional)
                </label>
                <input type="text" name="title" id="gigs-title"
                    placeholder="e.g., Website Redesign, Logo Design"
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
                'prefix' => 'gigs_start_date',
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
                    <input type="checkbox" name="is_current" id="gigs-is-current" value="1" onchange="toggleGigsEndDate(this)">
                    <span style="font-weight: 500;">Currently working on this</span>
                </label>
            </div>

            <div id="gigs-end-date-container" style="margin-bottom: 20px;">
                @include('pages.profile.partials.date-selector', [
                'label' => 'End Date',
                'required' => false,
                'prefix' => 'gigs_end_date',
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
                <textarea name="details" id="gigs-details" rows="3"
                    placeholder="Describe the project and your contributions..."
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 25px;">
                <button type="button" onclick="closeModal('gigs-freelance-modal')" 
                    style="padding: 10px 20px; border: 2px solid #ddd; border-radius: 4px; background: white; color: var(--text-color); cursor: pointer; font-size: 14px;">
                    Close
                </button>
                <button type="submit" class="primary-btn1 btn-hover"
                    style="padding: 10px 20px; border: none; cursor: pointer; font-size: 14px;">
                    <i class="bi bi-plus-circle"></i> Add Gig/Freelance Work
                </button>
            </div>
        </form>
    </div>
</x-modal>

