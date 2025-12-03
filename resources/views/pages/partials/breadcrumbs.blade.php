<!-- Breadcrumb Section Start -->
<div class="breadcrumb-section">
    <div class="container">
        <div class="breadcrumb-content pb-60">
            <h1>{{ $title }}</h1>
            <ul class="breadcrumb-list">
                <li>
                    <a href="{{ url('/') }}">Home</a>
                    <svg width="13" height="13" viewBox="0 0 13 13" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path
                                d="M11.0636 6.58864L8.00264 12.8892C7.96567 12.9557 7.89913 13 7.8178 13H2.11415C1.96628 13 1.87017 12.8449 1.92931 12.7119L4.90894 6.58864C4.93851 6.52955 4.93851 6.47046 4.90894 6.41136L1.9441 0.288068C1.87756 0.155114 1.97368 0 2.12894 0H7.83259C7.90652 0 7.98046 0.0443182 8.01743 0.110795L11.0784 6.41136C11.0932 6.47046 11.0932 6.52955 11.0636 6.58864Z" />
                        </g>
                    </svg>
                </li>
                <li>{{ $title }}</li>
            </ul>
        </div>
    </div>
    <div class="breadcrumb-img">
        <img src="{{ isset($bannerBg) ? asset($bannerBg) : asset('assets/img/innerpages/industries-details-breadcrumb-bg-img.jpg') }}"
            alt="">
    </div>
</div>
<!-- Breadcrumb Section End -->
