@extends('layouts.admin.main')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-page-header">
        <div>
            <h1 class="dashboard-page-title">Talent Verifications</h1>
            <p class="dashboard-page-subtitle">Review and verify talent profile submissions</p>
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
        <form method="GET" action="{{ route('admin.talent-verifications.index') }}" class="dashboard-filter-form">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name, email, or student ID">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Verification Status</label>
                    <select name="verification_status" class="form-select">
                        <option value="" {{ request('verification_status', $verificationStatus ?? '') === '' ? 'selected' : '' }}>Pending (default)</option>
                        <option value="pending" {{ request('verification_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="verified" {{ request('verification_status') === 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="rejected" {{ request('verification_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Document Type</label>
                    <select name="verification_type" class="form-select">
                        <option value="" {{ request('verification_type', $verificationType ?? '') === '' ? 'selected' : '' }}>All</option>
                        <option value="student_id" {{ request('verification_type') === 'student_id' ? 'selected' : '' }}>Student ID</option>
                        <option value="ghana_card" {{ request('verification_type') === 'ghana_card' ? 'selected' : '' }}>Ghana Card</option>
                        <option value="passport" {{ request('verification_type') === 'passport' ? 'selected' : '' }}>Passport</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.talent-verifications.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="dashboard-card mt-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Talent</th>
                        <th>Email</th>
                        <th>Document Type</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($profiles as $profile)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $profile->full_name }}</div>
                            @if($profile->student_id)
                            <div class="text-muted small">Student ID: {{ $profile->student_id }}</div>
                            @endif
                        </td>
                        <td>{{ $profile->user->email ?? '—' }}</td>
                        <td>
                            @php
                                $hasStudentDoc = !empty($profile->student_verification_document_url);
                                $hasIdentityDoc = !empty($profile->verification_document_url);
                                $documents = [];
                                if ($hasStudentDoc) {
                                    $documents[] = 'Student ID';
                                }
                                if ($hasIdentityDoc && $profile->verification_type) {
                                    if ($profile->verification_type === 'ghana_card') {
                                        $documents[] = 'Ghana Card';
                                    } elseif ($profile->verification_type === 'passport') {
                                        $documents[] = 'Passport';
                                    } elseif ($profile->verification_type === 'student_id') {
                                        $documents[] = 'Student ID';
                                    } else {
                                        $documents[] = ucfirst(str_replace('_', ' ', $profile->verification_type));
                                    }
                                }
                            @endphp
                            @if(count($documents) > 0)
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($documents as $doc)
                                        @if($doc === 'Student ID')
                                            <span class="badge bg-info">{{ $doc }}</span>
                                        @elseif($doc === 'Ghana Card')
                                            <span class="badge bg-primary">{{ $doc }}</span>
                                        @elseif($doc === 'Passport')
                                            <span class="badge bg-secondary">{{ $doc }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $doc }}</span>
                                        @endif
                                    @endforeach
                                </div>
                                @if($hasStudentDoc && $hasIdentityDoc)
                                    <div class="text-muted small mt-1">
                                        <i class="bi bi-check-circle-fill text-success"></i> Both documents submitted
                                    </div>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($profile->verification_status === 'verified')
                                <span class="badge bg-success">Verified</span>
                            @elseif($profile->verification_status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>{{ $profile->created_at->format('Y-m-d H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.talent-verifications.show', ['id' => $profile->id]) }}" class="btn btn-sm btn-outline-primary">Review</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No talent verifications found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $profiles->links() }}
        </div>
    </div>
</div>
@endsection

