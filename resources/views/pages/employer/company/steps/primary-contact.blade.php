<form method="POST" action="{{ route('employer.company.build.save', ['step' => 4]) }}">
    @csrf

    <h2
        style="font-family: var(--font-bricolageGrotesque); font-weight: 600; font-size: 24px; color: var(--title-color); margin-bottom: 30px;">
        Primary Contact Information
    </h2>

    <p style="margin-bottom: 30px; color: #666; font-size: 14px;">
        Provide details for the primary contact person who will manage this company profile and receive notifications.
    </p>

    <!-- Primary Contact Name -->
    <div style="margin-bottom: 20px;">
        <label for="primary_contact_name" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Primary Contact Name
        </label>
        <input type="text" name="primary_contact_name" id="primary_contact_name"
            value="{{ old('primary_contact_name', $company->primary_contact_name) }}" placeholder="Full name"
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('primary_contact_name')
        <span style="color: red; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Primary Contact Title -->
    <div style="margin-bottom: 20px;">
        <label for="primary_contact_title" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Primary Contact Title
        </label>
        <input type="text" name="primary_contact_title" id="primary_contact_title"
            value="{{ old('primary_contact_title', $company->primary_contact_title) }}"
            placeholder="e.g. HR Manager, Recruiting Lead, CEO"
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('primary_contact_title')
        <span style="color: red; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Primary Contact Email -->
    <div style="margin-bottom: 20px;">
        <label for="primary_contact_email" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Primary Contact Email
        </label>
        <input type="email" name="primary_contact_email" id="primary_contact_email"
            value="{{ old('primary_contact_email', $company->primary_contact_email) }}"
            placeholder="contact@example.com"
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('primary_contact_email')
        <span style="color: red; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Primary Contact Phone -->
    <div style="margin-bottom: 30px;">
        <label for="primary_contact_phone" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Primary Contact Phone
        </label>
        <input type="text" name="primary_contact_phone" id="primary_contact_phone"
            value="{{ old('primary_contact_phone', $company->primary_contact_phone) }}"
            placeholder="e.g. +233 24 123 4567"
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('primary_contact_phone')
        <span style="color: red; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Navigation Buttons -->
    <div style="display: flex; justify-content: space-between; margin-top: 30px;">
        <a href="{{ route('employer.company.build.step', ['step' => 3]) }}" class="primary-btn2 btn-hover"
            style="padding: 12px 24px; font-size: 16px; text-decoration: none; display: inline-block;">
            Back
        </a>
        <button type="submit" class="primary-btn1 btn-hover" style="padding: 12px 24px; font-size: 16px;">
            Complete Profile
        </button>
    </div>
</form>