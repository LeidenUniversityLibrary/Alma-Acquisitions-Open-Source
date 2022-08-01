<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async
            src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.tracking_code') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', '{{ config('services.google_analytics.tracking_code') }}');
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
@isset($acquisitionListTitle)
    @if($acquisitionListTitle === 'latest')
        <!-- Primary Meta Tags -->
            <title>{{config('app.name', 'Alma Acquisitions - Open Source')}}</title>
            <meta name="title" content="{{config('app.name')}} ">
            <meta name="description" content="{{config('app.description')}}">
            <meta name="keywords" content="{{config('app.keywords')}}">

            <!-- Open Graph / Facebook -->
            <meta property="og:type" content="website">
            <meta property="og:url" content="{{URL::current()}}">
            <meta property="og:title" content="{{config('app.name')}}">
            <meta property="og:description" content="{{config('app.description')}}">
            <meta property="og:image" content="{{asset('img/sm_card.png')}}">

            <!-- Twitter -->
            <meta property="twitter:card" content="summary_large_image">
            <meta property="twitter:url" content="{{URL::current()}}">
            <meta property="twitter:title" content="{{config('app.name')}}">
            <meta property="twitter:description" content="{{config('app.description')}}">
            <meta property="twitter:image" content="{{asset('img/sm_card.png')}}">
    @else
        <!-- Primary Meta Tags -->
            <title>{{config('app.name', 'Alma Acquisitions - Open Source')}}
                - {{Str::title($acquisitionListTitle)}}</title>
            <meta name="title" content="{{config('app.name')}} - {{Str::title($acquisitionListTitle)}}">
            <meta name="description" content="{{config('app.description')}} - {{Str::title($acquisitionListTitle)}}">
            <meta name="keywords" content="{{Str::title($acquisitionListTitle)}}, {{config('app.keywords')}}">

            <!-- Open Graph / Facebook -->
            <meta property="og:type" content="website">
            <meta property="og:url" content="{{URL::current()}}">
            <meta property="og:title" content="{{config('app.name')}} - {{Str::title($acquisitionListTitle)}}">
            <meta property="og:description"
                  content="{{config('app.description')}} - {{Str::title($acquisitionListTitle)}}">
            <meta property="og:image" content="{{asset('img/sm_card.png')}}">

            <!-- Twitter -->
            <meta property="twitter:card" content="summary_large_image">
            <meta property="twitter:url" content="{{URL::current()}}">
            <meta property="twitter:title" content="{{config('app.name')}} - {{Str::title($acquisitionListTitle)}}">
            <meta property="twitter:description"
                  content="{{config('app.description')}} - {{Str::title($acquisitionListTitle)}}">
            <meta property="twitter:image" content="{{asset('img/sm_card.png')}}">
    @endif
@else
    <!-- Primary Meta Tags -->
        <title>{{config('app.name', 'Alma Acquisitions - Open Source')}}</title>
        <meta name="title" content="{{config('app.name')}} ">
        <meta name="description" content="{{config('app.description')}}">
        <meta name="keywords" content="{{config('app.keywords')}}">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{URL::current()}}">
        <meta property="og:title" content="{{config('app.name')}}">
        <meta property="og:description" content="{{config('app.description')}}">
        <meta property="og:image" content="{{asset('img/sm_card.png')}}">

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{URL::current()}}">
        <meta property="twitter:title" content="{{config('app.name')}}">
        <meta property="twitter:description" content="{{config('app.description')}}">
        <meta property="twitter:image" content="{{asset('img/sm_card.png')}}">
    @endisset
    <meta name="robots" content="index, follow">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="language" content="English">
    <meta name="author" content="My Institution">
    <link rel="canonical" href={{ URL::current() }}>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon-196x196.png') }}" sizes="196x196"/>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon-96x96.png') }}" sizes="96x96"/>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon-32x32.png') }}" sizes="32x32"/>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon-16x16.png') }}" sizes="16x16"/>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon-128.png') }}" sizes="128x128"/>
    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Styles --}}
    <link rel="stylesheet" href={{ asset('css/app.css') }}>
    @yield('css')
</head>
<body>
<div id="content">
    {{-- SECTION Header container --}}
    <div class="header-container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container shadow-sm">
                <a class="navbar-brand" href="https://example.com">
                    <img src="{{ asset('img/logo.png') }}" width="151" height="64" alt="My Institution Logo"
                         id="logo_header">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
                    <ul class="nav navbar-nav ml-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="/">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                               href="https://example.com/about-us/contact/acquisition-requests"
                               target="_blank"
                               rel="noopener">Request an acquisition <sup><i class="bi bi-box-arrow-up-right"></i></sup></a>
                        </li>
                        <li class="nav-item">
                            <a href="https://example.com/about-us/contact/subject-librarians"
                               target="_blank"
                               class="nav-link">Contact a Subject Librarian <sup><i
                                        class="bi bi-box-arrow-up-right"></i></sup></a>
                        </li>
                        <li class="nav-item">
                            <a href="https://example.com/"
                               target="_blank"
                               class="nav-link">Ask-a-Librarian <sup><i class="bi bi-box-arrow-up-right"></i></sup></a>
                        </li>
                        @auth
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                                   aria-expanded="false">Admin</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{route('home')}}">Manage lists</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                              style="display: none;">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </li>

                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
        <div class="border-bottom border-library"></div>
    </div>
    <div class="main-container">
        <div class="container shadow-sm p-3 mt-3 rounded">
            <h1 class="text-center mt-4">
                @isset ($acquisitionsListName)
                    Acquisitions - New books at the library

                @else
                    {{ config('app.name', 'Alma Acquisitions - Open Source') }}

                @endisset
            </h1>
            <h5 class="text-muted text-center">Lists of the latest acquisitions by My Institution
                (UBL)</h5>
            @isset($acquisitionsListName)
                <h2 class="text-center display-4 my-4">
                    {{$acquisitionsListName}}
                </h2>
            @endisset
            @auth

                <div class="alert alert-primary text-center" role="alert">
                    <strong>You are logged in as an administrator.</strong>
                </div>
            @endauth
            <main class="container py-4">
                {{-- SECTION Yield Content --}}
                @yield('content')
                {{-- SECTION End Yield Content --}}
            </main>
        </div>
    </div>
    {{-- SECTION Footer container --}}
    <div class="footer-container">
        <div class="container-lg">
            <footer class="mt-5 pt-md-5 border-top col-12">
                <div class="row">
                    <div class="col-12 col-lg-4 mb-5">
                        <a href="https://example.com/" class="text-gray-dark">
                            <img src="{{{ asset('img/logo.png') }}}" alt="My Institution Logo" id="logo_footer"
                                 style="max-width:50%; display: block; margin:0 auto;"></a>
                    </div>
                    <div class="col-6 col-md">
                        <h5>My Institution</h5>
                        <ul class="list-unstyled text-small">
                            <li><a href="#" target="_blank" rel="noopener">My Institution Website</a></li>
                            <li><a href="#" target="_blank"
                                   rel="noopener">My Institution Website</a></li>
                        </ul>
                    </div>
                    <div class="col-6 col-md">
                        <h5>Social</h5>
                        <ul class="list-unstyled text-small">
                            <li><a href="#" target="_blank" rel="noopener">My Institution
                                    on Facebook</a></li>
                            <li><a href="#" target="_blank" rel="noopener">My Institution
                                    on Twitter</a></li>
                        </ul>
                    </div>
                    <div class="col-6 col-md">
                        <h5>About</h5>
                        <ul class="list-unstyled text-small">
                            <li>
                                <p class="text-muted">Designed and developed by <a
                                        href="https://github.com/LeidenUniversityLibrary" target="_blank"
                                        rel="noopener">My Institution</a>.
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</div>
<script src="{{ asset('js/app.js') }}"></script>
@yield('javascript')
</body>
</html>
