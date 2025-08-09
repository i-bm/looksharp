<!DOCTYPE html>
<html lang="en">

<head>
    <!-- required meta -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta property="og:title" content="{{ config('app.name') }} {{ isset($title) ? " - ".$title  : "" }}">
    <meta property="og:url" content="https://thenexcoretech.com">
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

    <link
        href="https://fonts.googleapis.com/css2?family=Hind+Madurai:wght@300;400;500;600;700&amp;family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&amp;display=swap"
        rel="stylesheet">
    <!-- main css -->

    <!-- Vendor CSS Files -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css"
        integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{  asset('assets/vendor/bootstrap.min.css')}}">
    {{--
    <link rel="stylesheet" href="{{  asset('assets/vendor/fontawesome.min.css')}}"> --}}
    <link rel="stylesheet" href="{{  asset('assets/vendor/swiper-bundle.min.css')}}">
    <link rel="stylesheet" href="{{  asset('assets/vendor/progressbar.css')}}">
    <link rel="stylesheet" href="{{  asset('assets/vendor/meanmenu.min.css')}}">
    <link rel="stylesheet" href="{{  asset('assets/vendor/magnific-popup.css')}}">
    <link rel="stylesheet" href="{{  asset('assets/vendor/animate.min.css')}}">
    <link rel="stylesheet" href="{{  asset('assets/vendor/odometer.min.css')}}">
    <link rel="stylesheet" href="{{  asset('assets/vendor/spacing.css')}}">

    <!-- Template Main CSS File -->
    <link rel="stylesheet" href="{{  asset('assets/css/style.css')}}">

    <style>
        .header-area-7 .header__logo img {
            max-width: 210px;
        }

        .main-menu ul.dp-menu {
            width: 320px;
        }
    </style>
</head>



<body class="body-wrapper body-it-solution">
    <!--[if lt IE 9]>
      <p class="browserupgrade">
         You are using an <strong>outdated</strong> browser. Please
         <a href="https://browsehappy.com/">upgrade your browser</a> to improve
         your experience and security.
      </p>
      <![endif]-->
    <!-- Preloader -->
    <div id="preloader">
        <div id="container" class="container-preloader">
            <div class="animation-preloader">
                <div class="spinner"></div>

            </div>
            <div class="loader-section section-left"></div>
            <div class="loader-section section-right"></div>
        </div>
    </div>



    <!-- Sroll to top -->
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"></path>
        </svg>
    </div>

    <!-- cursorAnimation start -->
    <div class="cursor-wrapper relative">
        <div class="cursor"></div>
        <div class="cursor-follower"></div>
    </div>
    <!-- cursorAnimation end -->
