<!DOCTYPE html>
<html lang="en">

<head>
    <!-- required meta -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta property="og:title" content="{{ config('app.name') }} {{ isset($title) ? " - ".$title  : "" }}">
    <meta property="og:url" content="https://bmtechechnologiesgh.com">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="en-us">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:image" content="{{ asset('assets/images/logo-1.png') }}">
    <meta property="og:image:url" content="{{ asset('assets/images/logo-1.png') }}">
    <!-- #favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/x-icon">
    <!-- #title -->
    <title> {{config('app.name')}} {{isset($title) ? " - ". $title : "" }}</title>

    <!-- #keywords -->
    <meta name="keywords" content="#">
    <!-- #description -->
    <meta name="description" content="{{isset($description) ? $description : ""}}">
    <!-- google fonts -->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- CSS Files
    ================================================== -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" id="bootstrap">
    <link href="{{ asset('assets/css/plugins.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/swiper.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/swiper-custom-1.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/coloring.css') }}" rel="stylesheet" type="text/css">
    <!-- color scheme -->
    <link id="colors" href="{{ asset('assets/css/colors/scheme-01.css') }}" rel="stylesheet" type="text/css">
</head>

<body>
    <div id="wrapper">
        <a href="#" id="back-to-top"></a>

        <!-- page preloader begin -->
        <div id="de-loader"></div>
        <!-- page preloader close -->
