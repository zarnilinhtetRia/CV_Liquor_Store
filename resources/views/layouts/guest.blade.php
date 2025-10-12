<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SSE Point of Sale</title>

    <link rel="shortcut icon" href="{{ asset('img/logo.jpg') }}" type="image/x-icon">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="{{ asset('locallink/css/font_bunny.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('locallink/css/google_font_source_sans_pro.css') }}">
    <!-- Font Awesome -->


    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->

    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">

    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">

    <!-- Scripts -->

</head>
<style>
    body {
        background-image: linear-gradient(#0047AA, #A8F0FD);
        background-size: cover;
        background-repeat: no-repeat;

        margin: 0;

        height: 70vh;

    }
</style>

<body class="">
    <div class="">
        <div>
            <a href="/">
                {{-- <img src="{{ asset('img/navlogo.png') }}" alt="" width="130px"> --}}
            </a>
        </div>
        <div class="container">
            <div class="p-2 text-center" style="margin-top: 13%">
                <img src="{{ asset('img/navlogo.png') }}" alt="Login Image" width="200px">
            </div>
            <div class="row justify-content-center">
                <div class="mx-auto my-auto card col-md-5" style="border-radius: 10px">

                    <div class="p-2">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>

</html>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
<script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('plugins/jquery/jquery.min.js ') }}"></script>
