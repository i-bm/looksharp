<section id="section-services" class="py-120">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="mb-4">
                    <a href="#services"
                        class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-4 py-2 rounded-pill border-2"
                        style="background: #fff; border-color: #e0e0e0; color: #333; text-decoration: none; font-weight: 500;">
                        <i class="fa-solid fa-wrench"></i>
                        <span>Our services</span>
                    </a>
                </div>
                <h1 class="display-3 fw-bold mb-0"
                    style="font-family: var(--heading-font); color: #2c2c2c; font-style: italic; line-height: 1.2;">
                    What we can do<br>
                    <span style="display: block; margin-top: 0.5rem;">for you</span>
                </h1>
            </div>
            <div class="col-lg-6">
                <p class="mb-4" style="color: #555; font-size: 16px; line-height: 1.8;">
                    From design to implementation, we provide quality IT solutions tailored to your business needs. Our
                    expert team delivers cutting-edge technology services that drive growth and innovation.
                </p>
                <a href="#services" class="btn-main fx-slide  bg-dark text-white1">
                    <span>See our services</span>
                </a>
            </div>
        </div>
    </div>

    <div class="mx-3 p-0 mt-5">
        <div class="container">
            <div class="row g-3">

                @foreach (getServices() as $service)
                <div class="col-lg-4 col-md-6">
                    <a href="{{ $service['route'] }}">
                        <div class="hover rounded-1 relative overflow-hidden text-light wow zoomIn" data-wow-delay="0s">
                            <div class="abs p-40 bottom-0 z-3">
                                <h3 class="border-top pt-3">{{ $service['name'] }}</h3>
                                <p class="mb-0 hover-mh-60">Read More</p>
                            </div>
                            <img src="{{ asset($service['image']) }}" class="w-100 hover-scale-1-2 wow scaleIn"
                                data-wow-delay="0s" alt="">
                            <div class="gradient-edge-bottom h-50 op-5"></div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
