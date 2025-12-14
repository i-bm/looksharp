@extends('layouts.admin.main')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-page-header">
        <div>
            <h1 class="dashboard-page-title">Analytics & Reports</h1>
            <p class="dashboard-page-subtitle">Platform statistics and insights</p>
        </div>
    </div>

    <!-- User Growth Chart -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <h2 class="dashboard-card-title">User Growth (Last 12 Months)</h2>
                <p class="dashboard-card-subtitle">Monthly new user registrations</p>
            </div>
        </div>
        <div class="dashboard-chart-container" style="height: 300px;">
            <canvas id="userGrowthChart"></canvas>
        </div>
    </div>

    <!-- Users by Type -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <h2 class="dashboard-card-title">Users by Type</h2>
                <p class="dashboard-card-subtitle">Distribution of user accounts</p>
            </div>
        </div>
        <div class="dashboard-table-container">
            @if(isset($analytics['users_by_type']) && count($analytics['users_by_type']) > 0)
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>User Type</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($analytics['users_by_type'] as $type => $count)
                    <tr>
                        <td><strong>{{ ucfirst(str_replace('_', ' ', $type)) }}</strong></td>
                        <td>{{ $count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="dashboard-empty-state">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                <p style="color: #666; margin: 0;">No user type data available.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Recent Activity -->
    @if(isset($analytics['recent_activity']['recent_users']) && count($analytics['recent_activity']['recent_users']) >
    0)
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <h2 class="dashboard-card-title">Recent User Activity</h2>
                <p class="dashboard-card-subtitle">Latest registered users</p>
            </div>
        </div>
        <div class="dashboard-table-container">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($analytics['recent_activity']['recent_users'] as $user)
                    <tr>
                        <td><strong>{{ $user['email'] }}</strong></td>
                        <td>{{ $user['name'] ?? 'N/A' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $user['user_type'] ?? 'N/A')) }}</td>
                        <td>{{ $user['created_at'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // User Growth Chart
    const ctx = document.getElementById('userGrowthChart');
    if (ctx) {
        const chartData = @json($analytics['user_growth'] ?? ['labels' => [], 'data' => []]);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    label: 'New Users',
                    data: chartData.data || [],
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
                        display: true,
                        position: 'top'
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