@extends('layouts.admin.main')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-page-header">
        <div>
            <h1 class="dashboard-page-title">Content Moderation</h1>
            <p class="dashboard-page-subtitle">Review and moderate platform content</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="dashboard-stats">
        <div class="dashboard-stat-card">
            <div class="dashboard-stat-header">
                <h3 class="dashboard-stat-title">Pending Reviews</h3>
                <i class="bi bi-shield-exclamation dashboard-stat-icon"></i>
            </div>
            <div class="dashboard-stat-value">{{ $contentStats['pending_reviews'] ?? 0 }}</div>
            <div class="dashboard-stat-footer">
                <span class="dashboard-stat-label">Awaiting Review</span>
            </div>
        </div>

        <div class="dashboard-stat-card">
            <div class="dashboard-stat-header">
                <h3 class="dashboard-stat-title">Flagged Jobs</h3>
                <i class="bi bi-flag dashboard-stat-icon"></i>
            </div>
            <div class="dashboard-stat-value">{{ $contentStats['flagged_jobs'] ?? 0 }}</div>
            <div class="dashboard-stat-footer">
                <span class="dashboard-stat-label">Reported Job Postings</span>
            </div>
        </div>

        <div class="dashboard-stat-card">
            <div class="dashboard-stat-header">
                <h3 class="dashboard-stat-title">Reported Profiles</h3>
                <i class="bi bi-person-x dashboard-stat-icon"></i>
            </div>
            <div class="dashboard-stat-value">{{ $contentStats['reported_profiles'] ?? 0 }}</div>
            <div class="dashboard-stat-footer">
                <span class="dashboard-stat-label">Profile Reports</span>
            </div>
        </div>

        <div class="dashboard-stat-card">
            <div class="dashboard-stat-header">
                <h3 class="dashboard-stat-title">Pending Approvals</h3>
                <i class="bi bi-clock-history dashboard-stat-icon"></i>
            </div>
            <div class="dashboard-stat-value">{{ $contentStats['pending_approvals'] ?? 0 }}</div>
            <div class="dashboard-stat-footer">
                <span class="dashboard-stat-label">Awaiting Approval</span>
            </div>
        </div>
    </div>

    <!-- Placeholder Content -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <h2 class="dashboard-card-title">Content Moderation Dashboard</h2>
                <p class="dashboard-card-subtitle">Content moderation features will be available here</p>
            </div>
        </div>
        <div class="dashboard-empty-state" style="text-align: center; padding: 3rem;">
            <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc; margin-bottom: 1rem;"></i>
            <p style="color: #666; margin: 0;">Content moderation features are coming soon. This section will include
                tools for reviewing jobs, profiles, and user-generated content.</p>
        </div>
    </div>
</div>
@endsection