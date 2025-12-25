<form class="company-section-edit-form" data-section="contact-location"
    action="{{ route('employer.company.contact-location.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Country</label>
            <input type="text" name="country" class="form-control"
                value="{{ old('country', $company->country ?? 'Ghana') }}">
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-4">
            <label class="form-label">State or Region <span class="text-danger">*</span></label>
            <input type="text" name="state_or_region" id="state_or_region" class="form-control"
                value="{{ old('state_or_region', $company->state_or_region) }}"
                placeholder="Start typing to search regions..." required data-autocomplete
                data-autocomplete-url="/api/autocomplete/regions" data-autocomplete-min-length="1"
                data-autocomplete-allow-custom="true">
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-4">
            <label class="form-label">City <span class="text-danger">*</span></label>
            <input type="text" name="city" id="city" class="form-control" value="{{ old('city', $company->city) }}"
                placeholder="Start typing to search cities..." required data-autocomplete
                data-autocomplete-url="/api/autocomplete/cities" data-autocomplete-min-length="1"
                data-autocomplete-allow-custom="true">
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone_number" class="form-control"
                value="{{ old('phone_number', $company->phone_number) }}" placeholder="e.g. +233 24 123 4567">
            <div class="invalid-feedback"></div>
        </div>

        <div class="col-12">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="{{ old('address', $company->address) }}"
                placeholder="Street address">
            <div class="invalid-feedback"></div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Official Email</label>
            <input type="email" name="official_email" class="form-control"
                value="{{ old('official_email', $company->official_email) }}" placeholder="company@example.com">
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Website</label>
            <input type="url" name="website" class="form-control" value="{{ old('website', $company->website) }}"
                placeholder="https://www.example.com">
            <div class="invalid-feedback"></div>
        </div>

        <div class="col-12">
            <label class="form-label">LinkedIn URL</label>
            <input type="url" name="linkedin_url" class="form-control"
                value="{{ old('linkedin_url', $company->linkedin_url) }}"
                placeholder="https://www.linkedin.com/company/example">
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
