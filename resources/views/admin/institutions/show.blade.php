@extends('layouts.admin.main')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-page-header">
        <div>
            <a href="{{ route('admin.institutions.index') }}" class="dashboard-back-link">
                <i class="bi bi-arrow-left"></i> Back to Institutions
            </a>
            <h1 class="dashboard-page-title">Institution Details</h1>
            <p class="dashboard-page-subtitle">View institution information</p>
        </div>
        <div>
            <a href="{{ route('admin.institutions.edit', $institution->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2 class="dashboard-card-title">Institution Information</h2>
                    </div>
                    <div>
                        <span
                            class="dashboard-status-badge dashboard-status-{{ $institution->is_active ? 'success' : 'danger' }}">
                            {{ $institution->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Name</dt>
                        <dd class="col-sm-9"><strong>{{ $institution->name }}</strong></dd>

                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9">{{ $institution->email ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Student Email Domain</dt>
                        <dd class="col-sm-9">{{ $institution->student_email_domain ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Phone</dt>
                        <dd class="col-sm-9">{{ $institution->phone ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Address</dt>
                        <dd class="col-sm-9">{{ $institution->address ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">City</dt>
                        <dd class="col-sm-9">{{ $institution->city ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">State/Region</dt>
                        <dd class="col-sm-9">{{ $institution->state ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">ZIP Code</dt>
                        <dd class="col-sm-9">{{ $institution->zip ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Country</dt>
                        <dd class="col-sm-9">{{ $institution->country ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Website</dt>
                        <dd class="col-sm-9">
                            @if($institution->website)
                            <a href="{{ $institution->website }}" target="_blank" rel="noopener noreferrer">{{
                                $institution->website }}</a>
                            @else
                            N/A
                            @endif
                        </dd>

                        <dt class="col-sm-3">Logo</dt>
                        <dd class="col-sm-9">
                            @if($institution->logo)
                            <a href="{{ $institution->logo }}" target="_blank" rel="noopener noreferrer">View Logo</a>
                            @else
                            N/A
                            @endif
                        </dd>

                        <dt class="col-sm-3">Associated Users</dt>
                        <dd class="col-sm-9">{{ $institution->users_count ?? 0 }}</dd>

                        <dt class="col-sm-3">Education Records</dt>
                        <dd class="col-sm-9">{{ $institution->education_count ?? 0 }}</dd>

                        <dt class="col-sm-3">Created</dt>
                        <dd class="col-sm-9">{{ $institution->created_at->format('F d, Y h:i A') }}</dd>

                        <dt class="col-sm-3">Last Updated</dt>
                        <dd class="col-sm-9">{{ $institution->updated_at->format('F d, Y h:i A') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2 class="dashboard-card-title">Actions</h2>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <a href="{{ route('admin.institutions.edit', $institution->id) }}"
                        class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <form method="POST" action="{{ route('admin.institutions.destroy', $institution->id) }}"
                        onsubmit="return confirm('Are you sure you want to delete this institution?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection