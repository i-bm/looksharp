@extends('layouts.admin.main')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-page-header">
        <div>
            <a href="{{ route('admin.career-interest-areas.index') }}" class="dashboard-back-link">
                <i class="bi bi-arrow-left"></i> Back to Career Interest Areas
            </a>
            <h1 class="dashboard-page-title">Edit Career Interest Area</h1>
            <p class="dashboard-page-subtitle">Update career interest category details</p>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <h2 class="dashboard-card-title">Career Interest Area Details</h2>
            </div>
        </div>
        <div class="dashboard-card-body">
            <form method="POST" action="{{ route('admin.career-interest-areas.update', $careerInterestArea->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                        value="{{ old('name', $careerInterestArea->name) }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug"
                        value="{{ old('slug', $careerInterestArea->slug) }}" placeholder="Auto-generated if left empty">
                    @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Leave empty to auto-generate from name</small>
                </div>

                <div class="mb-3">
                    <label for="parent_id" class="form-label">Parent Category</label>
                    <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id"
                        name="parent_id">
                        <option value="">None (Parent Category)</option>
                        @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}" {{ (old('parent_id', $careerInterestArea->parent_id) ==
                            $parent->id) ? 'selected' : '' }}>{{ $parent->name }}</option>
                        @endforeach
                    </select>
                    @error('parent_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="order" class="form-label">Order</label>
                    <input type="number" class="form-control @error('order') is-invalid @enderror" id="order"
                        name="order" value="{{ old('order', $careerInterestArea->order) }}" min="0">
                    @error('order')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{
                            old('is_active', $careerInterestArea->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Update Career Interest Area</button>
                    <a href="{{ route('admin.career-interest-areas.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection