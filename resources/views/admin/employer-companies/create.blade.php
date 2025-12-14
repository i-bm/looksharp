@extends('layouts.admin.main')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-page-header">
        <div>
            <h1 class="dashboard-page-title">Provision Employer Company</h1>
            <p class="dashboard-page-subtitle">Create a draft company and invite a company admin to log in.</p>
        </div>
        <div>
            <a href="{{ route('admin.employer-companies.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="dashboard-card">
        <form method="POST" action="{{ route('admin.employer-companies.store') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Invite email *</label>
                    <input type="email" name="invite_email" class="form-control" value="{{ old('invite_email') }}" required>
                    <div class="form-text">This user will be created if they don’t exist and invited to log in via OTP.</div>
                </div>
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
                    <input type="text" name="company_size" class="form-control" value="{{ old('company_size') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Official email</label>
                    <input type="email" name="official_email" class="form-control" value="{{ old('official_email') }}">
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
                <button type="submit" class="btn btn-primary">Provision & Send Invite</button>
            </div>
        </form>
    </div>
</div>
@endsection

