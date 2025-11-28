<section id="subheader" class="text-light sm-mt-90 relative rounded-1 overflow-hidden m-3"
    data-bgimage="url({{ isset($bannerBg) ? asset($bannerBg) : asset('assets/images/background/1.jpg') }}) center">
    <div class="container relative z-2">
        <div class="row gy-4 gx-5 align-items-center">
            <div class="col-lg-12">
                <h1 class="split">{{ $title }}</h1>
                <ul class="crumb wow fadeInUp">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    @if (isset($parentRoute))
                    <li><a href="{{ $parentRoute }}">{{ $parentTitle ?? '' }}</a></li>
                    @endif
                    <li class="active">{{ $title }}</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="gradient-edge-bottom op-7 h-80"></div>
    <div class="sw-overlay op-7"></div>
</section>
