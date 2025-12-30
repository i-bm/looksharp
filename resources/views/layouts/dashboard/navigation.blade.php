@if(Auth::check())
@php
// Get current route name
$currentRoute = request()->route() ? request()->route()->getName() : '';

// Dashboard route
$isDashboard = $currentRoute === 'dashboard';

// Talent routes
$isTalentProfile = str_contains($currentRoute, 'talent.profile');
$isOpportunities = str_contains($currentRoute, 'opportunit') || str_contains($currentRoute, 'job') || str_contains($currentRoute, 'browse');
$isApplications = str_contains($currentRoute, 'application') || str_contains($currentRoute, 'applied');
$isInterviews = str_contains($currentRoute, 'interview');
$isJobAlerts = str_contains($currentRoute, 'alert') || str_contains($currentRoute, 'notification');

// Employer routes
$isEmployerCompany = str_contains($currentRoute, 'employer.company');
$isEmployerJobs = str_contains($currentRoute, 'employer.job') || str_contains($currentRoute, 'employer.post');
$isEmployerApplicants = str_contains($currentRoute, 'employer.applicant');
$isEmployerAnalytics = str_contains($currentRoute, 'employer.analytics') || str_contains($currentRoute, 'analytics');

// Settings routes
$isSettings = str_contains($currentRoute, 'settings') || str_contains($currentRoute, 'account') || str_contains($currentRoute, 'security');

// Parent menu expansion flags
$isOpportunitiesParent = $isOpportunities || $isApplications;
$isCompanyParent = $isEmployerCompany;
$isJobsParent = $isEmployerJobs || $isEmployerApplicants;
$isSettingsParent = $isSettings;
@endphp
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
            <li class="dashboard-nav-item {{ $isDashboard ? 'dashboard-nav-item-active' : '' }}">
                <a href="{{ route('dashboard') }}" class="dashboard-nav-link">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            @if(Auth::check() && Auth::user()->hasRole('talent'))
            <!-- Talent Navigation -->
            @php
                $talentProfile = Auth::user()->talentProfile;
                $isTalentVerified = $talentProfile && $talentProfile->isVerified();
                $talentVerificationStatus = $talentProfile ? $talentProfile->verification_status : null;
            @endphp
            <li class="dashboard-nav-item {{ $isTalentProfile ? 'dashboard-nav-item-active' : '' }}">
                <a href="{{ route('talent.profile.show') }}" class="dashboard-nav-link">
                    <i class="bi bi-person-badge"></i>
                    <span>Profile</span>
                    @if($talentProfile && !$isTalentVerified && $talentVerificationStatus === 'pending')
                        <span class="badge bg-warning ms-2" title="Verification pending">!</span>
                    @elseif($talentProfile && !$isTalentVerified && $talentVerificationStatus === 'rejected')
                        <span class="badge bg-danger ms-2" title="Verification rejected">!</span>
                    @elseif($talentProfile && $isTalentVerified)
                        <span class="badge bg-success ms-2" title="Verified">✓</span>
                    @elseif($talentProfile && !$isTalentVerified)
                        <span class="badge bg-secondary ms-2" title="Verification required">!</span>
                    @endif
                </a>

            </li>

            <li class="dashboard-nav-item dashboard-nav-item-parent {{ $isOpportunitiesParent ? 'active' : '' }}">
                <a href="#" class="dashboard-nav-link dashboard-nav-link-toggle">
                    <i class="bi bi-briefcase"></i>
                    <span>Opportunities</span>
                    @if($talentProfile && !$isTalentVerified)
                        <span class="badge bg-warning ms-2" title="Verification required to apply">!</span>
                    @endif
                    <i class="bi bi-chevron-down dashboard-nav-arrow"></i>
                </a>
                <ul class="dashboard-nav-submenu">
                    <li class="dashboard-nav-item {{ $isOpportunities ? 'dashboard-nav-item-active' : '' }}">
                        <a href="#" class="dashboard-nav-link {{ $talentProfile && !$isTalentVerified ? 'text-muted' : '' }}" 
                           @if($talentProfile && !$isTalentVerified) title="Verification required to apply for jobs" @endif>
                            <i class="bi bi-search"></i>
                            <span>Browse Jobs</span>
                            @if($talentProfile && !$isTalentVerified)
                                <span class="badge bg-warning ms-2" title="Verification required">!</span>
                            @endif
                        </a>
                    </li>
                    <li class="dashboard-nav-item {{ $isApplications ? 'dashboard-nav-item-active' : '' }}">
                        <a href="#" class="dashboard-nav-link {{ $talentProfile && !$isTalentVerified ? 'text-muted' : '' }}"
                           @if($talentProfile && !$isTalentVerified) title="Verification required to apply for jobs" @endif>
                            <i class="bi bi-file-check"></i>
                            <span>My Applications</span>
                            @if($talentProfile && !$isTalentVerified)
                                <span class="badge bg-warning ms-2" title="Verification required">!</span>
                            @endif
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

            <li class="dashboard-nav-item {{ $isInterviews ? 'dashboard-nav-item-active' : '' }}">
                <a href="#" class="dashboard-nav-link">
                    <i class="bi bi-calendar-check"></i>
                    <span>Interviews</span>
                </a>
            </li>

            <li class="dashboard-nav-item {{ $isJobAlerts ? 'dashboard-nav-item-active' : '' }}">
                <a href="#" class="dashboard-nav-link">
                    <i class="bi bi-bell"></i>
                    <span>Job Alerts</span>
                </a>
            </li>
            @elseif(Auth::check() && Auth::user()->hasRole('employer'))
            <!-- Employer Navigation -->
            @php
                $company = Auth::user()->employerCompany();
                $isVerified = $company && $company->isVerified();
                $verificationStatus = $company ? $company->verification_status : null;
            @endphp
            <li class="dashboard-nav-item dashboard-nav-item-parent {{ $isCompanyParent ? 'active' : '' }}">
                <a href="#" class="dashboard-nav-link dashboard-nav-link-toggle">
                    <i class="bi bi-building"></i>
                    <span>Company</span>
                    @if($company && !$isVerified && $verificationStatus === 'pending')
                        <span class="badge bg-warning ms-2" title="Verification pending">!</span>
                    @elseif($company && !$isVerified && $verificationStatus === 'rejected')
                        <span class="badge bg-danger ms-2" title="Verification rejected">!</span>
                    @elseif($company && $isVerified)
                        <span class="badge bg-success ms-2" title="Verified">✓</span>
                    @endif
                    <i class="bi bi-chevron-down dashboard-nav-arrow"></i>
                </a>
                <ul class="dashboard-nav-submenu">
                    <li class="dashboard-nav-item {{ $isEmployerCompany ? 'dashboard-nav-item-active' : '' }}">
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

            <li class="dashboard-nav-item dashboard-nav-item-parent {{ $isJobsParent ? 'active' : '' }}">
                <a href="#" class="dashboard-nav-link dashboard-nav-link-toggle">
                    <i class="bi bi-briefcase"></i>
                    <span>Jobs</span>
                    <i class="bi bi-chevron-down dashboard-nav-arrow"></i>
                </a>
                <ul class="dashboard-nav-submenu">
                    <li class="dashboard-nav-item {{ $isEmployerJobs ? 'dashboard-nav-item-active' : '' }}">
                        <a href="#" class="dashboard-nav-link {{ $company && !$isVerified ? 'text-muted' : '' }}" 
                           @if($company && !$isVerified) title="Company verification required for paid features" @endif>
                            <i class="bi bi-plus-circle"></i>
                            <span>Post Job</span>
                            @if($company && !$isVerified)
                                <span class="badge bg-warning ms-2" title="Verification required">!</span>
                            @endif
                        </a>
                    </li>
                    <li class="dashboard-nav-item {{ $isEmployerJobs ? 'dashboard-nav-item-active' : '' }}">
                        <a href="#" class="dashboard-nav-link">
                            <i class="bi bi-list-ul"></i>
                            <span>All Jobs</span>
                        </a>
                    </li>
                    <li class="dashboard-nav-item {{ $isEmployerApplicants ? 'dashboard-nav-item-active' : '' }}">
                        <a href="#" class="dashboard-nav-link">
                            <i class="bi bi-people"></i>
                            <span>Applicants</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="dashboard-nav-item {{ $isEmployerAnalytics ? 'dashboard-nav-item-active' : '' }}">
                <a href="#" class="dashboard-nav-link">
                    <i class="bi bi-graph-up"></i>
                    <span>Analytics</span>
                </a>
            </li>
            @endif

            <li class="dashboard-nav-item dashboard-nav-item-parent {{ $isSettingsParent ? 'active' : '' }}">
                <a href="#" class="dashboard-nav-link dashboard-nav-link-toggle">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                    <i class="bi bi-chevron-down dashboard-nav-arrow"></i>
                </a>
                <ul class="dashboard-nav-submenu">
                    <li class="dashboard-nav-item {{ str_contains($currentRoute, 'account') ? 'dashboard-nav-item-active' : '' }}">
                        <a href="#" class="dashboard-nav-link">
                            <i class="bi bi-person"></i>
                            <span>Account</span>
                        </a>
                    </li>
                    <li class="dashboard-nav-item {{ str_contains($currentRoute, 'security') ? 'dashboard-nav-item-active' : '' }}">
                        <a href="#" class="dashboard-nav-link">
                            <i class="bi bi-shield-lock"></i>
                            <span>Security</span>
                        </a>
                    </li>
                    <li class="dashboard-nav-item {{ str_contains($currentRoute, 'notification') ? 'dashboard-nav-item-active' : '' }}">
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