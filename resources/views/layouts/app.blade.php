<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Absensi') }}</title>

    <link rel="icon" href="{{asset('assets/img/blm.jpg')}}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Ion Icons CDN (Standardized to v7.1.0 for consistency) -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- Original assets/css/app.css and app.js from Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Leaflet CSS and JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <!-- Custom styles (if any, consider migrating to Tailwind) -->
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}" />

    <style>
        /* Custom styles for the appBottomMenu to override default if necessary */
        .appBottomMenu .item.active {
            color: #4CAF50; /* Primary color for active state */
        }
        .appBottomMenu .action-button.large {
            background-color: #4CAF50; /* Primary color for camera button */
        }
        /* Ensure the body font is Inter as set in the head */
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen">
        @include('layouts.navigation')

        @if (isset($header))
        <header class="bg-gray-800 shadow">
            <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        @yield('header')

        <main>
            @yield('content')
        </main>

        <!-- App Bottom Menu - Styled with Tailwind CSS -->
        <div class="fixed bottom-0 left-0 right-0 z-50 flex items-center justify-around w-full px-4 py-2 bg-white rounded-t-lg shadow-lg appBottomMenu">
            <a href="{{route('absen.dashboard')}}" class="item flex-1 text-center flex flex-col items-center justify-center py-1 rounded-md transition-colors duration-200 {{request()->is('dashboard') ? 'active text-green-500' : 'text-gray-600 hover:text-green-500'}}">
                <div class="flex flex-col items-center col">
                    <ion-icon name="home-outline" class="text-2xl"></ion-icon>
                    <strong class="mt-1 text-xs font-medium">Home</strong>
                </div>
            </a>
            <a href="{{route('absen.histori')}}" class="item flex-1 text-center flex flex-col items-center justify-center py-1 rounded-md transition-colors duration-200 {{request()->is('absen/histori') ? 'active text-green-500' : 'text-gray-600 hover:text-green-500'}}">
                <div class="flex flex-col items-center col">
                    <ion-icon name="document-text-outline" class="text-2xl"></ion-icon>
                    <strong class="mt-1 text-xs font-medium">Histori</strong>
                </div>
            </a>

            {{-- Central Camera Button --}}
            {{-- Note: Complex logic for button state (disabled, different icon) should ideally be handled in the view that yields content,
                 or via JavaScript based on data passed from the backend, as layout files are generally for structural elements.
                 Here, it's simplified to always link to absen.create. --}}
            <a href="{{ route('absen.create') }}" class="flex flex-col items-center justify-center flex-1 py-1 text-center transition-colors duration-200 rounded-md item">
                <div class="flex flex-col items-center col">
                    <div class="flex items-center justify-center w-16 h-16 -mt-8 text-white bg-green-500 border-4 border-white rounded-full shadow-lg action-button large">
                        <ion-icon name="camera" class="text-3xl"></ion-icon>
                    </div>
                    <strong class="mt-1 text-xs font-medium text-gray-600">Absen</strong>
                </div>
            </a>

            <a href="{{route('absen.izin')}}" class="item flex-1 text-center flex flex-col items-center justify-center py-1 rounded-md transition-colors duration-200 {{request()->is('absen/izin') ? 'active text-green-500' : 'text-gray-600 hover:text-green-500'}}">
                <div class="flex flex-col items-center col">
                    <ion-icon name="calendar-outline" class="text-2xl"></ion-icon>
                    <strong class="mt-1 text-xs font-medium">Izin</strong>
                </div>
            </a>
            <a href="{{route('editprofile')}}" class="item flex-1 text-center flex flex-col items-center justify-center py-1 rounded-md transition-colors duration-200 {{request()->is('absen/editprofile') ? 'active text-green-500' : 'text-gray-600 hover:text-green-500'}}">
                <div class="flex flex-col items-center col">
                    <ion-icon name="people-outline" class="text-2xl"></ion-icon>
                    <strong class="mt-1 text-xs font-medium">Profile</strong>
                </div>
            </a>
        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="{{ asset('assets/js/lib/popper.min.js')}}"></script>
<script src="{{ asset('assets/js/lib/bootstrap.min.js')}}"></script>
{{-- Ion Icons v5.5.2 was removed, standardized to v7.1.0 in head --}}
<script src="{{ asset('assets/js/plugins/owl-carousel/owl.carousel.min.js') }} "></script>
<script src="{{ asset('assets/js/plugins/jquery-circle-progress/circle-progress.min.js')}}"></script>

@stack('charts_script')

<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
<script src="{{ asset('assets/js/base.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js" xintegrity="sha512-dQIiHSl2hr3NWKKLycPndtpbh5iaHLo6MwrXm7F0FM5e+kL2U16oE9uIwPHUl6fQBeCthiEuV/rzP3MiAB8Vfw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@stack('myscript')
</html>
