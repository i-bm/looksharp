@extends('layouts.admin.main')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-page-header">
        <div>
            <h1 class="dashboard-page-title">Institutions</h1>
            <p class="dashboard-page-subtitle">Manage educational institutions</p>
        </div>
        <div>
            <a href="{{ route('admin.institutions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create Institution
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

    <!-- Sync from GTEC -->
    <div class="alert alert-info">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong>Sync from GTEC API:</strong> Update institutions from the GTEC (Ghana Tertiary Education
                Commission) database.
            </div>
            <form method="POST" action="{{ route('admin.institutions.sync-gtec') }}" style="display: inline;"
                onsubmit="return confirm('This will update existing institutions and create new ones. Continue?');">
                @csrf
                <button type="submit" class="btn btn-info btn-sm">
                    <i class="bi bi-arrow-repeat"></i> Sync from GTEC
                </button>
            </form>
        </div>
    </div>

    <!-- Filters -->
    <div class="dashboard-card">
        <form method="GET" action="{{ route('admin.institutions.index') }}" class="dashboard-filter-form">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                        placeholder="Name, email, city, or state">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="">All Status</option>
                        <option value="1" {{ request('is_active')==='1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active')==='0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Country</label>
                    <input type="text" name="country" class="form-control" value="{{ request('country') }}"
                        placeholder="Ghana">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('admin.institutions.index') }}" class="btn btn-secondary w-100">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Institutions Table -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <h2 class="dashboard-card-title">Institutions ({{ $institutions->total() }})</h2>
                <p class="dashboard-card-subtitle">All registered educational institutions</p>
            </div>
        </div>
        <div class="dashboard-table-container">
            @if($institutions->count() > 0)
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Country</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($institutions as $institution)
                    <tr>
                        <td>
                            <div class="dashboard-table-cell">
                                <strong>{{ $institution->name }}</strong>
                            </div>
                        </td>
                        <td>{{ $institution->email ?? '-' }}</td>
                        <td>{{ $institution->city ?? '-' }}</td>
                        <td>{{ $institution->state ?? '-' }}</td>
                        <td>{{ $institution->country ?? '-' }}</td>
                        <td>
                            <span
                                class="dashboard-status-badge dashboard-status-{{ $institution->is_active ? 'success' : 'danger' }}">
                                {{ $institution->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="dashboard-table-actions">
                                <a href="{{ route('admin.institutions.show', $institution->id) }}"
                                    class="dashboard-table-link" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.institutions.edit', $institution->id) }}"
                                    class="dashboard-table-link" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.institutions.destroy', $institution->id) }}"
                                    style="display: inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this institution?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dashboard-table-link text-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="dashboard-pagination">
                {{ $institutions->links() }}
            </div>
            @else
            <div class="dashboard-empty-state">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                <p style="color: #666; margin: 0;">No institutions found.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection