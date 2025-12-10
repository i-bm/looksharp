@extends('layouts.dashboard.main')

@section('content')
<div class="dashboard-container">
    <!-- Welcome Header -->
    {{-- <div class="dashboard-header">
        <button class="dashboard-mobile-menu-btn" id="mobileMenuBtn">
            <i class="bi bi-list"></i>
        </button>
        <div class="dashboard-header-content">
            <h1 class="dashboard-welcome">Welcome Back, {{ Auth::user()->talentProfile ?
                (Auth::user()->talentProfile->first_name . ' ' . Auth::user()->talentProfile->last_name) :
                Auth::user()->full_name }}</h1>
            <p class="dashboard-subtitle">Here's what's happening with your profile today</p>
        </div>
        <div class="dashboard-header-actions">
            <div class="dashboard-search">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Search Here" class="dashboard-search-input">
            </div>
            <div class="dashboard-icons">
                <button class="dashboard-icon-btn" title="Messages">
                    <i class="bi bi-envelope"></i>
                </button>
                <button class="dashboard-icon-btn" title="Notifications">
                    <i class="bi bi-bell"></i>
                </button>
                <div class="dashboard-profile">
                    <div class="dashboard-avatar-initials">
                        @if(Auth::user()->talentProfile)
                        {{ strtoupper(substr(Auth::user()->talentProfile->first_name, 0, 1) .
                        substr(Auth::user()->talentProfile->last_name, 0, 1)) }}
                        @else
                        {{ strtoupper(substr(Auth::user()->first_name, 0, 1) . substr(Auth::user()->last_name, 0, 1)) }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Stats Cards -->
    <div class="dashboard-stats">
        <div class="dashboard-stat-card">
            <div class="dashboard-stat-header">
                <h3 class="dashboard-stat-title">Profile Completion</h3>
                <i class="bi bi-person-check dashboard-stat-icon"></i>
            </div>
            <div class="dashboard-stat-value">{{ $profileCompletion ?? 0 }}%</div>
            <div class="dashboard-stat-footer">
                <span class="dashboard-stat-label">Compared To Last Week</span>
                <span class="dashboard-stat-badge dashboard-stat-badge-success">+{{ $profileCompletionChange ?? 0
                    }}%</span>
            </div>
        </div>

        <div class="dashboard-stat-card">
            <div class="dashboard-stat-header">
                <h3 class="dashboard-stat-title">Active Applications</h3>
                <i class="bi bi-briefcase dashboard-stat-icon"></i>
            </div>
            <div class="dashboard-stat-value">{{ $activeApplications ?? 0 }}</div>
            <div class="dashboard-stat-footer">
                <span class="dashboard-stat-label">Applications This Month</span>
                <span class="dashboard-stat-badge dashboard-stat-badge-success">+{{ $newApplications ?? 0 }} New</span>
            </div>
        </div>

        <div class="dashboard-stat-card">
            <div class="dashboard-stat-header">
                <h3 class="dashboard-stat-title">Saved Opportunities</h3>
                <i class="bi bi-bookmark dashboard-stat-icon"></i>
            </div>
            <div class="dashboard-stat-value">{{ $savedOpportunities ?? 0 }}</div>
            <div class="dashboard-stat-footer">
                <span class="dashboard-stat-label">Total Saved Jobs</span>
                <span class="dashboard-stat-badge dashboard-stat-badge-info">View All</span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="dashboard-content">
        <!-- Left Column -->
        <div class="dashboard-content-left">
            <!-- Application Status Chart -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2 class="dashboard-card-title">Application Status</h2>
                        <p class="dashboard-card-subtitle">Overview of your job applications progress</p>
                    </div>
                    <select class="dashboard-select">
                        <option>All Time</option>
                        <option>This Month</option>
                        <option>This Year</option>
                    </select>
                </div>
                <div class="dashboard-chart-container">
                    <canvas id="applicationChart" class="dashboard-chart"></canvas>
                </div>
            </div>

            <!-- Recent Applications Table -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2 class="dashboard-card-title">Recent Applications</h2>
                        <p class="dashboard-card-subtitle">Your latest job application submissions</p>
                    </div>
                    <a href="#" class="dashboard-btn-link">View All Applications</a>
                </div>
                <div class="dashboard-table-container">
                    @if(count($recentApplications ?? []) > 0)
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Position</th>
                                <th>Company</th>
                                <th>Date Applied</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentApplications as $application)
                            <tr>
                                <td>
                                    <div class="dashboard-table-cell">
                                        <strong>{{ $application['position'] ?? 'N/A' }}</strong>
                                    </div>
                                </td>
                                <td>{{ $application['company'] ?? 'N/A' }}</td>
                                <td>{{ $application['date_applied'] ?? 'N/A' }}</td>
                                <td>
                                    <span
                                        class="dashboard-status-badge dashboard-status-{{ $application['status_class'] ?? 'review' }}">
                                        {{ $application['status'] ?? 'Under Review' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ $application['url'] ?? '#' }}" class="dashboard-table-link">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="dashboard-empty-state">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                        <p style="color: #666; margin: 0;">No applications yet. Start applying to opportunities to see
                            them here.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="dashboard-content-right">
            <!-- Recommended Opportunities -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2 class="dashboard-card-title">Recommended Opportunities</h2>
                        <p class="dashboard-card-subtitle">Jobs matched to your profile</p>
                    </div>
                    <select class="dashboard-select">
                        <option>Today</option>
                        <option>This Week</option>
                        <option>This Month</option>
                    </select>
                </div>
                <div class="dashboard-opportunities">
                    @if(count($recommendedOpportunities ?? []) > 0)
                    @foreach($recommendedOpportunities as $opportunity)
                    <div class="dashboard-opportunity-item">
                        <div class="dashboard-opportunity-header">
                            <h4 class="dashboard-opportunity-title">{{ $opportunity['title'] ?? 'N/A' }}</h4>
                            <a href="{{ $opportunity['url'] ?? '#' }}" class="dashboard-opportunity-link">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </div>
                        <div class="dashboard-opportunity-details">
                            <div class="dashboard-opportunity-detail">
                                <i class="bi bi-building"></i>
                                <span>{{ $opportunity['company'] ?? 'N/A' }}</span>
                            </div>
                            <div class="dashboard-opportunity-detail">
                                <i class="bi bi-geo-alt"></i>
                                <span>{{ $opportunity['location'] ?? 'N/A' }}</span>
                            </div>
                            <div class="dashboard-opportunity-detail">
                                <i class="bi bi-clock"></i>
                                <span>{{ $opportunity['type'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="dashboard-opportunity-footer">
                            @if(isset($opportunity['match_percentage']))
                            <span class="dashboard-opportunity-match">{{ $opportunity['match_percentage'] }}%
                                Match</span>
                            @endif
                            <button class="dashboard-btn-primary">Apply Now</button>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="dashboard-empty-state" style="text-align: center; padding: 2rem;">
                        <i class="bi bi-briefcase" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                        <p style="color: #666; margin: 0;">No recommended opportunities at the moment. Complete your
                            profile to get personalized job recommendations.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <h2 class="dashboard-card-title">Quick Actions</h2>
                        <p class="dashboard-card-subtitle">Common tasks and shortcuts</p>
                    </div>
                </div>
                <div class="dashboard-actions">
                    <a href="{{ route('talent.profile.build') }}" class="dashboard-action-item">
                        <i class="bi bi-person-gear"></i>
                        <span>Complete Profile</span>
                    </a>
                    <a href="#" class="dashboard-action-item">
                        <i class="bi bi-search"></i>
                        <span>Browse Jobs</span>
                    </a>
                    <a href="#" class="dashboard-action-item">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Update Resume</span>
                    </a>
                    <a href="#" class="dashboard-action-item">
                        <i class="bi bi-bell"></i>
                        <span>Job Alerts</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Application Status Chart
    const ctx = document.getElementById('applicationChart');
    if (ctx) {
        const chartData = @json($applicationChartData ?? ['labels' => [], 'data' => []]);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Applications',
                    data: chartData.data || [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    borderColor: '#F53003',
                    backgroundColor: 'rgba(245, 48, 3, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
</script>
@endpush
@endsection
