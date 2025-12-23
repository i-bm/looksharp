<div class="col-lg-6 d-none d-lg-flex auth-side-panel auth-login-slider-wrapper">
    <div class="swiper auth-login-slider">
        <div class="swiper-wrapper">
            <!-- Talent Slide -->
            <div class="swiper-slide auth-slide"
                style="background-image: url('{{ asset('assets/img/feature-img-3.jpg') }}');">
                <div class="auth-side-panel-overlay"></div>
                <div class="auth-side-panel-content">
                    <!-- Logo -->
                    <div>
                        <img src="{{ asset('assets/img/logo-white.png') }}" alt="Logo" class="auth-logo">
                    </div>

                    <!-- Image and Testimonial -->
                    <div class="auth-testimonial-container">
                        <div class="auth-testimonial-wrapper">
                            <p class="auth-testimonial-quote">
                                "Looksharp changed everything.
                                One-tap applies, real companies, no more ghosting.<br>
                                Wish it existed sooner!"
                            </p>
                            <div>
                                <p class="auth-testimonial-name">Student</p>
                                <p class="auth-testimonial-title">
                                    University of Ghana</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employer Slide -->
            <div class="swiper-slide auth-slide"
                style="background-image: url('{{ asset('assets/img/feature-img-4.jpg') }}');">
                <div class="auth-side-panel-overlay"></div>
                <div class="auth-side-panel-content">
                    <!-- Logo -->
                    <div>
                        <img src="{{ asset('assets/img/logo-white.png') }}" alt="Logo" class="auth-logo">
                    </div>

                    <!-- Image and Testimonial -->
                    <div class="auth-testimonial-container">
                        <div class="auth-testimonial-wrapper">
                            <p class="auth-testimonial-quote">
                                "We've found amazing talent through Looksharp. The quality of applicants is
                                outstanding,
                                and the platform makes recruitment so much easier."
                            </p>
                            <div>
                                <p class="auth-testimonial-name">HR Manager</p>
                                <p class="auth-testimonial-title">Leading Tech Company in Accra</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- University Slide -->
            <div class="swiper-slide auth-slide"
                style="background-image: url('{{ asset('assets/img/feature-img-5.jpg') }}');">
                <div class="auth-side-panel-overlay"></div>
                <div class="auth-side-panel-content">
                    <!-- Logo -->
                    <div>
                        <img src="{{ asset('assets/img/logo-white.png') }}" alt="Logo" class="auth-logo">
                    </div>

                    <!-- Image and Testimonial -->
                    <div class="auth-testimonial-container">
                        <div class="auth-testimonial-wrapper">
                            <p class="auth-testimonial-quote">
                                "Looksharp has transformed how we track student placements. The platform
                                gives us
                                real-time insights into where our students are interning and helps us
                                connect with
                                quality employers."
                            </p>
                            <div>
                                <p class="auth-testimonial-name">Career Services Officer</p>
                                <p class="auth-testimonial-title">Partner University</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pagination dots -->
        <div class="swiper-pagination auth-slider-pagination"></div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        function initAuthSlider() {
            if (typeof Swiper !== 'undefined' && document.querySelector(".auth-login-slider")) {
                var authLoginSwiper = new Swiper(".auth-login-slider", {
                    slidesPerView: 1,
                    speed: 1500,
                    spaceBetween: 0,
                    autoplay: {
                        delay: 4000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    },
                    effect: "fade",
                    fadeEffect: {
                        crossFade: true,
                    },
                    loop: true,
                    pagination: {
                        el: ".auth-slider-pagination",
                        clickable: true,
                    },
                });
            } else if (typeof Swiper === 'undefined') {
                // Retry after a short delay if Swiper isn't loaded yet
                setTimeout(initAuthSlider, 100);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAuthSlider);
        } else {
            initAuthSlider();
        }
    })();
</script>
@endpush