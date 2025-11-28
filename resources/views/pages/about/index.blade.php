@extends('layouts.landing.main')

@section('content')
@include('pages.partials.breadcrumbs-sm', ['parentTitle' => 'About Us', 'title' => 'About Us', 'bannerBg' =>
'assets/images/background/1.jpg'])


<section>
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <div class="relative">
                    <div class="w-100 pe-5 pb-5 wow scaleIn">
                        <img src="{{ asset('assets/images/misc/l1.webp') }}" class="w-100 rounded-1"
                            alt="Cargo logistics service">
                    </div>
                    <img src="{{ asset('assets/images/misc/s1.webp') }}"
                        class="w-40 rounded-1 abs end-0 bottom-0 z-2 soft-shadow wow scaleIn" data-wow-delay=".2s"
                        alt="Freight transport">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="ps-lg-3">
                    <div class="subtitle id-color wow fadeInUp" data-wow-delay=".2s">About Us</div>
                    <h2 class="split">Moving Cargo, Connecting the World</h2>
                    <p class="mb-0 wow fadeInUp text-dark" data-wow-delay=".6s">

                        We are a premier IT Consulting and Managed Services Provider (MSP) dedicated to empowering Small
                        and Medium Enterprises (SMEs) across Ghana with reliable, scalable, and future-ready technology
                        solutions.</p>
                    <p>Our team consists of seasoned professionals holding top-tier certifications from industry leaders
                        including Microsoft, Cisco, AWS, Oracle, Google Cloud, Juniper, HPE, and more. This depth of
                        expertise enables us to deliver strategic, vendor-agnostic recommendations tailored specifically
                        to your business objectives.</p>
                    <p>Every engagement begins with a comprehensive business process analysis, ensuring the solutions we
                        design and implement drive measurable operational efficiency, reduce costs, and accelerate
                        growth.</p>
                    <p>At BM Technologies, customer success is our core mission. We combine proactive managed services,
                        rapid response times, predictable budgeting, and a genuine partnership approach to become the
                        trusted extension of your IT team — letting you focus on running and growing your business while
                        we handle the technology.</p>
                    <p>BM Technologies – Strategic IT Partnerships for Ghanaian SMEs. On time. On budget. Built for your
                        success.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
