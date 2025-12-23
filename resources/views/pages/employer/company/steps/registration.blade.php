<form method="POST" action="{{ route('employer.company.build.save', ['step' => 3]) }}">
    @csrf

    <h2
        style="font-family: var(--font-bricolageGrotesque); font-weight: 600; font-size: 24px; color: var(--title-color); margin-bottom: 30px;">
        Registration & Verification
    </h2>

    <!-- Registration Number / TIN -->
    <div style="margin-bottom: 30px;">
        <label for="registration_number" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Registration Number / TIN
        </label>
        <input type="text" name="registration_number" id="registration_number"
            value="{{ old('registration_number', $company->registration_number) }}"
            placeholder="Company registration number or Tax Identification Number"
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('registration_number')
        <span style="color: red; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
        @enderror
        <p style="margin-top: 8px; font-size: 14px; color: #666;">
            This information helps verify your company's legitimacy and may be required for posting opportunities.
        </p>
    </div>

    <!-- Note about verification documents (for future implementation) -->
    <div
        style="background: #f0f7ff; padding: 15px; border-radius: 4px; border-left: 4px solid #0066cc; margin-bottom: 30px;">
        <p style="margin: 0; font-size: 14px; color: #333;">
            <strong>Note:</strong> Additional verification documents may be required during the admin review process.
            You'll be notified if any additional documentation is needed.
        </p>
    </div>

    <!-- Navigation Buttons -->
    <div style="display: flex; justify-content: space-between; margin-top: 30px;">
        <a href="{{ route('employer.company.build.step', ['step' => 2]) }}" class="primary-btn2 btn-hover"
            style="padding: 12px 24px; font-size: 16px; text-decoration: none; display: inline-block;">
            Back
        </a>
        <button type="submit" class="primary-btn1 btn-hover" style="padding: 12px 24px; font-size: 16px;">
            Continue to Step 4
        </button>
    </div>
</form>