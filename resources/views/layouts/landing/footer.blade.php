<!-- Footer Section Start -->
<footer class="footer-section">
    <div class="container">
        <div class="company-logo-and-contact-area">
            <div class="row gy-5">
                <div class="col-lg-4">
                    <div class="footer-logo-and-social">
                        <div class="logo-area">
                            <a href="#"><img src="{{ asset('assets/img/logo-white.png') }}" width="300" alt=""></a>
                        </div>
                        <p>{{ config('misc.address') }}</p>
                        <ul class="social-list">
                            <li><a href="https://www.facebook.com/"><i class="bx bxl-facebook"></i></a></li>
                            <li><a href="https://www.linkedin.com/"><i class="bx bxl-linkedin"></i></a></li>
                            <li><a href="https://www.youtube.com/"><i class="bx bxl-youtube"></i></a></li>
                            <li><a href="https://www.instagram.com/"><i class="bx bxl-instagram-alt"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="contact-area">
                        <h2>Your trusted gateway to real internships.</h2>
                        <ul class="mail-and-call">
                            <li>
                                <div class="icon">
                                    <img src="{{ asset('assets/img/home1/icon/footer-mail.svg') }}" alt="">
                                </div>
                                <div class="content">
                                    <p>Send Us Mail</p>
                                    <a href="mailto:{{ config('misc.email') }}">{{ config('misc.email') }}</a>
                                </div>
                            </li>
                            <li>
                                <div class="icon">
                                    <img src="{{ asset('assets/img/home1/icon/footer-call-icon.svg') }}" alt="">
                                </div>
                                <div class="content">
                                    <p>Collaborate!</p>
                                    <a href="tel:{{ config('misc.phone') }}">{{ config('misc.phone') }}</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="footer-menu">
        <div class="container">
            <div class="row gy-5 justify-content-between">
                <div class="col-xl-4 col-lg-3 col-md-4 col-sm-6">
                    <div class="footer-widget">
                        <div class="widget-title">
                            <h3>Download App</h3>
                        </div>
                        <div class="store">
                            <a href="#"><img src="assets/img/home1/icon/play-store.svg" alt="Play-store"></a>
                            <a href="#"><img src="assets/img/home1/icon/apple-store.svg" alt="apple-store"></a>
                        </div>
                    </div>
                </div>
                <div
                    class="col-xl-2 col-lg-3 col-md-4 col-sm-6 d-flex justify-content-lg-start justify-content-md-center justify-content-sm-center">
                    <div class="footer-widget">
                        <div class="widget-title">
                            <h3>Company</h3>
                        </div>
                        <ul class="widget-list">
                            <li><a href="history.html">Our History</a></li>
                            <li><a href="team.html">Our Expert</a></li>
                            <li><a href="news-and-insight.html">News & Media</a></li>
                            <li><a href="career.html">Careers <span>Hiring</span></a></li>
                            <li><a href="certification.html">Certifications</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 d-flex justify-content-lg-center justify-content-md-end">
                    <div class="footer-widget">
                        <div class="widget-title">
                            <h3>Solutions</h3>
                        </div>
                        <ul class="widget-list">
                            <li><a href="solution-details.html">Business Solution</a></li>
                            <li><a href="solution-details.html">Container Shipping</a></li>
                            <li><a href="solution-details.html">International Courier</a></li>
                            <li><a href="solution-details.html">Document & Secure Package</a></li>
                            <li><a href="solution-details.html">Custom courier solutions</a></li>
                        </ul>
                    </div>
                </div>
                <div
                    class="col-xl-2 col-lg-3 col-md-4 col-sm-6 d-flex justify-content-lg-end justify-content-md-start justify-content-sm-center">
                    <div class="footer-widget">
                        <div class="widget-title">
                            <h3>Support</h3>
                        </div>
                        <ul class="widget-list">
                            <li><a href="get-in-touch.html">Request a quote</a></li>
                            <li><a href="terms-and-conditions.html">terms & conditions</a></li>
                            <li><a href="terms-and-conditions.html">refund policy</a></li>
                            <li><a href="#">Offer & Discount</a></li>
                            <li><a href="https://www.google.com/maps">Visit Sitemap</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="footer-bottom">
        <div class="container">
            <p>Copyright &copy; {{ date('Y') }} <a href="{{ url('/') }}">{{ config('app.name') }}</a>. All Rights
                Reserved.</p>
        </div>
    </div>
</footer>
<!-- Footer Section End -->
<!--  Main jQuery  -->
<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery-ui.js') }}"></script>
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker.min.js') }}"></script>

<!-- Popper and Bootstrap JS -->
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/popper.min.js') }}"></script>
<!-- Swiper slider JS -->
<script src="{{ asset('assets/js/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/slick.js') }}"></script>
<!-- Waypoints JS -->
<script src="{{ asset('assets/js/waypoints.min.js') }}"></script>
<!-- Counterup JS -->
<script src="{{ asset('assets/js/jquery.counterup.min.js') }}"></script>
<!-- Wow JS -->
<script src="{{ asset('assets/js/wow.min.js') }}"></script>
<!-- Gsap  JS -->
<script src="{{ asset('assets/js/gsap.min.js') }}"></script>
<script src="{{ asset('assets/js/ScrollTrigger.min.js') }}"></script>
<script src="assets/js/jquery.fancybox.min.js"></script>
<!-- Custom JS -->
<script src="{{ asset('assets/js/select-dropdown.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>
<script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015"
    integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ=="
    data-cf-beacon='{"version":"2024.11.0","token":"70834e4b23964a2eaf7cf4ec0e5e9a84","r":1,"server_timing":{"name":{"cfCacheStatus":true,"cfEdge":true,"cfExtPri":true,"cfL4":true,"cfOrigin":true,"cfSpeedBrain":true},"location_startswith":null}}'
    crossorigin="anonymous"></script>
</body>

</html>
