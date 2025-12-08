@if(Auth::check())
@php
$user = Auth::user();
$profile = $user->talentProfile;
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

// Determine active route
$currentRoute = request()->route()->getName();
$isHome = $currentRoute === 'dashboard';
$isOpportunities = str_contains($currentRoute, 'opportunit') || str_contains($currentRoute, 'job') ||
str_contains($currentRoute, 'browse');
$isApplications = str_contains($currentRoute, 'application') || str_contains($currentRoute, 'applied');
$isNotifications = str_contains($currentRoute, 'notification');
$isAccount = str_contains($currentRoute, 'profile') && !str_contains($currentRoute, 'location');
@endphp
<nav class="dashboard-bottom-nav">
    <a href="{{ route('dashboard') }}" class="dashboard-bottom-nav-item {{ $isHome ? 'active' : '' }}"
        data-route="dashboard">
        <i class="bi bi-house"></i>
        <span>Home</span>
    </a>
    <a href="#" class="dashboard-bottom-nav-item {{ $isOpportunities ? 'active' : '' }}" data-route="opportunities">
        <i class="bi bi-briefcase"></i>
        <span>Opportunities</span>
    </a>
    <a href="#" class="dashboard-bottom-nav-item {{ $isApplications ? 'active' : '' }}" data-route="applications">
        <i class="bi bi-file-check"></i>
        <span>Applications</span>
    </a>
    <a href="#" class="dashboard-bottom-nav-item {{ $isNotifications ? 'active' : '' }}" data-route="notifications">
        <i class="bi bi-bell"></i>
        <span>Notifications</span>
    </a>
    <a href="{{ route('talent.profile.show') }}" class="dashboard-bottom-nav-item {{ $isAccount ? 'active' : '' }}"
        data-route="account">
        @if($profile && $profile->profile_photo)
        <img src="{{ asset('storage/'.$profile->profile_photo) }}" alt="Account" class="dashboard-bottom-nav-avatar">
        @else
        <div class="dashboard-bottom-nav-avatar-initials">{{ $initials }}</div>
        @endif
        <span>Account</span>
    </a>
</nav>
@endif