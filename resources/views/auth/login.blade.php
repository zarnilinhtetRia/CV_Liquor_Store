{{-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi Ba Yate</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('login_page/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('login_page/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('login_page/dist/css/adminlte.min.css') }}">
</head>
<style>
    .logo-size {
        width: 10vw;
        height: auto;
    }
</style>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center" style="background-color: #6CB4DF">
                <img class="d-flex mx-auto logo-size" src="{{ asset('img/navlogo.png') }}" alt="">
                <a href="{{ url('/') }}" class="h1"><b></b></a>
            </div>

            <div class="card-body">
                <p class="login-box-msg">Sign in to start</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="input-group mb-3">
                        <input id="email" type="email" class="form-control" name="email" :value="old('email')"
                            required autofocus autocomplete="username" placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror

                    <div class="input-group mb-3">
                        <input id="password" type="password" class="form-control" name="password" required
                            autocomplete="current-password" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror

                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input id="remember_me" type="checkbox" name="remember">
                                <label for="remember_me">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                    </div>
                </form>

                <div class="social-auth-links text-center mt-3s mb-3">
                    &nbsp;
                </div>


            </div>
        </div>
    </div>

    <script src="{{ asset('login_page/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('login_page/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('login_page/dist/js/adminlte.min.js') }}"></script>
</body>

</html> --}}





<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>POS CodeVerse | Point of sales</title>

    <!-- Favicon -->

    <link rel="shortcut icon" href="{{ asset('assets/images/coveverse.jpg') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/backend-plugin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/@fortawesome/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/remixicon/fonts/remixicon.css') }}">


</head>
<style>
    body {
        background-image: url('{{ asset('img/bg_logo.jpg') }}');
        /* Change the path to your image */
        background-position: center center;
        /* Adjusts the positioning of the image */
        background-repeat: no-repeat;
        /* Prevents the image from repeating */
        background-size: cover;
        /* Ensures the image covers the entire background */
        background-attachment: fixed;
        /* Keeps the background fixed when scrolling */
        min-height: 100vh;
        /* Ensures that the body takes up the full viewport height */
    }
</style>

<body class="">
    <!-- loader Start -->
    {{-- <div id="loading">
        <div id="loading-center">
        </div>
    </div> --}}
    <!-- loader END -->

    <div class="wrapper">
        <section class="login-content">
            <div class="container">
                <div class="row align-items-center justify-content-center height-self-center">
                    <div class="col-lg-8">
                        <div class="card auth-card">
                            <div class="card-body p-0">
                                <div class="d-flex align-items-center auth-content">
                                    <div class="col-lg-7 align-self-center">
                                        <div class="p-3">
                                            <h2 class="mb-2">Sign In</h2>
                                            <p>Login to stay connected.</p>
                                            <form method="POST" action="{{ route('login') }}">
                                                @csrf

                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="floating-label form-group">
                                                            <input class="floating-input form-control" type="email"
                                                                placeholder=" " name="email">
                                                            <label>Email</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="floating-label form-group">
                                                            <input class="floating-input form-control" type="password"
                                                                placeholder=" " name="password">
                                                            <label>Password</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="custom-control custom-checkbox mb-3">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="customCheck1">
                                                            <label class="custom-control-label control-label-1"
                                                                for="customCheck1">Remember Me</label>
                                                        </div>
                                                    </div>

                                                </div>
                                                <button type="submit" class="btn text-white"
                                                    style="background-color: #CB0101 !important">Sign In</button>

                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 content-right">
                                        {{-- <img src="{{ asset('assets/images/login/01.png') }}"
                                            class="img-fluid image-right" alt=""> --}}
                                        <img src="{{ asset('img/solar_logo.jpg') }}" class="img-fluid image-right"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Backend Bundle JavaScript -->
    <script src="{{ asset('assets/js/backend-bundle.min.js') }}"></script>

    <!-- Table Treeview JavaScript -->

    <script src="{{ asset('assets/js/table-treeview.js') }}"></script>


    <!-- Chart Custom JavaScript -->

    <script src="{{ asset('assets/js/customizer.js') }}"></script>

    <!-- Chart Custom JavaScript -->
    <script async src="{{ asset('assets/js/chart-custom.js') }}"></script>



    <!-- app JavaScript -->

    <script src="{{ asset('assets/js/app.js') }}"></script>

</body>

</html>
