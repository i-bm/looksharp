@php
$industryOptions = $industries ? $industries->map(function($industry) {
return [
'value' => $industry->name,
'label' => $industry->name,
];
})->toArray() : [];

$othersIndustry = $industries ? $industries->firstWhere('name', 'Others(Please Specify)') : null;
$otherOptionValue = $othersIndustry ? $othersIndustry->name : null;
$currentIndustry = old('industry', $company->industry);
$showOtherInput = $currentIndustry === 'Others(Please Specify)';
@endphp

<form class="company-section-edit-form" data-section="basic-info"
    action="{{ route('employer.company.basic-info.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Legal Company Name <span class="text-danger">*</span></label>
            <input type="text" name="legal_name" class="form-control"
                value="{{ old('legal_name', $company->legal_name) }}" required>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Trading Name (if different)</label>
            <input type="text" name="trading_name" class="form-control"
                value="{{ old('trading_name', $company->trading_name) }}">
            <div class="invalid-feedback"></div>
        </div>

        <div class="col-md-6">
            <x-select-search name="industry" label="Industry" :options="$industryOptions" :value="$currentIndustry"
                placeholder="Select industry" searchPlaceholder="Search industries..."
                :otherOptionValue="$otherOptionValue" otherOptionLabel="Others(Please Specify)" :showOtherInput="true"
                otherInputName="other_industry" otherInputLabel="Please specify your industry"
                :otherInputValue="old('other_industry', $company->industry && $company->industry !== 'Others(Please Specify)' && !in_array($company->industry, array_column($industryOptions, 'value')) ? $company->industry : '')" :required="true" />
        </div>
        <div class="col-md-6">
            <label class="form-label">Company Size <span class="text-danger">*</span></label>
            <select name="company_size" id="company_size" class="form-control" required>
                <option value="" {{ old('company_size', $company->company_size) ? '' : 'selected' }}>Select company size
                </option>
                <option value="1-10" {{ old('company_size', $company->company_size) == '1-10' ? 'selected' : '' }}>1-10
                    employees</option>
                <option value="11-50" {{ old('company_size', $company->company_size) == '11-50' ? 'selected' : ''
                    }}>11-50 employees</option>
                <option value="51-200" {{ old('company_size', $company->company_size) == '51-200' ? 'selected' : ''
                    }}>51-200 employees</option>
                <option value="201-500" {{ old('company_size', $company->company_size) == '201-500' ? 'selected' : ''
                    }}>201-500 employees</option>
                <option value="500+" {{ old('company_size', $company->company_size) == '500+' ? 'selected' : '' }}>500+
                    employees</option>
            </select>
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