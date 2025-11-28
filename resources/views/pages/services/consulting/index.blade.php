@extends('layouts.landing.main')

@section('content')
@include('pages.partials.breadcrumbs-sm', ['parentRoute' => route('services'), 'parentTitle' => 'Services', 'title' =>
'Strategic Business Consulting',
'bannerBg' =>
'assets/images/background/1.jpg'])


@endsection
