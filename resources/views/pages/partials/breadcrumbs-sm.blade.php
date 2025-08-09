<!-- ==== banner section start ==== -->
<style>
    .common-banner1{
        background-image: url('{{asset('assets/images/banner/banner-bg.png')}}');
        background-size: cover;
       /* padding: 120px 0px; */
    }
    @media only screen and (min-width: 1400px) {
    .common-banner1 {
        padding: 100px 0px;
        margin-top: 150px;
    }
}

    @media (min-width: 992px) {
    .common-banner1 {
        padding: 100px 0px;
        margin-top: 140px;
    }
}
</style>
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
    {{-- <div class="shape">
        <img src="{{asset('assets/images/shape.png')}}" alt="Image">
    </div> --}}
</section>
<!-- ==== / banner section end ==== -->