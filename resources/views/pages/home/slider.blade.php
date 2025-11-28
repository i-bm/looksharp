<section class="text-light no-top no-bottom relative rounded-1 overflow-hidden m-3 sm-mt-90">
    <div class="mh-800">
        <div class="swiper">
            <!-- Additional required wrapper -->
            <div class="swiper-wrapper">


                @foreach (getSlider() as $slider)
                <div class="swiper-slide">
                    <div class="swiper-inner" data-bgimage="url({{ asset($slider['image']) }})">
                        <div class="sw-caption">
                            <div class="container">
                                <div class="row gx-5 align-items-center justify-content-between">
                                    <div class="col-lg-6">
                                        <div class="sw-text-wrapper">
                                            <h1 class="animated text-uppercase anim-order-1">
                                                {{ $slider['title'] }}</h1>

                                            <div class="animated anim-order-2">
                                                <p>{{ $slider['description'] }}</p>

                                                <div class="spacer-half"></div>

                                                <a class="btn-main fx-slide animated fadeInUp anim-order-3"
                                                    href="{{ url('/contact-us') }}"><span>Get in touch</span></a>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="abs w-100 bottom-0 z-2 pb-4 sm-hide">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="d-flex justify-content-between">
                                            @foreach (getServices() as $service)
                                            <div>
                                                <h6>{{ $service['name'] }}</h6>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sw-overlay op-4"></div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- If we need pagination -->
            <div class="swiper-pagination"></div>

            <!-- If we need navigation buttons -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
</section>
