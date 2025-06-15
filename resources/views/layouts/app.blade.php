<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Apple Pay Domain Verification -->
    <meta name="apple-pay-domain" content="{{ config('app.url') }}">
    
    <!-- Content Security Policy -->
    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self';
        script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.paypal.com https://cdn.jsdelivr.net https://js.stripe.com;
        style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com;
        font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com;
        img-src 'self' data: https:;
        frame-src 'self' https://www.paypal.com https://checkout.stripe.com https://js.stripe.com;
        connect-src 'self' https://api.paypal.com https://q.stripe.com;
    ">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .card {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        /* Removed specific font-size for i to allow fa-lg to apply */
        /* .nav-link i { 
            font-size: 1.1em;
        } */
        #paypal-button-container {
            min-width: 200px;
        }
        .nav-link .badge {
            position: absolute;
            top: 0;
            right: -5px; /* Adjust as needed */
            transform: translate(50%, -50%);
            font-size: 0.7em;
            padding: 0.3em 0.6em;
            border-radius: 50%;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="fas fa-pokemon-ball"></i> {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home') }}">
                                    <i class="fas fa-home"></i> Home
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('myfavoritesubject') }}">
                                    <i class="fas fa-heart"></i> My Pokemon
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('map') }}">
                                    <i class="fas fa-map-marked-alt"></i> Map
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('shop') }}">
                                    <i class="fas fa-shopping-cart"></i> Shop
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('weather.form') }}">
                                    <i class="fas fa-cloud-sun"></i> Weather
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('blog.index') }}">
                                    <i class="fas fa-blog"></i> Blog
                                </a>
                            </li>
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        @auth
                            <li class="nav-item position-relative me-3 w-full">
                                <a class="nav-link text-primary" href="{{ route('cart') }}" style="padding-right: 1.5rem !important;">
                                    <i class="fas fa-shopping-cart fa-2x"></i>
                                    @if(count(session('cart', [])) > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6em; padding: 0.4em 0.7em; top: 5px; right: -18px;">
                                            {{ array_sum(array_column(session('cart'), 'quantity')) }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-link nav-link">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                </form>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
