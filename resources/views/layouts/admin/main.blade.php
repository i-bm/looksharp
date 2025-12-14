@include('layouts.dashboard.header')
<link rel="stylesheet" href="{{ asset('assets/css/admin-dashboard.css') }}">

<body class="dashboard-body">
    <div class="dashboard-layout">
        @include('layouts.admin.navigation')
        <div class="dashboard-main-wrapper">
            <!-- Mobile Hamburger Menu Button -->
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