<form class="company-section-edit-form" data-section="primary-contact" action="{{ route('employer.company.primary-contact.update') }}" method="POST">
    @csrf
    @method('PUT')

    <p class="text-muted mb-3">Provide details for the primary contact person who will manage this company profile and receive notifications.</p>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Primary Contact Name <span class="text-danger">*</span></label>
            <input type="text" name="primary_contact_name" class="form-control" value="{{ old('primary_contact_name', $company->primary_contact_name) }}" placeholder="Full name" required>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Primary Contact Title</label>
            <input type="text" name="primary_contact_title" class="form-control" value="{{ old('primary_contact_title', $company->primary_contact_title) }}" placeholder="e.g. HR Manager, Recruiting Lead, CEO">
            <div class="invalid-feedback"></div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Primary Contact Email</label>
            <input type="email" name="primary_contact_email" class="form-control" value="{{ old('primary_contact_email', $company->primary_contact_email) }}" placeholder="contact@example.com">
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Primary Contact Phone <span class="text-danger">*</span></label>
            <input type="text" name="primary_contact_phone" class="form-control" value="{{ old('primary_contact_phone', $company->primary_contact_phone) }}" placeholder="e.g. +233 24 123 4567" required>
            <div class="invalid-feedback"></div>
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

