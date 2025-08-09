<!-- Header area start -->
<header class="header-area-7 header-p-inline-60">
    <div class="header-main">
        <div class="container rr-container-1405">
            <div class="header-area-7__inner">
                <div class="header__logo">
                    <a href="{{ url('/') }}">
                        <img src="{{  asset('assets/imgs/logo/logo-2-light.png')}}" class="normal-logo"
                            alt="nexcore-logo">
                    </a>
                </div>
                <div class="header__nav pos-center">
                    <nav class="main-menu">
                        <ul>
                            <li>
                                <a href="#">Home</a>
                            </li>
                            <li><a href="about.html">About Us</a></li>
                            <li class="menu-item-has-children">
                                <a href="#">Service</a>
                                <ul class="dp-menu">
                                    @foreach (getServices() as $service)
                                    <li><a href="#">{{ $service['name'] }}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                            <li><a href="#">Blog</a> </li>
                            <li><a href="#">Contact</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="header__navicon">
                    <button class="side-toggle">
                        <img src="assets/imgs/icon/icon-11.webp" alt="imageimage">
                        Menu
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- Header area end -->
