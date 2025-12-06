<x-modal id="demographics-modal" title="Demographics & More" size="medium">
    <div>
        <p style="color: #666; margin-bottom: 20px;">Edit your demographic information and additional details.</p>
        <div style="display: flex; flex-direction: column; gap: 15px; margin-bottom: 25px;">
            @if($profile->date_of_birth)
            <div>
                <span style="color: #666; font-size: 14px; display: block; margin-bottom: 5px;">Date of Birth</span>
                <p style="margin: 0; color: var(--title-color); font-weight: 500;">
                    {{ $profile->date_of_birth->format('F j, Y') }}
                </p>
            </div>
            @endif

            @if($profile->gender)
            <div>
                <span style="color: #666; font-size: 14px; display: block; margin-bottom: 5px;">Gender</span>
                <p style="margin: 0; color: var(--title-color); font-weight: 500;">
                    {{ ucfirst($profile->gender) }}
                </p>
            </div>
            @endif

            @if($profile->nss_status)
            <div>
                <span style="color: #666; font-size: 14px; display: block; margin-bottom: 5px;">NSS Status</span>
                <p style="margin: 0; color: var(--title-color); font-weight: 500;">
                    {{ $profile->nss_status }}
                </p>
                @if($profile->nss_posting_location)
                <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">
                    {{ $profile->nss_posting_location }}
                </p>
                @endif
            </div>
            @endif

            @if($profile->verification_status)
            <div>
                <span style="color: #666; font-size: 14px; display: block; margin-bottom: 5px;">Verification</span>
                <p style="margin: 0;">
                    <span
                        style="background: {{ $profile->verification_status === 'verified' ? '#4CAF50' : '#ff9800' }}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;">
                        {{ ucfirst($profile->verification_status) }}
                    </span>
                </p>
            </div>
            @endif
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 25px;">
            <button type="button" onclick="closeModal('demographics-modal')" 
                style="padding: 10px 20px; border: 2px solid #ddd; border-radius: 4px; background: white; color: var(--text-color); cursor: pointer; font-size: 14px;">
                Close
            </button>
            <a href="{{ route('talent.profile.edit') }}" class="primary-btn1 btn-hover"
                style="text-decoration: none; padding: 10px 20px; display: inline-block; font-size: 14px;">
                <i class="bi bi-pencil"></i> Edit Full Profile
            </a>
        </div>
    </div>
</x-modal>

