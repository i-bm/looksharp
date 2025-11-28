<!-- header begin -->
<header class="header-static transparent mt-lg-4 pt-lg-2">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="de-flex sm-pt10">
                    <div class="de-flex-col">
                        <!-- logo begin -->
                        <div id="logo">
                            <a href="{{ url('/') }}">
                                <img class="logo-main" src="{{ asset('assets/images/logo-white.png') }}"
                                    alt="logo of {{ config('misc.company') }}">
                                <img class="logo-scroll" src="{{ asset('assets/images/logo-white.png') }}"
                                    alt="logo of {{ config('misc.company') }}">
                                <img class="logo-mobile" src="{{ asset('assets/images/logo-white.png') }}"
                                    alt="logo of {{ config('misc.company') }}">
                            </a>
                        </div>
                        <!-- logo end -->
                    </div>
                    <div class="de-flex-col header-col-mid">
                        <!-- mainemenu begin -->
                        <ul id="mainmenu">
                            <li><a class="menu-item" href="{{ url('/') }}">Home</a></li>
                            <li><a class="menu-item" href="{{ route('services') }}">Services</a>
                                <ul>
                                    @foreach (getServices() as $service)
                                    <li><a href="{{ $service['route'] }}">{{ $service['name'] }}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                            <li><a class="menu-item" href="{{ route('about') }}">Company</a>
                            </li>
                            <li><a class="menu-item" href="{{ url('/careers') }}">Careers</a>
                            </li>
                            <li><a class="menu-item" href="{{ url('/blog') }}">Blog</a></li>
                            <li><a class="menu-item" href="{{ route('contact') }}">Contact</a></li>
                        </ul>
                        <!-- mainmenu end -->
                    </div>
                    <div class="de-flex-col">
                        <div class="menu_side_area">
                            <a href="{{ route('contact') }}" class="btn-main btn-line fx-slide"><span>Get in
                                    touch</span></a>
                            <span id="menu-btn"></span>
                        </div>

                        <div id="btn-extra">
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- header close -->
