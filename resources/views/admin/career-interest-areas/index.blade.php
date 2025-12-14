@extends('layouts.admin.main')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-page-header">
        <div>
            <h1 class="dashboard-page-title">Career Interest Areas</h1>
            <p class="dashboard-page-subtitle">Manage career interest categories and subcategories</p>
        </div>
        <div>
            <a href="{{ route('admin.career-interest-areas.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create Category
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

    <!-- Filters -->
    <div class="dashboard-card">
        <form method="GET" action="{{ route('admin.career-interest-areas.index') }}" class="dashboard-filter-form">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                        placeholder="Name or slug">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All</option>
                        <option value="parents" {{ request('type')=='parents' ? 'selected' : '' }}>Parent Categories
                        </option>
                        <option value="children" {{ request('type')=='children' ? 'selected' : '' }}>Subcategories
                        </option>
                    </select>
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
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('admin.career-interest-areas.index') }}" class="btn btn-secondary w-100">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Career Interest Areas Table -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <h2 class="dashboard-card-title">Career Interest Areas ({{ $careerInterestAreas->total() }})</h2>
                <p class="dashboard-card-subtitle">All career interest categories and subcategories</p>
            </div>
        </div>
        <div class="dashboard-table-container">
            @if($careerInterestAreas->count() > 0)
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Parent Category</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($careerInterestAreas as $area)
                    <tr>
                        <td>
                            <div class="dashboard-table-cell">
                                <strong>{{ $area->name }}</strong>
                            </div>
                        </td>
                        <td><code>{{ $area->slug }}</code></td>
                        <td>{{ $area->parent ? $area->parent->name : '-' }}</td>
                        <td>{{ $area->order }}</td>
                        <td>
                            <span
                                class="dashboard-status-badge dashboard-status-{{ $area->is_active ? 'success' : 'danger' }}">
                                {{ $area->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="dashboard-table-actions">
                                <a href="{{ route('admin.career-interest-areas.show', $area->id) }}"
                                    class="dashboard-table-link" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.career-interest-areas.edit', $area->id) }}"
                                    class="dashboard-table-link" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST"
                                    action="{{ route('admin.career-interest-areas.destroy', $area->id) }}"
                                    style="display: inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this career interest area?');">
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
                {{ $careerInterestAreas->links() }}
            </div>
            @else
            <div class="dashboard-empty-state">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                <p style="color: #666; margin: 0;">No career interest areas found.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection