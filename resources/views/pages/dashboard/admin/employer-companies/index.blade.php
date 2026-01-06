@extends('layouts.admin.main')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-page-header">
        <div>
            <h1 class="dashboard-page-title">Employer Companies</h1>
            <p class="dashboard-page-subtitle">Review and approve employer companies</p>
        </div>
        <div>
            <a href="{{ route('admin.employer-companies.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Provision Company
            </a>
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
        <form method="GET" action="{{ route('admin.employer-companies.index') }}" class="dashboard-filter-form">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Company name or email">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="" {{ request('status', $status ?? '') === '' ? 'selected' : '' }}>Submitted (default)</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="needs_changes" {{ request('status') === 'needs_changes' ? 'selected' : '' }}>Needs changes</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Verification Status</label>
                    <select name="verification_status" class="form-select">
                        <option value="" {{ request('verification_status') === '' ? 'selected' : '' }}>All</option>
                        <option value="pending" {{ request('verification_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="verified" {{ request('verification_status') === 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="rejected" {{ request('verification_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.employer-companies.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="dashboard-card mt-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Verification</th>
                        <th>Submitted</th>
                        <th>Official Email</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $company->legal_name }}</div>
                            <div class="text-muted small">ID: {{ $company->id }}</div>
                        </td>
                        <td>{{ $company->status }}</td>
                        <td>
                            @if($company->verification_status === 'verified')
                                <span class="badge bg-success">Verified</span>
                            @elseif($company->verification_status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>{{ optional($company->submitted_at)->format('Y-m-d H:i') ?? '—' }}</td>
                        <td>{{ $company->official_email ?? '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.employer-companies.show', ['id' => $company->id]) }}" class="btn btn-sm btn-outline-primary">Review</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No companies found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $companies->links() }}
        </div>
    </div>
</div>
@endsection

