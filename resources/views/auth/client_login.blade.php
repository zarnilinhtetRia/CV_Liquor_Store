<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cinderella</title>
    <link rel="icon" href="{{ asset('img/cinderella.png') }}" type="image/x-icon">

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

    body {
        /* background: linear-gradient(45deg, #ff9a9e, #ff6a88, #ff99ac); */
        background-color: pink !important;
    }

    /* .card {
        background: #B1DEF5;
    } */
</style>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="card">
            <div class="card-header text-center">
                <img class="d-flex mx-auto logo-size" src="{{ asset('img/cinderella.png') }}" alt="">
                <a href="{{ url('/') }}" class="h1"><b></b></a>
            </div>


            <div class="card-body">
                @if (session('error'))
                    <div class="text-danger text-center" style="font-weight: bold;"> {{ session('error') }}</div>
                @endif
                <p class="login-box-msg">Sign in to start</p>

                <form method="POST" action="{{ route('clients.login') }}">
                    @csrf

                    <div class="input-group mb-3">
                        <input id="phone_number" type="text" class="form-control" name="phone_number"
                            value="{{ old('phone_number') }}" required autofocus placeholder="Phone Number">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-phone"></span>
                            </div>
                        </div>
                    </div>
                    @error('phone_number')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror

                    <div class="input-group mb-3">
                        <input id="password" type="password" class="form-control" name="password" required
                            placeholder="Password">
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
                                <input type="checkbox" name="remember" id="remember_me">
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

                <!-- Beautiful Create Account Link -->


                <div class="text-center mt-4">
                    <p> Don't have an account?<a href="{{ url('register') }}" class="btn btn-link">Create an
                            Account</a>
                    </p>
                </div>
            </div>

        </div>
    </div>

    <script src="{{ asset('login_page/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('login_page/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('login_page/dist/js/adminlte.min.js') }}"></script>
</body>

</html>
