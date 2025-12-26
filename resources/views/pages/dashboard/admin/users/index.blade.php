@extends('layouts.admin.main')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-page-header">
        <div>
            <h1 class="dashboard-page-title">User Management</h1>
            <p class="dashboard-page-subtitle">Manage user accounts and permissions</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="dashboard-card">
        <form method="GET" action="{{ route('admin.users.index') }}" class="dashboard-filter-form">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}"
                        placeholder="Email or name">
                </div>
                <div class="col-md-2">
                    <label class="form-label">User Type</label>
                    <select name="user_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="talent" {{ ($filters['user_type'] ?? '' )=='talent' ? 'selected' : '' }}>Talent
                        </option>
                        <option value="employer" {{ ($filters['user_type'] ?? '' )=='employer' ? 'selected' : '' }}>
                            Employer</option>
                        <option value="university_admin" {{ ($filters['user_type'] ?? '' )=='university_admin'
                            ? 'selected' : '' }}>University</option>
                        <option value="admin" {{ ($filters['user_type'] ?? '' )=='admin' ? 'selected' : '' }}>Admin
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="">All Status</option>
                        <option value="1" {{ ($filters['is_active'] ?? '' )==='1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ ($filters['is_active'] ?? '' )==='0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary w-100">Reset</a>
                </div>
            </div>
        </form>
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

    <!-- Users Table -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <h2 class="dashboard-card-title">Users ({{ $users->total() }})</h2>
                <p class="dashboard-card-subtitle">All registered user accounts</p>
            </div>
        </div>
        <div class="dashboard-table-container">
            @if($users->count() > 0)
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="dashboard-table-cell">
                                <strong>{{ $user->email }}</strong>
                            </div>
                        </td>
                        <td>{{ $user->full_name ?? 'N/A' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $user->user_type ?? 'N/A')) }}</td>
                        <td>
                            <span
                                class="dashboard-status-badge dashboard-status-{{ $user->is_active ? 'success' : 'danger' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="dashboard-table-actions">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="dashboard-table-link"
                                    title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($user->is_active)
                                <form method="POST" action="{{ route('admin.users.deactivate', $user->id) }}"
                                    style="display: inline;"
                                    onsubmit="return confirm('Are you sure you want to deactivate this user?');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="dashboard-table-link text-danger" title="Deactivate">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('admin.users.activate', $user->id) }}"
                                    style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="dashboard-table-link text-success" title="Activate">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="dashboard-pagination">
                {{ $users->links() }}
            </div>
            @else
            <div class="dashboard-empty-state">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                <p style="color: #666; margin: 0;">No users found.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection