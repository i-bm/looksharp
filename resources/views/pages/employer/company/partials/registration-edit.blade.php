<form class="company-section-edit-form" data-section="registration"
    action="{{ route('employer.company.registration.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-3">
        <div class="col-12">
            <label class="form-label">Registration Number / TIN <span class="text-danger">*</span></label>
            <input type="text" name="registration_number" class="form-control"
                value="{{ old('registration_number', $company->registration_number) }}"
                placeholder="Company registration number or Tax Identification Number" required>
            <div class="invalid-feedback"></div>
            <small class="form-text text-muted">
                This information helps verify your company's legitimacy and may be required for posting opportunities.
            </small>
        </div>
    </div>

    <!-- Verification Documents Section -->
    <div class="mt-4 pt-4 border-top">
        <h5 class="mb-3">Verification Documents</h5>
        <p class="text-muted small mb-3">Upload required documents for business verification. These documents are
            required
            before you can post paid opportunities.</p>

        <!-- Ghana Card Upload -->
        <div class="mb-4">
            <label class="form-label">Ghana Card (Owner/Representative) <span class="text-danger">*</span></label>
            <div class="verification-document-upload" data-type="ghana_card">
                @if($company->ghana_card_document_url)
                <div class="alert alert-success mb-2">
                    <i class="bi bi-check-circle"></i> Ghana Card document uploaded
                    @if($company->verification_status === 'verified')
                    <span class="badge bg-success ms-2">Verified</span>
                    @elseif($company->verification_status === 'rejected')
                    <span class="badge bg-danger ms-2">Rejected</span>
                    @else
                    <span class="badge bg-warning ms-2">Pending Review</span>
                    @endif
                </div>
                @endif
                <input type="file" name="ghana_card" id="ghana_card_upload" class="form-control"
                    accept=".pdf,.jpg,.jpeg,.png">
                <small class="form-text text-muted">Accepted formats: PDF, JPG, PNG (Max: 10MB)</small>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <!-- Business Registration Document Upload -->
        <div class="mb-4">
            <label class="form-label">Business Registration Document <span class="text-danger">*</span></label>
            <div class="verification-document-upload" data-type="business_registration">
                @if($company->business_registration_document_url)
                <div class="alert alert-success mb-2">
                    <i class="bi bi-check-circle"></i> Business registration document uploaded
                    @if($company->verification_status === 'verified')
                    <span class="badge bg-success ms-2">Verified</span>
                    @elseif($company->verification_status === 'rejected')
                    <span class="badge bg-danger ms-2">Rejected</span>
                    @else
                    <span class="badge bg-warning ms-2">Pending Review</span>
                    @endif
                </div>
                @endif
                <input type="file" name="business_registration" id="business_registration_upload" class="form-control"
                    accept=".pdf,.jpg,.jpeg,.png">
                <small class="form-text text-muted">Accepted formats: PDF, JPG, PNG (Max: 10MB)</small>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="alert alert-info">
            <strong>Note:</strong> Your documents will be reviewed by our admin team. You'll be notified once
            verification
            is complete.
        </div>
    </div>

    <div class="mt-3 d-flex gap-2 justify-content-end">
        <button type="button" class="btn btn-outline-secondary btn-cancel-edit">Cancel</button>
        <button type="submit" class="primary-btn1">
            Save Changes
            <span></span>
        </button>
    </div>
</form>
