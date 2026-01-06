@if(Auth::check() && Auth::user()->hasRole('admin'))
<!-- Sidebar Backdrop (Mobile) -->
<div class="dashboard-sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- Sidebar Navigation -->
<aside class="dashboard-sidebar">
    <div class="dashboard-sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="dashboard-logo">
            <img src="{{ asset('assets/img/logo-red.png') }}" alt="{{ config('app.name') }}" class="dashboard-logo-img">
            <span class="dashboard-logo-text" style="margin-left: 10px; color: #666;">Admin</span>
        </a>
        <button class="dashboard-sidebar-toggle" id="sidebarToggle" title="Collapse sidebar">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>

    <nav class="dashboard-nav">
        <ul class="dashboard-nav-list">
            <li
                class="dashboard-nav-item {{ request()->routeIs('admin.dashboard') ? 'dashboard-nav-item-active' : '' }}">
                <a href="{{ route('admin.dashboard') }}" class="dashboard-nav-link">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="dashboard-nav-item {{ request()->routeIs('admin.users.*') ? 'dashboard-nav-item-active' : '' }}">
                <a href="{{ route('admin.users.index') }}" class="dashboard-nav-link">
                    <i class="bi bi-people"></i>
                    <span>User Management</span>
                </a>
            </li>

            <li
                class="dashboard-nav-item {{ request()->routeIs('admin.content.*') ? 'dashboard-nav-item-active' : '' }}">
                <a href="{{ route('admin.content.index') }}" class="dashboard-nav-link">
                    <i class="bi bi-shield-check"></i>
                    <span>Content Moderation</span>
                </a>
            </li>

            <li
                class="dashboard-nav-item {{ request()->routeIs('admin.analytics.*') ? 'dashboard-nav-item-active' : '' }}">
                <a href="{{ route('admin.analytics.index') }}" class="dashboard-nav-link">
                    <i class="bi bi-graph-up"></i>
                    <span>Analytics</span>
                </a>
            </li>

            <li
                class="dashboard-nav-item {{ request()->routeIs('admin.career-interest-areas.*') ? 'dashboard-nav-item-active' : '' }}">
                <a href="{{ route('admin.career-interest-areas.index') }}" class="dashboard-nav-link">
                    <i class="bi bi-briefcase"></i>
                    <span>Career Interest Areas</span>
                </a>
            </li>

            <li
                class="dashboard-nav-item {{ request()->routeIs('admin.institutions.*') ? 'dashboard-nav-item-active' : '' }}">
                <a href="{{ route('admin.institutions.index') }}" class="dashboard-nav-link">
                    <i class="bi bi-building"></i>
                    <span>Institutions</span>
                </a>
            </li>

            <li
                class="dashboard-nav-item {{ request()->routeIs('admin.employer-companies.*') ? 'dashboard-nav-item-active' : '' }}">
                <a href="{{ route('admin.employer-companies.index') }}" class="dashboard-nav-link">
                    <i class="bi bi-buildings"></i>
                    <span>Employer Companies</span>
                    @php
                        $pendingVerifications = \App\Models\EmployerCompany::where('verification_status', 'pending')->count();
                    @endphp
                    @if($pendingVerifications > 0)
                        <span class="badge bg-warning ms-2">{{ $pendingVerifications }}</span>
                    @endif
                </a>
            </li>

            <li
                class="dashboard-nav-item {{ request()->routeIs('admin.talent-verifications.*') ? 'dashboard-nav-item-active' : '' }}">
                <a href="{{ route('admin.talent-verifications.index') }}" class="dashboard-nav-link">
                    <i class="bi bi-person-check"></i>
                    <span>Talent Verifications</span>
                    @php
                        $pendingTalentVerifications = \App\Models\TalentProfile::where('verification_status', 'pending')->count();
                    @endphp
                    @if($pendingTalentVerifications > 0)
                        <span class="badge bg-danger ms-2">{{ $pendingTalentVerifications }}</span>
                    @endif
                </a>
            </li>

            <li
                class="dashboard-nav-item {{ request()->routeIs('admin.settings.*') ? 'dashboard-nav-item-active' : '' }}">
                <a href="{{ route('admin.settings.index') }}" class="dashboard-nav-link">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
            </li>

            <li class="dashboard-nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dashboard-nav-link dashboard-nav-link-logout1">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    <div class="dashboard-sidebar-footer">
        <span style="padding-left: 10px;color: #666;font-size: 14px;">&copy;{{ date('Y') }} {{config('app.name')}}
            v1.0.0</span>
    </div>
</aside>
@endif