@if(Auth::check())
<!-- Sidebar Backdrop (Mobile) -->
<div class="dashboard-sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- Sidebar Navigation -->
<aside class="dashboard-sidebar">
    <div class="dashboard-sidebar-header">
        <a href="{{ route('dashboard') }}" class="dashboard-logo">
            <img src="{{ asset('assets/img/logo-red.png') }}" alt="{{ config('app.name') }}" class="dashboard-logo-img">
            {{-- <span class="dashboard-logo-text">{{ config('app.name') }}</span> --}}
        </a>
        <button class="dashboard-sidebar-toggle" id="sidebarToggle" title="Collapse sidebar">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>

    <nav class="dashboard-nav">
        <ul class="dashboard-nav-list">
            <li class="dashboard-nav-item dashboard-nav-item-active">
                <a href="{{ route('dashboard') }}" class="dashboard-nav-link">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            @if(Auth::check() && Auth::user()->hasRole('talent'))
            <!-- Talent Navigation -->
            <li class="dashboard-nav-item">
                <a href="{{ route('talent.profile.show') }}" class="dashboard-nav-link">
                    <i class="bi bi-person-badge"></i>
                    <span>Profile</span>
                </a>

            </li>

            <li class="dashboard-nav-item dashboard-nav-item-parent">
                <a href="#" class="dashboard-nav-link dashboard-nav-link-toggle">
                    <i class="bi bi-briefcase"></i>
                    <span>Opportunities</span>
                    <i class="bi bi-chevron-down dashboard-nav-arrow"></i>
                </a>
                <ul class="dashboard-nav-submenu">
                    <li class="dashboard-nav-item">
                        <a href="#" class="dashboard-nav-link">
                            <i class="bi bi-search"></i>
                            <span>Browse Jobs</span>
                        </a>
                    </li>
                    <li class="dashboard-nav-item">
                        <a href="#" class="dashboard-nav-link">
                            <i class="bi bi-file-check"></i>
                            <span>My Applications</span>
                        </a>
                    </li>
                    <li class="dashboard-nav-item">
                        <a href="#" class="dashboard-nav-link">
                            <i class="bi bi-bookmark"></i>
                            <span>Saved Jobs</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="dashboard-nav-item">
                <a href="#" class="dashboard-nav-link">
                    <i class="bi bi-calendar-check"></i>
                    <span>Interviews</span>
                </a>
            </li>

            <li class="dashboard-nav-item">
                <a href="#" class="dashboard-nav-link">
                    <i class="bi bi-bell"></i>
                    <span>Job Alerts</span>
                </a>
            </li>
            @elseif(Auth::check() && Auth::user()->hasRole('employer'))
            <!-- Employer Navigation -->
            <li class="dashboard-nav-item dashboard-nav-item-parent">
                <a href="#" class="dashboard-nav-link dashboard-nav-link-toggle">
                    <i class="bi bi-building"></i>
                    <span>Company</span>
                    <i class="bi bi-chevron-down dashboard-nav-arrow"></i>
                </a>
                <ul class="dashboard-nav-submenu">
                    <li class="dashboard-nav-item">
                        <a href="{{ route('employer.company.show') }}" class="dashboard-nav-link">
                            <i class="bi bi-building-gear"></i>
                            <span>Company Profile</span>
                        </a>
                    </li>
                    <li class="dashboard-nav-item">
                        <a href="#" class="dashboard-nav-link">
                            <i class="bi bi-people"></i>
                            <span>Team Members</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="dashboard-nav-item dashboard-nav-item-parent">
                <a href="#" class="dashboard-nav-link dashboard-nav-link-toggle">
                    <i class="bi bi-briefcase"></i>
                    <span>Jobs</span>
                    <i class="bi bi-chevron-down dashboard-nav-arrow"></i>
                </a>
                <ul class="dashboard-nav-submenu">
                    <li class="dashboard-nav-item">
                        <a href="#" class="dashboard-nav-link">
                            <i class="bi bi-plus-circle"></i>
                            <span>Post Job</span>
                        </a>
                    </li>
                    <li class="dashboard-nav-item">
                        <a href="#" class="dashboard-nav-link">
                            <i class="bi bi-list-ul"></i>
                            <span>All Jobs</span>
                        </a>
                    </li>
                    <li class="dashboard-nav-item">
                        <a href="#" class="dashboard-nav-link">
                            <i class="bi bi-people"></i>
                            <span>Applicants</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="dashboard-nav-item">
                <a href="#" class="dashboard-nav-link">
                    <i class="bi bi-graph-up"></i>
                    <span>Analytics</span>
                </a>
            </li>
            @endif

            <li class="dashboard-nav-item dashboard-nav-item-parent">
                <a href="#" class="dashboard-nav-link dashboard-nav-link-toggle">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                    <i class="bi bi-chevron-down dashboard-nav-arrow"></i>
                </a>
                <ul class="dashboard-nav-submenu">
                    <li class="dashboard-nav-item">
                        <a href="#" class="dashboard-nav-link">
                            <i class="bi bi-person"></i>
                            <span>Account</span>
                        </a>
                    </li>
                    <li class="dashboard-nav-item">
                        <a href="#" class="dashboard-nav-link">
                            <i class="bi bi-shield-lock"></i>
                            <span>Security</span>
                        </a>
                    </li>
                    <li class="dashboard-nav-item">
                        <a href="#" class="dashboard-nav-link">
                            <i class="bi bi-bell"></i>
                            <span>Notifications</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="dashboard-nav-item">
                <a href="#" class="dashboard-nav-link">
                    <i class="bi bi-question-circle"></i>
                    <span>Help</span>
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