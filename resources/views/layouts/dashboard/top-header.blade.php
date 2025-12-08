@if(Auth::check())
@php
$user = Auth::user();
$profile = $user->talentProfile;
$fullName = $profile ? $profile->full_name : ($user->name ?? 'User');
$initials = '';
if ($profile) {
$initials = strtoupper(substr($profile->first_name ?? '', 0, 1) . substr($profile->last_name ?? '', 0, 1));
} elseif ($user->name) {
$nameParts = explode(' ', $user->name);
$initials = strtoupper(substr($nameParts[0] ?? '', 0, 1) . substr($nameParts[1] ?? '', 0, 1));
}
if (empty($initials)) {
$initials = 'U';
}
@endphp
<div class="dashboard-top-header">
    <!-- Mobile Header: Logo + Hamburger -->
    <div class="dashboard-top-header-mobile">
        <a href="{{ route('dashboard') }}" class="dashboard-top-header-logo">
            <img src="{{ asset('assets/img/logo-red.png') }}" alt="{{ config('app.name') }}"
                class="dashboard-top-header-logo-img">
        </a>
        <button class="dashboard-mobile-menu-btn" id="mobileMenuBtnTop" type="button" aria-label="Toggle menu">
            <i class="bi bi-list"></i>
        </button>
    </div>

    <!-- Desktop Header: Full Content -->
    <div class="dashboard-top-header-desktop">
        <div class="dashboard-top-header-left">
            <h1 class="dashboard-top-welcome">Welcome Back, {{ $fullName }}</h1>
            <p class="dashboard-top-subtitle">Here's what's happening with your profile today</p>
        </div>
        <div class="dashboard-top-header-right">
            <!-- Search Bar -->
            <div class="dashboard-top-search">
                <i class="bi bi-search dashboard-top-search-icon"></i>
                <input type="text" class="dashboard-top-search-input" placeholder="Search Here"
                    id="dashboardSearchInput">
            </div>

            <!-- Message Icon -->
            <button type="button" class="dashboard-top-icon-btn" title="Messages" id="messagesBtn">
                <i class="bi bi-envelope"></i>
            </button>

            <!-- Notification Icon -->
            <button type="button" class="dashboard-top-icon-btn" title="Notifications" id="notificationsBtn">
                <i class="bi bi-bell"></i>
            </button>

            <!-- User Avatar -->
            <div class="dashboard-top-avatar-container">
                <button type="button" class="dashboard-top-avatar-btn" id="userMenuBtn" title="User Menu">
                    @if($profile && $profile->profile_photo)
                    <img src="{{ asset('storage/'.$profile->profile_photo) }}" alt="{{ $fullName }}"
                        class="dashboard-top-avatar-img">
                    @else
                    <div class="dashboard-top-avatar-initials">{{ $initials }}</div>
                    @endif
                </button>
                <!-- User Dropdown Menu -->
                <div class="dashboard-top-user-menu" id="userMenuDropdown">
                    <a href="{{ route('talent.profile.show') }}" class="dashboard-top-user-menu-item">
                        <i class="bi bi-person"></i>
                        <span>Profile</span>
                    </a>
                    <a href="#" class="dashboard-top-user-menu-item">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}"
                        class="dashboard-top-user-menu-item dashboard-top-user-menu-item-form">
                        @csrf
                        <button type="submit" class="dashboard-top-user-menu-item-btn">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif