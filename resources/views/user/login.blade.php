@extends('layouts.frontend')

@section('content')
    <div class="container-fluid vh-100" style="padding:0;">
        <div class="row h-100">
            <!-- Left side (Video section) -->
            <div class="col-md-3 p-0">
                <div class="video-container h-100">
                    <video class="video-background" autoplay muted loop>
                        <source src="{{ asset('assets') }}/images/LoginFormVideo.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <div class="video-overlay"></div>
                </div>
            </div>

            <!-- Right side (Form section) -->
            <div class="col-md-9 d-flex justify-content-center align-items-center flex-column">
                <div class="login-form-container mb-auto">
                    <div class="d-flex justify-content-center py-4">
                        <img src="{{ asset('assets') }}/images/SeeJobRunLogo.png" alt="SeeJobRun" height="100"
                            width="100">
                    </div>
                    <h3 class="login-text-color">Log In to your Account</h3>
                    <p class="font-size-14 fw-bold">Login to your See Job Run account to start working with other
                        Contractors & Clients</p>
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (Session::has('message'))
                        <div class="alert alert-success" role="alert">
                            {{ Session::get('message') }}
                        </div>
                    @endif

                    @if (Session::has('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ Session::get('error') }}
                        </div>
                    @endif
                    @if ($errors->has('Error'))
                        <div class="alert alert-danger">
                            {{ $errors->first('Error') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('userlogin') }}">
                        @csrf
                        <div class="form-group mb-4">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                placeholder="Enter your email address *" name="email" value="{{ old('email') }}" required
                                autocomplete="email" autofocus>

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        {{-- <div class="form-group mb-3">
                            <input id="password" placeholder="Enter your password *" type="password"
                                class="form-control @error('password') is-invalid @enderror" name="password" required
                                autocomplete="current-password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div> --}}
                        <div class="form-group mb-3">
                            <div class="input-group">
                                <input id="password" 
                                       placeholder="Enter your password *" 
                                       type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword" style="margin-left: -44px;">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-right text-sm d-flex justify-content-end mb-3">
                                    @if (Route::has('password.request'))
                                        <a class="text-decoration-none" href="{{ route('forget.password.get') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif

                                </div>

                            </div>
                        </div>


                        <div class="d-grid gap-2 col-12 mx-auto">
                            <button type="submit" class="btn  btn-style">Login</button>
                        </div>
                        <div class="mt-3 ">
                            Don’t have an account? <a href="{{ route('register') }}" class="fw-bold text-decoration-none">
                                Sign Up</a>
                        </div>
                    </form>
                </div>

                <!-- Footer (Inside the form container, under the form) -->
                <footer class="mt-auto py-2 w-100 text-center">
                    <small class="text-muted">©See Job Run {{ date('Y') }}. All rights reserved.</small>
                </footer>
            </div>
        </div>
    </div>
<!-- Optional: Include Font Awesome for the eye icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection
@section('script')
<script>
    document.getElementById('togglePassword').addEventListener('click', function (e) {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

</script>
@stop