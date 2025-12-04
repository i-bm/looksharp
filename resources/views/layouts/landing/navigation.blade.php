<!-- header Section Start-->
<header class="header-area style-1">
    <div class="container-fluid d-flex flex-nowrap align-items-center justify-content-between">
        <div class="logo-and-menu-area">
            <a href="#" class="header-logo">
                @if (request()->is('/'))
                <img src="{{ asset('assets/img/logo-white.png') }}" alt="logo">
                @else
                <img src="{{ asset('assets/img/logo-red.png') }}" alt="logo">
                @endif
            </a>
            <div class="main-menu">
                <div class="mobile-logo-area d-xl-none d-flex align-items-center justify-content-between">
                    <a href="index.html" class="mobile-logo-wrap">
                        <img src="{{ asset('assets/img/logo-red.png') }}" alt="">
                    </a>
                    <div class="menu-close-btn">
                        <i class="bi bi-x"></i>
                    </div>
                </div>
                <ul class="menu-list">
                    <li class="menu-item-has-children {{ request()->is('/') ? 'active' : '' }}">
                        <a href="{{ url('/') }}" @if (!request()->is('/')) class="text-dark" @endif> Home </a>
                    </li>

                    <li class="menu-item-has-children {{ request()->is('students') ? 'active' : '' }}">
                        <a href="{{ route('students') }}"
                            class="{{ !request()->is('/') && !request()->is('students') ? 'text-dark' : '' }}"> For
                            Students </a>
                    </li>

                    <li class="menu-item-has-children {{ request()->is('employers') ? 'active' : '' }}">
                        <a href="{{ route('employers') }}"
                            class="{{ !request()->is('/') && !request()->is('employers') ? 'text-dark' : '' }}"> For
                            Employers </a>
                    </li>

                    <li class="menu-item-has-children {{ request()->is('universities') ? 'active' : '' }}">
                        <a href="{{ route('universities') }}"
                            class="{{ !request()->is('/') && !request()->is('universities') ? 'text-dark' : '' }}"> For
                            Universities </a>
                    </li>
                </ul>
                <a class="primary-btn1 btn-hover d-xl-none" href="{{ route('login') }}">
                    Get Started
                    <svg width="10" height="10" viewBox="0 0 10 10" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path
                                d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z">
                            </path>
                        </g>
                    </svg>
                    <span></span>
                </a>
            </div>
        </div>
        <div class="nav-right">
            <div class="contact-area">
                {{-- <div class="search-and-login">
                    <div class="search-bar">
                        <div class="search-btn">
                            <svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path
                                        d="M15.7417 14.6098L13.486 12.3621C14.7088 10.8514 15.3054 8.9291 15.1526 6.99153C14.9998 5.05396 14.1093 3.24888 12.6648 1.94851C11.2203 0.648146 9.33193 -0.0483622 7.38901 0.00261294C5.44609 0.0535881 3.59681 0.84816 2.22248 2.22248C0.84816 3.59681 0.0535881 5.44609 0.00261294 7.38901C-0.0483622 9.33193 0.648146 11.2203 1.94851 12.6648C3.24888 14.1093 5.05396 14.9998 6.99153 15.1526C8.9291 15.3054 10.8514 14.7088 12.3621 13.486L14.6098 15.7417C14.6839 15.8164 14.7721 15.8757 14.8692 15.9161C14.9664 15.9566 15.0705 15.9774 15.1758 15.9774C15.281 15.9774 15.3852 15.9566 15.4823 15.9161C15.5794 15.8757 15.6676 15.8164 15.7417 15.7417C15.8164 15.6676 15.8757 15.5794 15.9161 15.4823C15.9566 15.3852 15.9774 15.281 15.9774 15.1758C15.9774 15.0705 15.9566 14.9664 15.9161 14.8692C15.8757 14.7721 15.8164 14.6839 15.7417 14.6098ZM1.62572 7.60368C1.62572 6.42135 1.97632 5.26557 2.63319 4.2825C3.29005 3.29943 4.22368 2.53322 5.31601 2.08076C6.40834 1.62831 7.61031 1.50992 8.76992 1.74058C9.92953 1.97124 10.9947 2.54059 11.8307 3.37662C12.6668 4.21266 13.2361 5.27783 13.4668 6.43744C13.6974 7.59705 13.579 8.79902 13.1266 9.89134C12.6741 10.9837 11.9079 11.9173 10.9249 12.5742C9.94178 13.231 8.78601 13.5816 7.60368 13.5816C6.01822 13.5816 4.49771 12.9518 3.37662 11.8307C2.25554 10.7096 1.62572 9.18913 1.62572 7.60368Z" />
                                </g>
                            </svg>
                        </div>
                        <div class="search-input">
                            <div class="search-close"></div>
                            <form>
                                <div class="search-group">
                                    <div class="form-inner2">
                                        <input type="text" placeholder="Enter your keywords">
                                        <button type="submit"><i class="bi bi-search"></i></button>
                                    </div>
                                </div>
                                <div class="quick-search">
                                    <ul>
                                        <li>Quick Search :</li>
                                        <li><a href="solution-details.html">Freight Transportation</a></li>
                                        <li><a href="solution-details.html">Logistics & Distribution</a></li>
                                        <li><a href="solution-details.html">Ground Transportation</a></li>
                                        <li><a href="solution-details.html">Reverse Logistics</a></li>
                                        <li><a href="solution-details.html">Renewable Energy</a></li>
                                        <li><a href="solution-details.html">Retail & E-commerce</a></li>
                                        <li><a href="solution-details.html">Fashion and Textiles</a></li>
                                    </ul>
                                </div>
                            </form>
                        </div>
                    </div>
                    <a href="login-page.html" class="login-btn">
                        <svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                            <g>
                                <path
                                    d="M8.00093 8.3084C10.2867 8.3084 12.1551 6.43993 12.1551 4.1542C12.1551 1.86847 10.2867 0 8.00093 0C5.7152 0 3.84676 1.86847 3.84676 4.1542C3.84676 6.43993 5.71523 8.3084 8.00093 8.3084ZM15.1302 11.6281C15.0213 11.356 14.8762 11.102 14.713 10.8662C13.8785 9.63264 12.5905 8.81632 11.1393 8.61677C10.9579 8.59865 10.7584 8.6349 10.6132 8.74375C9.85131 9.30611 8.94429 9.59635 8.00096 9.59635C7.05763 9.59635 6.15061 9.30611 5.3887 8.74375C5.24356 8.6349 5.04402 8.58049 4.86263 8.61677C3.41138 8.81632 2.10527 9.63264 1.28895 10.8662C1.12568 11.102 0.980543 11.3742 0.871724 11.6281C0.817314 11.737 0.835439 11.864 0.889849 11.9728C1.03499 12.2268 1.21638 12.4808 1.37964 12.6984C1.6336 13.0431 1.90572 13.3515 2.21412 13.6417C2.46808 13.8957 2.75832 14.1315 3.0486 14.3674C4.48169 15.4377 6.20506 16 7.98284 16C9.76061 16 11.484 15.4376 12.9171 14.3674C13.2073 14.1497 13.4976 13.8957 13.7516 13.6417C14.0418 13.3515 14.332 13.0431 14.586 12.6984C14.7674 12.4626 14.9307 12.2268 15.0758 11.9728C15.1665 11.864 15.1846 11.7369 15.1302 11.6281Z" />
                            </g>
                        </svg>
                    </a>
                </div> --}}
                <a class=" {{ request()->is('/') ? 'primary-btn1' : 'primary-btn2' }} btn-hover text-white d-xl-flex d-none"
                    href="{{ route('login') }}">
                    Get Started
                    <svg width="10" height="10" viewBox="0 0 10 10" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path
                                d="M5.83333 4.16667V0H4.16667V4.16667H0V5.83333H4.16667V10H5.83333V5.83333H10V4.16667H5.83333Z" />
                        </g>
                    </svg>
                    <span></span>
                </a>
            </div>
            <div class="sidebar-button mobile-menu-btn">
                <svg width="20" height="18" viewBox="0 0 20 18" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M1.29445 2.8421H10.5237C11.2389 2.8421 11.8182 2.2062 11.8182 1.42105C11.8182 0.635903 11.2389 0 10.5237 0H1.29445C0.579249 0 0 0.635903 0 1.42105C0 2.2062 0.579249 2.8421 1.29445 2.8421Z">
                    </path>
                    <path
                        d="M1.23002 10.421H18.77C19.4496 10.421 20 9.78506 20 8.99991C20 8.21476 19.4496 7.57886 18.77 7.57886H1.23002C0.550421 7.57886 0 8.21476 0 8.99991C0 9.78506 0.550421 10.421 1.23002 10.421Z">
                    </path>
                    <path
                        d="M18.8052 15.1579H10.2858C9.62563 15.1579 9.09094 15.7938 9.09094 16.5789C9.09094 17.3641 9.62563 18 10.2858 18H18.8052C19.4653 18 20 17.3641 20 16.5789C20 15.7938 19.4653 15.1579 18.8052 15.1579Z">
                    </path>
                </svg>
            </div>
        </div>
    </div>
</header>
