@extends('layouts.admin.main')

@php
    /** @var \App\Models\EmployerCompany $company */
@endphp

@section('content')
<div class="dashboard-container">
    <div class="dashboard-page-header">
        <div>
            <h1 class="dashboard-page-title">{{ $company->legal_name }}</h1>
            <p class="dashboard-page-subtitle">Status: <strong>{{ $company->status }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.employer-companies.index') }}" class="btn btn-outline-secondary">Back</a>
            <a href="{{ route('employer.company.public', ['id' => $company->id]) }}" class="btn btn-outline-primary" target="_blank" rel="noopener">Public</a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

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
        <div class="row g-3">
            <div class="col-md-6">
                <div class="text-muted small">Company ID</div>
                <div class="fw-semibold">{{ $company->id }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Submitted at</div>
                <div class="fw-semibold">{{ optional($company->submitted_at)->format('Y-m-d H:i') ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Official email</div>
                <div class="fw-semibold">{{ $company->official_email ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Phone</div>
                <div class="fw-semibold">{{ $company->phone_number ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Industry</div>
                <div class="fw-semibold">{{ $company->industry ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Company size</div>
                <div class="fw-semibold">{{ $company->company_size ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Website</div>
                <div class="fw-semibold">{{ $company->website ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">LinkedIn</div>
                <div class="fw-semibold">{{ $company->linkedin_url ?? '—' }}</div>
            </div>
            <div class="col-12">
                <div class="text-muted small">Location</div>
                <div class="fw-semibold">{{ trim(($company->city ?? '').' '.($company->country ?? '')) ?: '—' }}</div>
            </div>
            <div class="col-12">
                <div class="text-muted small">Address</div>
                <div class="fw-semibold">{{ $company->address ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Registration number / TIN</div>
                <div class="fw-semibold">{{ $company->registration_number ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Primary contact</div>
                <div class="fw-semibold">{{ $company->primary_contact_name ?? '—' }} @if($company->primary_contact_title) ({{ $company->primary_contact_title }}) @endif</div>
                <div class="text-muted small">{{ $company->primary_contact_email ?? '' }} @if($company->primary_contact_phone) · {{ $company->primary_contact_phone }} @endif</div>
            </div>
        </div>

        @if($company->review_notes)
        <hr>
        <div>
            <div class="text-muted small">Latest review notes</div>
            <div class="fw-semibold">{{ $company->review_notes }}</div>
        </div>
        @endif
    </div>

    <div class="dashboard-card mt-3">
        <h3 class="h5 mb-3">Review actions</h3>

        <div class="row g-3">
            <div class="col-lg-6">
                <form method="POST" action="{{ route('admin.employer-companies.approve', ['id' => $company->id]) }}">
                    @csrf
                    <label class="form-label">Approve (optional notes)</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes">{{ old('notes') }}</textarea>
                    <button type="submit" class="btn btn-success mt-2">Approve</button>
                </form>
            </div>

            <div class="col-lg-6">
                <form method="POST" action="{{ route('admin.employer-companies.needs-changes', ['id' => $company->id]) }}">
                    @csrf
                    <label class="form-label">Request changes (notes required)</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="What should the employer fix?" required>{{ old('notes') }}</textarea>
                    <button type="submit" class="btn btn-warning mt-2">Needs Changes</button>
                </form>
            </div>

            <div class="col-lg-6">
                <form method="POST" action="{{ route('admin.employer-companies.reject', ['id' => $company->id]) }}">
                    @csrf
                    <label class="form-label">Reject (notes required)</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Reason for rejection" required>{{ old('notes') }}</textarea>
                    <button type="submit" class="btn btn-danger mt-2">Reject</button>
                </form>
            </div>

            <div class="col-lg-6">
                <form method="POST" action="{{ route('admin.employer-companies.suspend', ['id' => $company->id]) }}">
                    @csrf
                    <label class="form-label">Suspend (notes required)</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Reason for suspension" required>{{ old('notes') }}</textarea>
                    <button type="submit" class="btn btn-danger mt-2">Suspend</button>
                </form>
            </div>
        </div>
    </div>

    <div class="dashboard-card mt-3">
        <h3 class="h5 mb-3">Company admins</h3>
        <ul class="mb-0">
            @foreach($company->members as $member)
            <li>{{ $member->email }} <span class="text-muted">({{ $member->pivot->role }})</span></li>
            @endforeach
        </ul>
    </div>
</div>
@endsection

