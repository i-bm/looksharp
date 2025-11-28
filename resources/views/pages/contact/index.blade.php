@extends('layouts.landing.main')

@section('content')
@include('pages.partials.breadcrumbs-sm', ['parentTitle' => 'Contact Us', 'title' => 'Contact Us', 'bannerBg' =>
'assets/images/background/1.jpg'])


<section class="relative">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-6">
                <div class="subtitle">Contact Us Now</div>
                <h2 class="wow fadeInUp">We’re here to answer your questions.</h2>

                <p class="col-lg-8">Have a question, suggestion, or just want to say hi? We’re here and happy to hear
                    from you!</p>

                <div class="spacer-single"></div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="relative mb-4">
                            <i class="abs fs-28 p-3 bg-color text-light rounded-1 icofont-location-pin"></i>
                            <div class="ms-80px">
                                <h4 class="mb-0">Office Location</h4>
                                <a href="{{ config('misc.address') }}">{{ config('misc.address') }}</a>
                            </div>
                        </div>

                        <div class="relative mb-4">
                            <i class="abs fs-28 p-3 bg-color text-light rounded-1 icofont-envelope"></i>
                            <div class="ms-80px">
                                <h4 class="mb-0">Send a Message</h4>
                                <a href="mailto:{{ config('misc.email') }}">{{ config('misc.email') }}</a>
                            </div>
                        </div>

                        <div class="relative mb-4">
                            <i class="abs fs-28 p-3 bg-color text-light rounded-1 icofont-phone"></i>
                            <div class="ms-80px">
                                <h4 class="mb-0">Call Us Directly</h4>
                                <a href="tel:{{ config('misc.phone') }}">{{ config('misc.phone') }}</a>
                            </div>
                        </div>

                        <div class="relative mb-4">
                            <i class="abs fs-28 p-3 bg-color text-light rounded-1 icofont-whatsapp"></i>
                            <div class="ms-80px">
                                <h4 class="mb-0">WhatsApp Us</h4>
                                <a href="https://wa.me/{{ config('misc.whatsapp_number') }}">{{
                                    config('misc.phone') }}</a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            @include('pages.contact.form')
        </div>
    </div>
</section>


@include('pages.home.testimonials')
@endsection
