<!doctype html>
<html lang="en">

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
    <meta property="og:image" content="{{ asset('assets/fav-icon.svg') }}">
    <meta property="og:image:url" content="{{ asset('assets/fav-icon.svg') }}">
    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/jquery-ui.css') }}" rel="stylesheet">
    <!-- Bootstrap Icon CSS -->
    <link href="{{ asset('assets/css/bootstrap-icons.css') }}" rel="stylesheet">
    <!-- CSS -->
    <link href="{{ asset('assets/css/animate.min.css') }}" rel="stylesheet">
    <!-- FancyBox CSS -->
    <link href="{{ asset('assets/css/jquery.fancybox.min.css') }}" rel="stylesheet">
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
    <!-- Title -->
    <title> {{config('app.name')}} {{isset($title) ? " - ". $title : "" }}</title>
    <link rel="icon" href="{{ asset('assets/fav-icon.svg') }}" type="image/gif" sizes="20x20">

    <link rel="stylesheet" id="silktide-consent-manager-css"
        href="{{ asset('assets/plugins/cookies/silktide-consent-manager.css') }}">
    <script src="{{ asset('assets/plugins/cookies/silktide-consent-manager.js') }}"></script>

    <script>
        silktideCookieBannerManager.updateCookieBannerConfig({
      background: {
        showBackground: true
      },
      cookieIcon: {
        position: "bottomLeft"
      },
      cookieTypes: [
        {
          id: "necessary",
          name: "Necessary",
          description: "<p>These cookies are necessary for the website to function properly and cannot be switched off. They help with things like logging in and setting your privacy preferences.</p>",
          required: true,
          onAccept: function() {
            console.log('Add logic for the required Necessary here');
          }
        },
        {
          id: "analytics",
          name: "Analytics",
          description: "<p>These cookies help us improve the site by tracking which pages are most popular and how visitors move around the site.</p>",
          required: false,
          onAccept: function() {
            gtag('consent', 'update', {
              analytics_storage: 'granted',
            });
            dataLayer.push({
              'event': 'consent_accepted_analytics',
            });
          },
          onReject: function() {
            gtag('consent', 'update', {
              analytics_storage: 'denied',
            });
          }
        },
        {
          id: "advertising",
          name: "Advertising",
          description: "<p>These cookies provide extra features and personalization to improve your experience. They may be set by us or by partners whose services we use.</p>",
          required: false,
          onAccept: function() {
            gtag('consent', 'update', {
              ad_storage: 'granted',
              ad_user_data: 'granted',
              ad_personalization: 'granted',
            });
            dataLayer.push({
              'event': 'consent_accepted_advertising',
            });
          },
          onReject: function() {
            gtag('consent', 'update', {
              ad_storage: 'denied',
              ad_user_data: 'denied',
              ad_personalization: 'denied',
            });
          }
        }
      ],
      text: {
        banner: {
          description: "<p>We use cookies on our site to enhance your user experience, provide personalized content, and analyze our traffic. <a href=\"https://your-website.com/cookie-policy\" target=\"_blank\">Cookie Policy.</a></p>",
          acceptAllButtonText: "Accept all",
          acceptAllButtonAccessibleLabel: "Accept all cookies",
          rejectNonEssentialButtonText: "Reject non-essential",
          rejectNonEssentialButtonAccessibleLabel: "Reject non-essential",
          preferencesButtonText: "Preferences",
          preferencesButtonAccessibleLabel: "Toggle preferences"
        },
        preferences: {
          title: "Customize your cookie preferences",
          description: "<p>We respect your right to privacy. You can choose not to allow some types of cookies. Your cookie preferences will apply across our website.</p>",
          creditLinkText: "Get this banner for free",
          creditLinkAccessibleLabel: "Get this banner for free"
        }
      },
      position: {
        banner: "bottomLeft"
      }
    });
    </script>
</head>

<body class="tt-magic-cursor transport-home">

    <div id="magic-cursor">
        <div id="ball"></div>
    </div>

    <!-- Back To Top -->
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
        <svg class="arrow" width="22" height="25" viewBox="0 0 24 23" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M0.556131 11.4439L11.8139 0.186067L13.9214 2.29352L13.9422 20.6852L9.70638 20.7061L9.76793 8.22168L3.6064 14.4941L0.556131 11.4439Z" />
            <path d="M23.1276 11.4999L16.0288 4.40105L15.9991 10.4203L20.1031 14.5243L23.1276 11.4999Z" />
        </svg>
    </div>
