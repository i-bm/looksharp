<!-- ==== banner section start ==== -->
<section class="common-banner">
    <div class="container">
        <div class="row">
            <div class="common-banner__content text-center">

                <h2 class="title-animation">{{ $title }}</h2>
            </div>
        </div>
    </div>
    <div class="banner-bg">
        <img src="{{isset($bannerBg) ? asset($bannerBg) : asset('assets/images/banner/banner-bg.png')}}" alt="Image">
    </div>
    <div class="shape">
        <img src="{{asset('assets/images/shape.png')}}" alt="Image">
    </div>
</section>
<!-- ==== / banner section end ==== -->