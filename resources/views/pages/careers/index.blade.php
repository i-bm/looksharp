@extends('layouts.landing.main')

@section('content')
@include('pages.partials.breadcrumbs-sm', ['title' => 'Careers'])

<div class="container">
    <div class="row">
        <div class="col-12 mt-5 mb-5">
            <h2 class="text-center">We currently have no open positions.</h2>
            <p class="text-center text-dark">However, we are always looking for talented people to join our team. If you
                are
                interested in working with us, please send us your resume and cover letter to <a
                    href="mailto:{{ config('misc.email_hr') }}" class="text-primary">{{ config('misc.email_hr') }}</a>.
        </div>
    </div>
</div>
@endsection
