@extends('layouts.admin.main')

@section('content')
<div class="dashboard-container">
    <!-- Stats Cards -->
    <div class="dashboard-stats">
        <div class="dashboard-stat-card">
            <div class="dashboard-stat-header">
                <h3 class="dashboard-stat-title">Total Users</h3>
                <i class="bi bi-people dashboard-stat-icon"></i>
            </div>
            <div class="dashboard-stat-value">{{ $stats['total_users'] ?? 0 }}</div>
            <div class="dashboard-stat-footer">
                <span class="dashboard-stat-label">All Registered Users</span>
            </div>
        </div>

        <div class="dashboard-stat-card">
            <div class="dashboard-stat-header">
                <h3 class="dashboard-stat-title">Active Users</h3>
                <i class="bi bi-person-check dashboard-stat-icon"></i>
            </div>
            <div class="dashboard-stat-value">{{ $stats['active_users'] ?? 0 }}</div>
            <div class="dashboard-stat-footer">
                <span class="dashboard-stat-label">Currently Active Accounts</span>
            </div>
        </div>

        <div class="dashboard-stat-card">
            <div class="dashboard-stat-header">
                <h3 class="dashboard-stat-title">New Users Today</h3>
                <i class="bi bi-person-plus dashboard-stat-icon"></i>
            </div>
            <div class="dashboard-stat-value">{{ $stats['users_created_today'] ?? 0 }}</div>
            <div class="dashboard-stat-footer">
                <span class="dashboard-stat-label">Registered Today</span>
            </div>
        </div>

        <div class="dashboard-stat-card">
            <div class="dashboard-stat-header">
                <h3 class="dashboard-stat-title">This Month</h3>
                <i class="bi bi-calendar-month dashboard-stat-icon"></i>
            </div>
            <div class="dashboard-stat-value">{{ $stats['users_created_this_month'] ?? 0 }}</div>
            <div class="dashboard-stat-footer">
                <span class="dashboard-stat-label">New Users This Month</span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="dashboard-content">
        <!-- Left Column -->
        <div class="dashboard-content-left">
            <!-- Users by Type -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2 class="dashboard-card-title">Users by Type</h2>
                        <p class="dashboard-card-subtitle">Distribution of user accounts by role</p>
                    </div>
                </div>
                <div class="dashboard-table-container">
                    @if(isset($stats['users_by_type']) && count($stats['users_by_type']) > 0)
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>User Type</th>
                                <th>Count</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $total = $stats['total_users'] ?? 1;
                            @endphp
                            @foreach($stats['users_by_type'] as $type => $count)
                            <tr>
                                <td><strong>{{ ucfirst(str_replace('_', ' ', $type)) }}</strong></td>
                                <td>{{ $count }}</td>
                                <td>{{ round(($count / $total) * 100, 1) }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="dashboard-empty-state">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                        <p style="color: #666; margin: 0;">No user data available.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recent Users -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2 class="dashboard-card-title">Recent Users</h2>
                        <p class="dashboard-card-subtitle">Latest registered users</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="dashboard-btn-link">View All Users</a>
                </div>
                <div class="dashboard-table-container">
                    @if(isset($stats['recent_users']) && $stats['recent_users']->count() > 0)
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['recent_users'] as $user)
                            <tr>
                                <td>
                                    <div class="dashboard-table-cell">
                                        <strong>{{ $user->email }}</strong>
                                    </div>
                                </td>
                                <td>{{ ucfirst($user->user_type ?? 'N/A') }}</td>
                                <td>
                                    <span
                                        class="dashboard-status-badge dashboard-status-{{ $user->is_active ? 'success' : 'danger' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="dashboard-table-link">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="dashboard-empty-state">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                        <p style="color: #666; margin: 0;">No recent users.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="dashboard-content-right">
            <!-- Quick Actions -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2 class="dashboard-card-title">Quick Actions</h2>
                        <p class="dashboard-card-subtitle">Common administrative tasks</p>
                    </div>
                </div>
                <div class="dashboard-actions">
                    <a href="{{ route('admin.users.index') }}" class="dashboard-action-item">
                        <i class="bi bi-people"></i>
                        <span>Manage Users</span>
                    </a>
                    <a href="{{ route('admin.content.index') }}" class="dashboard-action-item">
                        <i class="bi bi-shield-check"></i>
                        <span>Content Moderation</span>
                    </a>
                    <a href="{{ route('admin.analytics.index') }}" class="dashboard-action-item">
                        <i class="bi bi-graph-up"></i>
                        <span>View Analytics</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="dashboard-action-item">
                        <i class="bi bi-gear"></i>
                        <span>System Settings</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection