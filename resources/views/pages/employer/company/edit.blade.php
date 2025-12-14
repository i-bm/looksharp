@extends('layouts.dashboard.main')

@php
    /** @var \App\Models\EmployerCompany $company */
@endphp

@section('content')
<div class="dashboard-container">
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <h2 class="dashboard-card-title">Edit Company Profile</h2>
                <p class="dashboard-card-subtitle">Update your company details before submitting for approval.</p>
            </div>
            <div>
                <a href="{{ route('employer.company.show') }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('employer.company.update') }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Legal company name *</label>
                    <input type="text" name="legal_name" class="form-control" value="{{ old('legal_name', $company->legal_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Trading name</label>
                    <input type="text" name="trading_name" class="form-control" value="{{ old('trading_name', $company->trading_name) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Industry</label>
                    <input type="text" name="industry" class="form-control" value="{{ old('industry', $company->industry) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Company size</label>
                    <input type="text" name="company_size" class="form-control" value="{{ old('company_size', $company->company_size) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Website</label>
                    <input type="url" name="website" class="form-control" value="{{ old('website', $company->website) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">LinkedIn URL</label>
                    <input type="url" name="linkedin_url" class="form-control" value="{{ old('linkedin_url', $company->linkedin_url) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Country</label>
                    <input type="text" name="country" class="form-control" value="{{ old('country', $company->country ?? 'Ghana') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $company->city) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phone number</label>
                    <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $company->phone_number) }}">
                </div>

                <div class="col-12">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $company->address) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Official email</label>
                    <input type="email" name="official_email" class="form-control" value="{{ old('official_email', $company->official_email) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Registration number / TIN</label>
                    <input type="text" name="registration_number" class="form-control" value="{{ old('registration_number', $company->registration_number) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Primary contact name</label>
                    <input type="text" name="primary_contact_name" class="form-control" value="{{ old('primary_contact_name', $company->primary_contact_name) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Primary contact title</label>
                    <input type="text" name="primary_contact_title" class="form-control" value="{{ old('primary_contact_title', $company->primary_contact_title) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Primary contact email</label>
                    <input type="email" name="primary_contact_email" class="form-control" value="{{ old('primary_contact_email', $company->primary_contact_email) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Primary contact phone</label>
                    <input type="text" name="primary_contact_phone" class="form-control" value="{{ old('primary_contact_phone', $company->primary_contact_phone) }}">
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('employer.company.show') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

