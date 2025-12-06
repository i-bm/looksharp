<x-modal id="certifications-modal" title="Edit Certifications" size="large">
    <div>
        <!-- Existing Certifications -->
        @if($profile->certifications->count() > 0)
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 15px; color: var(--title-color);">Your Certifications ({{ $profile->certifications->count() }})</h3>
            @foreach($profile->certifications as $cert)
            <div
                style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>{{ $cert->name }}</strong><br>
                    <span style="color: #666; font-size: 14px;">Issued by {{ $cert->issuer }}</span><br>
                    <span style="color: #666; font-size: 14px;">
                        <i class="bi bi-calendar3"></i>
                        Obtained: {{ $cert->date_obtained->format('M Y') }}
                        @if($cert->expiration_date)
                        | Expires: {{ $cert->expiration_date->format('M Y') }}
                        @endif
                    </span>
                    @if($cert->credential_url)
                    <br><a href="{{ $cert->credential_url }}" target="_blank"
                        style="color: #2196F3; font-size: 14px;">View Credential <i
                            class="bi bi-box-arrow-up-right"></i></a>
                    @endif
                </div>
                <button type="button" onclick="deleteCertification('{{ $cert->id }}')"
                    style="background: #f44336; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">
                    Remove
                </button>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Add Certification Form -->
        <form id="add-certification-form" onsubmit="submitAddCertification(event)">
            @csrf
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 20px; color: var(--title-color);">{{ $profile->certifications->count() > 0 ? 'Add Another Certification' : 'Add Certification' }}</h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Certification Name <span style="color: #F53003;">*</span>
                    </label>
                    <input type="text" name="name" id="cert-name" required
                        placeholder="e.g., Google Analytics Certified"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Issuer/Organization <span style="color: #F53003;">*</span>
                    </label>
                    <input type="text" name="issuer" id="cert-issuer" required
                        placeholder="e.g., Google, Microsoft"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>
            </div>

            @php
            $currentYear = (int) date('Y');
            $yearOptions = getYearOptions($currentYear - 50, $currentYear + 50);
            @endphp

            <div style="margin-bottom: 20px;">
                @include('pages.profile.partials.date-selector', [
                'label' => 'Date Obtained',
                'required' => true,
                'prefix' => 'cert_date_obtained',
                'dayValue' => '',
                'monthValue' => '',
                'yearValue' => '',
                'yearOptions' => $yearOptions,
                'errorKey' => 'date_obtained'
                ])
            </div>

            <div style="margin-bottom: 20px;">
                @include('pages.profile.partials.date-selector', [
                'label' => 'Expiration Date (Optional)',
                'required' => false,
                'prefix' => 'cert_expiration_date',
                'dayValue' => '',
                'monthValue' => '',
                'yearValue' => '',
                'yearOptions' => $yearOptions,
                'errorKey' => 'expiration_date'
                ])
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Credential URL (Optional)
                </label>
                <input type="url" name="credential_url" id="cert-credential-url"
                    placeholder="https://credly.com/badges/..."
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 25px;">
                <button type="button" onclick="closeModal('certifications-modal')" 
                    style="padding: 10px 20px; border: 2px solid #ddd; border-radius: 4px; background: white; color: var(--text-color); cursor: pointer; font-size: 14px;">
                    Close
                </button>
                <button type="submit" class="primary-btn1 btn-hover"
                    style="padding: 10px 20px; border: none; cursor: pointer; font-size: 14px;">
                    <i class="bi bi-plus-circle"></i> Add Certification
                </button>
            </div>
        </form>
    </div>
</x-modal>

