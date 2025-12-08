@include('layouts.dashboard.header')

<body class="dashboard-body">
    <div class="dashboard-layout">
        @include('layouts.dashboard.navigation')
        <div class="dashboard-main-wrapper">
            <!-- Mobile Hamburger Menu Button (kept for backward compatibility, hidden on mobile) -->
            <button class="dashboard-mobile-menu-btn dashboard-mobile-menu-btn-old" id="mobileMenuBtn" type="button"
                aria-label="Toggle menu">
                <i class="bi bi-list"></i>
            </button>
            @include('layouts.dashboard.top-header')
            @yield('content')
        </div>
        @include('layouts.dashboard.bottom-nav')
    </div>
    @include('layouts.dashboard.footer')