<form method="POST" action="{{ route('employer.company.build.save', ['step' => 2]) }}">
    @csrf

    <h2
        style="font-family: var(--font-bricolageGrotesque); font-weight: 600; font-size: 24px; color: var(--title-color); margin-bottom: 30px;">
        Contact & Location Information
    </h2>

    <!-- Country -->
    <div style="margin-bottom: 20px;">
        <label for="country" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Country
        </label>
        <input type="text" name="country" id="country" value="{{ old('country', $company->country ?? 'Ghana') }}"
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('country')
        <span style="color: red; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    <!-- City -->
    <div style="margin-bottom: 20px;">
        <label for="city" style="display: block; margin-bottom: 8px; font-weight: 500;">
            City
        </label>
        <input type="text" name="city" id="city" value="{{ old('city', $company->city) }}" placeholder="e.g. Accra"
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('city')
        <span style="color: red; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Address -->
    <div style="margin-bottom: 20px;">
        <label for="address" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Address
        </label>
        <input type="text" name="address" id="address" value="{{ old('address', $company->address) }}"
            placeholder="Street address"
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('address')
        <span style="color: red; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Phone Number -->
    <div style="margin-bottom: 20px;">
        <label for="phone_number" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Phone Number
        </label>
        <input type="text" name="phone_number" id="phone_number"
            value="{{ old('phone_number', $company->phone_number) }}" placeholder="e.g. +233 24 123 4567"
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('phone_number')
        <span style="color: red; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Official Email -->
    <div style="margin-bottom: 20px;">
        <label for="official_email" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Official Email
        </label>
        <input type="email" name="official_email" id="official_email"
            value="{{ old('official_email', $company->official_email) }}" placeholder="company@example.com"
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('official_email')
        <span style="color: red; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Website -->
    <div style="margin-bottom: 20px;">
        <label for="website" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Website
        </label>
        <input type="url" name="website" id="website" value="{{ old('website', $company->website) }}"
            placeholder="https://www.example.com"
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('website')
        <span style="color: red; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    <!-- LinkedIn URL -->
    <div style="margin-bottom: 30px;">
        <label for="linkedin_url" style="display: block; margin-bottom: 8px; font-weight: 500;">
            LinkedIn URL
        </label>
        <input type="url" name="linkedin_url" id="linkedin_url"
            value="{{ old('linkedin_url', $company->linkedin_url) }}"
            placeholder="https://www.linkedin.com/company/example"
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('linkedin_url')
        <span style="color: red; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Navigation Buttons -->
    <div style="display: flex; justify-content: space-between; margin-top: 30px;">
        <a href="{{ route('employer.company.build.step', ['step' => 1]) }}" class="primary-btn2 btn-hover"
            style="padding: 12px 24px; font-size: 16px; text-decoration: none; display: inline-block;">
            Back
        </a>
        <button type="submit" class="primary-btn1 btn-hover" style="padding: 12px 24px; font-size: 16px;">
            Continue to Step 3
        </button>
    </div>
</form>