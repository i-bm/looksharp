<section class="p-0 overflow-hidden" aria-label="section">
    {{-- <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <img src="{{ asset('assets/images/misc/c1.webp') }}" class="w-100 wow fadeInUp" alt="">
            </div>
        </div>
    </div> --}}
</section>

<section class="bg-color text-light pt-50 pb-50">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-9">
                <h3 class="mb-0 fs-32 split">Ready to Get Started?</h3>
            </div>
            <div class="col-lg-3 text-lg-end">
                <a class="btn-main fx-slide btn-line wow fadeInRight" data-wow-delay=".2s"
                    href="{{ route('contact') }}"><span>Contact Us</span></a>
            </div>
        </div>
    </div>
</section>
</div>
<!-- content close -->

<!-- footer begin -->
<footer class="text-light section-dark">
    <div class="container">
        <div class="row g-4 justify-content-between">
            <div class="col-md-6">
                <img src="{{ asset('assets/images/logo-white.png') }}" class="w-200px mb-2"
                    alt="logo of {{ config('misc.company') }}">
                <div class="spacer-single"></div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="widget">
                            <h5>Services</h5>
                            <ul>
                                @foreach (getServices() as $service)
                                <li><a href="#">{{ $service['short_name'] }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="widget">
                            <h5>Company</h5>
                            <ul>
                                <li><a href="{{ url('/') }}">Home</a></li>
                                <li><a href="{{ route('about') }}">About Us</a></li>
                                <li><a href="{{ url('/careers') }}">Careers</a></li>
                                <li><a href="{{ url('/blog') }}">Blog</a></li>
                                <li><a href="{{ route('contact') }}">Contact</a></li>
                            </ul>
                        </div>
                    </div>
                </div>


                <div class="social-icons mb-sm-30 text-center">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>

            <div class="col-md-6">
                <div class="d-flex align-items-center justify-content-between">
                    <h2>Get in Touch</h2>
                    <img src="{{ asset('assets/images/ui/up-right-arrow.webp') }}" class="w-60px op-5" alt="">
                </div>

                <div class="widget">
                    <div class="op-5 fs-15">Email</div>
                    <h3>{{ config('misc.email') }}</h3>

                    <div class="spacer-20"></div>

                    <div class="op-5 fs-15">Phone</div>
                    <h3>{{ config('misc.phone') }}</h3>

                    <div class="spacer-20"></div>

                    <div class="op-5 fs-15">Office Location</div>
                    <h3>{{ config('misc.address') }}</h3>

                    <div class="spacer-20"></div>

                </div>
            </div>
        </div>
    </div>
    <div class="subfooter">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    Copyright &copy; {{ date('Y') }} {{ config('misc.company') }}. All rights reserved.
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- footer close -->
</div>

<!-- overlay content begin -->
<div id="extra-wrap" class="text-light">
    <div id="btn-close">
        <span></span>
        <span></span>
    </div>

    <div id="extra-content">
        <img src="{{ asset('assets/images/logo-white.png') }}" class="w-200px" alt="">

        <div class="spacer-30-line"></div>

        <h5>Our Services</h5>
        <ul class="ul-check">
            @foreach (getServices() as $service)
            <li>{{ $service['name'] }}</li>
            @endforeach
        </ul>

        <div class="spacer-30-line"></div>

        <h5>Contact Us</h5>
        <div><i class="icofont-phone me-2 op-5"></i>{{ config('misc.phone') }}</div>
        <div><i class="icofont-location-pin me-2 op-5"></i>{{ config('misc.address') }}</div>
        <div><i class="icofont-envelope me-2 op-5"></i>{{ config('misc.email') }}</div>

        <div class="spacer-30-line"></div>

        <h5>About Us</h5>
        <p>We are a trusted logistics and cargo solutions provider committed to delivering your goods safely,
            efficiently, and on time. With years of experience in freight forwarding, warehousing, and international
            shipping.</p>

        <div class="social-icons">
            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="#"><i class="fa-brands fa-instagram"></i></a>
            <a href="#"><i class="fa-brands fa-youtube"></i></a>
            <a href="#"><i class="fa-brands fa-whatsapp"></i></a>
        </div>
    </div>
</div>
<!-- overlay content end -->


<!-- Javascript Files
    ================================================== -->
<script src="{{ asset('assets/js/plugins.js') }}"></script>
<script src="{{ asset('assets/js/designesia.js') }}"></script>
<script src="{{ asset('assets/js/swiper.js') }}"></script>
<script src="{{ asset('assets/js/custom-swiper-1.js') }}"></script>
<script src="{{ asset('assets/js/custom-marquee.js') }}"></script>

</body>

</html>
