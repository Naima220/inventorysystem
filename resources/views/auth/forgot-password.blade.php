<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MineMart - Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ global_asset('frontend') }}/fonts/material-icon/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" href="{{ global_asset('frontend') }}/css/style.css">
</head>
<body>
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-lg-4 d-none d-md-block text-center">
            <figure>
                <img src="{{ global_asset('frontend') }}/images/signin-image.jpg" alt="Forgot Password Image" class="img-fluid">
            </figure>
        </div>
        <div class="col-12 col-sm-10 col-md-6 col-lg-4">
            <h2 class="form-title text-center mb-4">Reset Password</h2>
            
            <p class="text-muted text-center mb-4 small">
                Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.
            </p>

            @if (session('status'))
                <div class="alert alert-success mb-4 text-center small">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-3">
                    <input type="email" name="email" placeholder="Email Address" required class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" autofocus />
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary w-100">
                        Email Password Reset Link
                    </button>
                </div>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('login') }}" class="text-decoration-none small">Back to Login</a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
