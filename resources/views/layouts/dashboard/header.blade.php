<!doctype html>
<html lang="en" class="dashboard-html">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta property="og:title" content="{{ config('app.name') }} {{ isset($title) ? " - ".$title  : "" }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="en-us">
    <meta property="og:site_name" content="{{ config('app.name') }}">

    <meta name="description" content="{{ isset($description) ? $description : '' }}">

    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/jquery-ui.css') }}" rel="stylesheet">
    <!-- Bootstrap Icon CSS -->
    <link href="{{ asset('assets/css/bootstrap-icons.css') }}" rel="stylesheet">
    <!-- CSS -->
    <link href="{{ asset('assets/css/animate.min.css') }}" rel="stylesheet">
    <!-- FancyBox CSS -->
    <link href="{{ asset('assets/css/jquery.fancybox.min.css') }}" rel="stylesheet">
    <!-- Nice CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/nice-select.css') }}">
    <!-- Swiper slider CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/swiper-bundle.min.css') }}">
    <!-- Slick slider CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/slick-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}">
    <!-- BoxIcon  CSS -->
    <link href="{{ asset('assets/css/boxicons.min.css') }}" rel="stylesheet">
    <!--  Style CSS  -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <!-- Dashboard CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    <!-- Select Search Component CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/select-search.css') }}">
    <!-- Autocomplete Component CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/autocomplete.css') }}">
    <!-- Toaster CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/toaster.css') }}">
    <!-- Title -->
    <title> {{config('app.name')}} {{isset($title) ? " - ". $title : "" }}</title>
    <link rel="icon" href="{{ asset('assets/favicon.png') }}" type="image/gif" sizes="20x20">
    <style>
        html,
        body {
            overflow-x: hidden;
            max-width: 100%;
        }
    </style>

    @stack('styles')