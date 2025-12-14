@extends('layouts.dashboard.main')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <h2 class="dashboard-card-title">Create Company Profile</h2>
                <p class="dashboard-card-subtitle">Create your company profile, then submit for admin approval.</p>
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

        <form method="POST" action="{{ route('employer.company.store') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Legal company name *</label>
                    <input type="text" name="legal_name" class="form-control" value="{{ old('legal_name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Trading name</label>
                    <input type="text" name="trading_name" class="form-control" value="{{ old('trading_name') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Industry</label>
                    <input type="text" name="industry" class="form-control" value="{{ old('industry') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Company size</label>
                    <input type="text" name="company_size" class="form-control" value="{{ old('company_size') }}" placeholder="e.g. 1-10, 11-50, 51-200">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Website</label>
                    <input type="url" name="website" class="form-control" value="{{ old('website') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">LinkedIn URL</label>
                    <input type="url" name="linkedin_url" class="form-control" value="{{ old('linkedin_url') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Country</label>
                    <input type="text" name="country" class="form-control" value="{{ old('country', 'Ghana') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phone number</label>
                    <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}">
                </div>

                <div class="col-12">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Official email</label>
                    <input type="email" name="official_email" class="form-control" value="{{ old('official_email') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Registration number / TIN</label>
                    <input type="text" name="registration_number" class="form-control" value="{{ old('registration_number') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Primary contact name</label>
                    <input type="text" name="primary_contact_name" class="form-control" value="{{ old('primary_contact_name') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Primary contact title</label>
                    <input type="text" name="primary_contact_title" class="form-control" value="{{ old('primary_contact_title') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Primary contact email</label>
                    <input type="email" name="primary_contact_email" class="form-control" value="{{ old('primary_contact_email') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Primary contact phone</label>
                    <input type="text" name="primary_contact_phone" class="form-control" value="{{ old('primary_contact_phone') }}">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Save Company Profile</button>
            </div>
        </form>
    </div>
</div>
@endsection

