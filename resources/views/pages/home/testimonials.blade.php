<section class="text-light bg-dark no-top no-bottom overflow-hidden mx-3 mb-3 rounded-1">
    <div class="container-fluid position-relative half-fluid">
        <div class="container">
            <div class="row">
                <!-- Image -->
                <div class="col-lg-6 position-lg-absolute left-half h-100">
                    <div class="triangle-bottomright-dark"></div>
                    <div class="image" data-bgimage="url({{ asset('assets/images/misc/2.jpg') }}) center"></div>
                </div>
                <!-- Text -->
                <div class="col-lg-5 offset-lg-7">
                    <div class="me-lg-3">
                        <div class="py-5 my-5">
                            <div class="owl-single-dots owl-carousel owl-theme">

                                @foreach (getTestimonials() as $testimonial)
                                <div class="item">
                                    <i class="icofont-quote-left id-color fs-40 mb-4 wow fadeInUp"></i>
                                    <h3 class="mb-4 wow fadeInUp fs-32">{{ $testimonial['description'] }}</h3>
                                    <span class="wow fadeInUp">{{ $testimonial['title'] }}, {{ $testimonial['company']
                                        }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</section>
