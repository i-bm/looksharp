@extends('layouts.admin.main')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-page-header">
        <div>
            <a href="{{ route('admin.career-interest-areas.index') }}" class="dashboard-back-link">
                <i class="bi bi-arrow-left"></i> Back to Career Interest Areas
            </a>
            <h1 class="dashboard-page-title">Career Interest Area Details</h1>
            <p class="dashboard-page-subtitle">View career interest category information</p>
        </div>
        <div>
            <a href="{{ route('admin.career-interest-areas.edit', $careerInterestArea->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2 class="dashboard-card-title">Career Interest Area Information</h2>
                    </div>
                    <div>
                        <span
                            class="dashboard-status-badge dashboard-status-{{ $careerInterestArea->is_active ? 'success' : 'danger' }}">
                            {{ $careerInterestArea->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Name</dt>
                        <dd class="col-sm-9"><strong>{{ $careerInterestArea->name }}</strong></dd>

                        <dt class="col-sm-3">Slug</dt>
                        <dd class="col-sm-9"><code>{{ $careerInterestArea->slug }}</code></dd>

                        <dt class="col-sm-3">Parent Category</dt>
                        <dd class="col-sm-9">{{ $careerInterestArea->parent ? $careerInterestArea->parent->name : 'None
                            (Parent Category)' }}</dd>

                        <dt class="col-sm-3">Order</dt>
                        <dd class="col-sm-9">{{ $careerInterestArea->order }}</dd>

                        <dt class="col-sm-3">Created</dt>
                        <dd class="col-sm-9">{{ $careerInterestArea->created_at->format('F d, Y h:i A') }}</dd>

                        <dt class="col-sm-3">Last Updated</dt>
                        <dd class="col-sm-9">{{ $careerInterestArea->updated_at->format('F d, Y h:i A') }}</dd>
                    </dl>
                </div>
            </div>

            @if($careerInterestArea->children->count() > 0)
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2 class="dashboard-card-title">Subcategories ({{ $careerInterestArea->children->count() }})
                        </h2>
                    </div>
                </div>
                <div class="dashboard-table-container">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($careerInterestArea->children as $child)
                            <tr>
                                <td><strong>{{ $child->name }}</strong></td>
                                <td><code>{{ $child->slug }}</code></td>
                                <td>{{ $child->order }}</td>
                                <td>
                                    <span
                                        class="dashboard-status-badge dashboard-status-{{ $child->is_active ? 'success' : 'danger' }}">
                                        {{ $child->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.career-interest-areas.show', $child->id) }}"
                                        class="dashboard-table-link" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2 class="dashboard-card-title">Actions</h2>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <a href="{{ route('admin.career-interest-areas.edit', $careerInterestArea->id) }}"
                        class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <form method="POST"
                        action="{{ route('admin.career-interest-areas.destroy', $careerInterestArea->id) }}"
                        onsubmit="return confirm('Are you sure you want to delete this career interest area?');">
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