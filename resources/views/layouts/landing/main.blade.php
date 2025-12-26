@include('layouts.landing.header')
@include('layouts.landing.navigation')
@yield('content')
<!-- Global Toast Container -->
<div id="global-toast-container" class="toast-container position-fixed p-3" style="z-index: 9999; top: 0; right: 0;"></div>
@include('layouts.landing.footer')
