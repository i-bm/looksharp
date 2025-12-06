@include('layouts.dashboard.header')
<body class="dashboard-body">
    <div class="dashboard-layout">
        @include('layouts.dashboard.navigation')
        <div class="dashboard-main-wrapper">
            @yield('content')
        </div>
    </div>
@include('layouts.dashboard.footer')
