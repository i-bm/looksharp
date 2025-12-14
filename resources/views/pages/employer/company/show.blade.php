@extends('layouts.dashboard.main')

@php
    /** @var \App\Models\EmployerCompany $company */
@endphp

@section('content')
<div class="dashboard-container">
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <h2 class="dashboard-card-title">{{ $company->legal_name }}</h2>
                <p class="dashboard-card-subtitle">Company profile status: <strong>{{ $company->status }}</strong></p>
            </div>
            <div class="d-flex gap-2">
                @if($company->isEditableByEmployer())
                <a href="{{ route('employer.company.edit') }}" class="btn btn-outline-secondary">Edit</a>
                @endif
                @if($company->isApproved())
                <a href="{{ route('employer.company.public', ['id' => $company->id]) }}" class="btn btn-outline-primary" target="_blank" rel="noopener">View Public</a>
                @endif
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($company->status === \App\Enums\EmployerCompanyStatusEnum::NEEDS_CHANGES->value)
        <div class="alert alert-warning">
            <strong>Changes requested:</strong>
            <div class="mt-2">{{ $company->review_notes }}</div>
        </div>
        @endif

        @if($company->status === \App\Enums\EmployerCompanyStatusEnum::SUBMITTED->value)
        <div class="alert alert-info">
            Your company profile is pending review. You’ll be notified once it’s approved.
        </div>
        @endif

        @if($company->status === \App\Enums\EmployerCompanyStatusEnum::APPROVED->value)
        <div class="alert alert-success">
            Your company is approved. You can now access employer features.
        </div>
        @endif

        @if($company->status === \App\Enums\EmployerCompanyStatusEnum::REJECTED->value)
        <div class="alert alert-danger">
            <strong>Rejected:</strong>
            <div class="mt-2">{{ $company->review_notes }}</div>
        </div>
        @endif

        @if($company->status === \App\Enums\EmployerCompanyStatusEnum::SUSPENDED->value)
        <div class="alert alert-danger">
            <strong>Suspended:</strong>
            <div class="mt-2">{{ $company->review_notes }}</div>
        </div>
        @endif

        <div class="row g-3">
            <div class="col-md-6">
                <div><strong>Legal name</strong></div>
                <div>{{ $company->legal_name }}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Trading name</strong></div>
                <div>{{ $company->trading_name ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Industry</strong></div>
                <div>{{ $company->industry ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Company size</strong></div>
                <div>{{ $company->company_size ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Website</strong></div>
                <div>{{ $company->website ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div><strong>LinkedIn</strong></div>
                <div>{{ $company->linkedin_url ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div><strong>Country</strong></div>
                <div>{{ $company->country ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div><strong>City</strong></div>
                <div>{{ $company->city ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div><strong>Phone</strong></div>
                <div>{{ $company->phone_number ?? '—' }}</div>
            </div>
            <div class="col-12">
                <div><strong>Address</strong></div>
                <div>{{ $company->address ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Official email</strong></div>
                <div>{{ $company->official_email ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Registration number / TIN</strong></div>
                <div>{{ $company->registration_number ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Primary contact</strong></div>
                <div>{{ $company->primary_contact_name ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Primary contact title</strong></div>
                <div>{{ $company->primary_contact_title ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Primary contact email</strong></div>
                <div>{{ $company->primary_contact_email ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Primary contact phone</strong></div>
                <div>{{ $company->primary_contact_phone ?? '—' }}</div>
            </div>
        </div>

        @if($company->isEditableByEmployer())
        <hr>
        <form method="POST" action="{{ route('employer.company.submit') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Submit for Approval</button>
            <p class="text-muted mt-2 mb-0">Once submitted, your profile is locked until admin review.</p>
        </form>
        @endif
    </div>
</div>
@endsection

