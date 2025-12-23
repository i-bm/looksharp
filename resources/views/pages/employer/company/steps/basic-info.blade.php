<form method="POST" action="{{ route('employer.company.build.save', ['step' => 1]) }}">
    @csrf

    <h2
        style="font-family: var(--font-bricolageGrotesque); font-weight: 600; font-size: 24px; color: var(--title-color); margin-bottom: 30px;">
        Basic Company Information
    </h2>

    <!-- Legal Company Name -->
    <div style="margin-bottom: 20px;">
        <label for="legal_name" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Legal Company Name <span style="color: red;">*</span>
        </label>
        <input type="text" name="legal_name" id="legal_name" value="{{ old('legal_name', $company->legal_name) }}"
            required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('legal_name')
        <span style="color: red; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Trading Name -->
    <div style="margin-bottom: 20px;">
        <label for="trading_name" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Trading Name (if different)
        </label>
        <input type="text" name="trading_name" id="trading_name"
            value="{{ old('trading_name', $company->trading_name) }}"
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('trading_name')
        <span style="color: red; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Industry -->
    @php
    $industryOptions = $industries->map(function($industry) {
    return [
    'value' => $industry->name,
    'label' => $industry->name,
    ];
    })->toArray();

    $othersIndustry = $industries->firstWhere('name', 'Others(Please Specify)');
    $otherOptionValue = $othersIndustry ? $othersIndustry->name : null;
    $currentIndustry = old('industry', $company->industry);
    $showOtherInput = $currentIndustry === 'Others(Please Specify)';
    @endphp

    <x-select-search name="industry" label="Industry" :options="$industryOptions" :value="$currentIndustry"
        placeholder="Select industry" searchPlaceholder="Search industries..." :otherOptionValue="$otherOptionValue"
        otherOptionLabel="Others(Please Specify)" :showOtherInput="true" otherInputName="other_industry"
        otherInputLabel="Please specify your industry" :otherInputValue="old('other_industry')" />

    <!-- Company Size -->
    <div style="margin-bottom: 30px;">
        <label for="company_size" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Company Size
        </label>
        <select name="company_size" id="company_size" required
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            <option value="" disabled {{ old('company_size', $company->company_size) ? '' : 'selected' }}>Select company
                size</option>
            <option value="1-10" {{ old('company_size', $company->company_size) == '1-10' ? 'selected' : '' }}>1-10
                employees</option>
            <option value="11-50" {{ old('company_size', $company->company_size) == '11-50' ? 'selected' : '' }}>11-50
                employees</option>
            <option value="51-200" {{ old('company_size', $company->company_size) == '51-200' ? 'selected' : ''
                }}>51-200 employees</option>
            <option value="201-500" {{ old('company_size', $company->company_size) == '201-500' ? 'selected' : ''
                }}>201-500 employees</option>
            <option value="500+" {{ old('company_size', $company->company_size) == '500+' ? 'selected' : '' }}>500+
                employees</option>
        </select>
        @error('company_size')
        <span style="color: red; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Navigation Buttons -->
    <div style="display: flex; justify-content: space-between; margin-top: 30px;">
        <div></div>
        <button type="submit" class="primary-btn1 btn-hover" style="padding: 12px 24px; font-size: 16px;">
            Continue to Step 2
        </button>
    </div>
</form>