@extends('layouts.landing.main')

@section('content')
@include('pages.partials.breadcrumbs', ['bannerBg' => 'assets/images/volunteer.jpg'])


<div class="cm-details donate-us community checkout faq">
    <div class="container">
        <div class="row gutter-60">
            <div class="col-12 col-xl-12">
                <iframe src="https://docs.google.com/forms/d/e/1FAIpQLScTFM-8sOrmJEGWs8lJdXAoKJLtaTWHkmcCRm49v_Poq-gd_g/viewform?embedded=true" width="640" height="1209" frameborder="0" marginheight="0" marginwidth="0">Loading…</iframe>
            </div>
        </div>
    </div>
</div>
@endsection