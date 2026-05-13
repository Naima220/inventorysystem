<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MineMart_Management_System - Login</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Icon -->
    <link rel="stylesheet" href="{{ global_asset('frontend') }}/fonts/material-icon/css/material-design-iconic-font.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ global_asset('frontend') }}/css/style.css">
</head>
<body>
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">

        <!-- Image -->
        <div class="col-lg-4 d-none d-md-block text-center">
            <figure>
                <img src="{{ global_asset('frontend') }}/images/signin-image.jpg" alt="Sign in Image" class="img-fluid">
            </figure>
        </div>

        <!-- Login Form -->
        <div class="col-12 col-sm-10 col-md-6 col-lg-4">
            <h2 class="form-title text-center mb-4">Sign In</h2>

            <form method="POST" action="{{ route('login') }}" id="login-form">
                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <input type="email"
                           name="email"
                           placeholder="Email"
                           required
                           class="form-control"/>
                </div>

                <!-- Password -->
                <div class="mb-2">
                    <input type="password"
                           name="password"
                           placeholder="Password"
                           required
                           class="form-control"/>
                </div>

               

                <!-- Button -->
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary w-100">
                        Log in
                    </button>
                </div>
            </form>

            <!-- Social -->
            <div class="text-center">
                <span class="social-label">Or login with</span>
                <ul class="list-inline mt-2">
                    <li class="list-inline-item"><a href="#"><i class="zmdi zmdi-facebook"></i></a></li>
                    <li class="list-inline-item"><a href="#"><i class="zmdi zmdi-twitter"></i></a></li>
                    <li class="list-inline-item"><a href="#"><i class="zmdi zmdi-google"></i></a></li>
                </ul>
            </div>

        </div>
    </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ global_asset('frontend') }}/vendor/jquery/jquery.min.js"></script>
<script src="{{ global_asset('frontend') }}/js/main.js"></script>
</body>
</html>
