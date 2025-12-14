@extends('layouts.admin.main')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-page-header">
        <div>
            <a href="{{ route('admin.users.index') }}" class="dashboard-back-link">
                <i class="bi bi-arrow-left"></i> Back to Users
            </a>
            <h1 class="dashboard-page-title">User Details</h1>
            <p class="dashboard-page-subtitle">View and manage user account information</p>
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

    <div class="row">
        <!-- User Information -->
        <div class="col-md-8">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2 class="dashboard-card-title">User Information</h2>
                    </div>
                    <div>
                        <span
                            class="dashboard-status-badge dashboard-status-{{ $targetUser->is_active ? 'success' : 'danger' }}">
                            {{ $targetUser->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9">{{ $targetUser->email }}</dd>

                        <dt class="col-sm-3">Full Name</dt>
                        <dd class="col-sm-9">{{ $targetUser->full_name ?? 'N/A' }}</dd>

                        @php
                        $profile = $targetUser->adminProfile ?? $targetUser->talentProfile ?? null;
                        @endphp

                        @if($targetUser->hasRole('admin') && $targetUser->adminProfile)
                        <!-- Admin Profile Info -->
                        <dt class="col-sm-3">First Name</dt>
                        <dd class="col-sm-9">{{ $targetUser->adminProfile->first_name ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Last Name</dt>
                        <dd class="col-sm-9">{{ $targetUser->adminProfile->last_name ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Phone Number</dt>
                        <dd class="col-sm-9">{{ $targetUser->adminProfile->phone_number ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Department</dt>
                        <dd class="col-sm-9">{{ $targetUser->adminProfile->department ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Job Title</dt>
                        <dd class="col-sm-9">{{ $targetUser->adminProfile->job_title ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Office Location</dt>
                        <dd class="col-sm-9">{{ $targetUser->adminProfile->office_location ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Extension</dt>
                        <dd class="col-sm-9">{{ $targetUser->adminProfile->extension ?? 'N/A' }}</dd>

                        @if($targetUser->adminProfile->bio)
                        <dt class="col-sm-3">Bio</dt>
                        <dd class="col-sm-9">{{ $targetUser->adminProfile->bio }}</dd>
                        @endif
                        @else
                        <!-- Fallback to user table fields for non-admin or admin without profile -->
                        <dt class="col-sm-3">First Name</dt>
                        <dd class="col-sm-9">{{ $targetUser->first_name ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Last Name</dt>
                        <dd class="col-sm-9">{{ $targetUser->last_name ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Phone Number</dt>
                        <dd class="col-sm-9">{{ $targetUser->phone_number ?? 'N/A' }}</dd>
                        @endif

                        <dt class="col-sm-3">User Type</dt>
                        <dd class="col-sm-9">{{ ucfirst(str_replace('_', ' ', $targetUser->user_type ?? 'N/A')) }}</dd>

                        <dt class="col-sm-3">Registered</dt>
                        <dd class="col-sm-9">{{ $targetUser->created_at->format('F d, Y h:i A') }}</dd>

                        <dt class="col-sm-3">Last Updated</dt>
                        <dd class="col-sm-9">{{ $targetUser->updated_at->format('F d, Y h:i A') }}</dd>

                        @if($targetUser->email_verified_at)
                        <dt class="col-sm-3">Email Verified</dt>
                        <dd class="col-sm-9">{{ $targetUser->email_verified_at->format('F d, Y h:i A') }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Roles -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2 class="dashboard-card-title">Roles</h2>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    @if($targetUser->roles->count() > 0)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($targetUser->roles as $role)
                        <span class="badge bg-primary">{{ $role->name }}</span>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted mb-0">No roles assigned</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2 class="dashboard-card-title">Actions</h2>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    @if($targetUser->is_active)
                    <form method="POST" action="{{ route('admin.users.deactivate', $targetUser->id) }}"
                        onsubmit="return confirm('Are you sure you want to deactivate this user?');">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-danger w-100 mb-2">
                            <i class="bi bi-x-circle"></i> Deactivate User
                        </button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('admin.users.activate', $targetUser->id) }}">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success w-100 mb-2">
                            <i class="bi bi-check-circle"></i> Activate User
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection