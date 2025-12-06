@extends('layouts.dashboard.main')

@section('content')
<div class="dashboard-container">
    <!-- Welcome Header -->
    <div class="dashboard-header">
        <button class="dashboard-mobile-menu-btn" id="mobileMenuBtn">
            <i class="bi bi-list"></i>
        </button>
        <div class="dashboard-header-content">
            <h1 class="dashboard-welcome">Welcome Back, {{ Auth::user()->talentProfile ? (Auth::user()->talentProfile->first_name . ' ' . Auth::user()->talentProfile->last_name) : Auth::user()->full_name }}</h1>
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
                            {{ strtoupper(substr(Auth::user()->talentProfile->first_name, 0, 1) . substr(Auth::user()->talentProfile->last_name, 0, 1)) }}
                        @else
                            {{ strtoupper(substr(Auth::user()->first_name, 0, 1) . substr(Auth::user()->last_name, 0, 1)) }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                            <tr>
                                <td>
                                    <div class="dashboard-table-cell">
                                        <strong>Software Developer Intern</strong>
                                    </div>
                                </td>
                                <td>MTN Ghana</td>
                                <td>15 Dec 2024</td>
                                <td><span class="dashboard-status-badge dashboard-status-review">Under Review</span>
                                </td>
                                <td><a href="#" class="dashboard-table-link"><i
                                            class="bi bi-box-arrow-up-right"></i></a></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="dashboard-table-cell">
                                        <strong>Marketing Assistant</strong>
                                    </div>
                                </td>
                                <td>Vodafone Ghana</td>
                                <td>12 Dec 2024</td>
                                <td><span class="dashboard-status-badge dashboard-status-interview">Interview
                                        Scheduled</span></td>
                                <td><a href="#" class="dashboard-table-link"><i
                                            class="bi bi-box-arrow-up-right"></i></a></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="dashboard-table-cell">
                                        <strong>Data Analyst</strong>
                                    </div>
                                </td>
                                <td>Ecobank Ghana</td>
                                <td>10 Dec 2024</td>
                                <td><span class="dashboard-status-badge dashboard-status-completed">Accepted</span></td>
                                <td><a href="#" class="dashboard-table-link"><i
                                            class="bi bi-box-arrow-up-right"></i></a></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="dashboard-table-cell">
                                        <strong>UI/UX Designer</strong>
                                    </div>
                                </td>
                                <td>Ashesi University</td>
                                <td>08 Dec 2024</td>
                                <td><span class="dashboard-status-badge dashboard-status-completed">Completed</span>
                                </td>
                                <td><a href="#" class="dashboard-table-link"><i
                                            class="bi bi-box-arrow-up-right"></i></a></td>
                            </tr>
                        </tbody>
                    </table>
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
                    <div class="dashboard-opportunity-item">
                        <div class="dashboard-opportunity-header">
                            <h4 class="dashboard-opportunity-title">Frontend Developer</h4>
                            <a href="#" class="dashboard-opportunity-link"><i class="bi bi-box-arrow-up-right"></i></a>
                        </div>
                        <div class="dashboard-opportunity-details">
                            <div class="dashboard-opportunity-detail">
                                <i class="bi bi-building"></i>
                                <span>Google Ghana</span>
                            </div>
                            <div class="dashboard-opportunity-detail">
                                <i class="bi bi-geo-alt"></i>
                                <span>Accra, Ghana</span>
                            </div>
                            <div class="dashboard-opportunity-detail">
                                <i class="bi bi-clock"></i>
                                <span>Full-time</span>
                            </div>
                        </div>
                        <div class="dashboard-opportunity-footer">
                            <span class="dashboard-opportunity-match">95% Match</span>
                            <button class="dashboard-btn-primary">Apply Now</button>
                        </div>
                    </div>

                    <div class="dashboard-opportunity-item">
                        <div class="dashboard-opportunity-header">
                            <h4 class="dashboard-opportunity-title">Backend Engineer</h4>
                            <a href="#" class="dashboard-opportunity-link"><i class="bi bi-box-arrow-up-right"></i></a>
                        </div>
                        <div class="dashboard-opportunity-details">
                            <div class="dashboard-opportunity-detail">
                                <i class="bi bi-building"></i>
                                <span>Microsoft Ghana</span>
                            </div>
                            <div class="dashboard-opportunity-detail">
                                <i class="bi bi-geo-alt"></i>
                                <span>Remote</span>
                            </div>
                            <div class="dashboard-opportunity-detail">
                                <i class="bi bi-clock"></i>
                                <span>Part-time</span>
                            </div>
                        </div>
                        <div class="dashboard-opportunity-footer">
                            <span class="dashboard-opportunity-match">88% Match</span>
                            <button class="dashboard-btn-primary">Apply Now</button>
                        </div>
                    </div>

                    <div class="dashboard-opportunity-item">
                        <div class="dashboard-opportunity-header">
                            <h4 class="dashboard-opportunity-title">Product Manager</h4>
                            <a href="#" class="dashboard-opportunity-link"><i class="bi bi-box-arrow-up-right"></i></a>
                        </div>
                        <div class="dashboard-opportunity-details">
                            <div class="dashboard-opportunity-detail">
                                <i class="bi bi-building"></i>
                                <span>Shopify</span>
                            </div>
                            <div class="dashboard-opportunity-detail">
                                <i class="bi bi-geo-alt"></i>
                                <span>Kumasi, Ghana</span>
                            </div>
                            <div class="dashboard-opportunity-detail">
                                <i class="bi bi-clock"></i>
                                <span>Contract</span>
                            </div>
                        </div>
                        <div class="dashboard-opportunity-footer">
                            <span class="dashboard-opportunity-match">82% Match</span>
                            <button class="dashboard-btn-primary">Apply Now</button>
                        </div>
                    </div>
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
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Applications',
                    data: [2, 3, 5, 4, 6, 8, 7, 9, 10, 8, 12, 15],
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
