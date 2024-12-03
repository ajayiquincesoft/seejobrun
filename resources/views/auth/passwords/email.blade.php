@extends('layouts.admin')

@section('content')
<div class="container mt--8 pb-5" style="margin-top: -18% !important;">
    <div class="row">
        <div class="col-md-5" style="background-color:#e2e3eb">
            <!-- <img src="admin_assets/img/bglogin.jpg" alt="login" class="login-card-img"> -->
            <img src="{{asset('/Logo-01.png')}}" alt="login" class="login-card-img">
        </div>

        <div class="col-md-7" style="background-color:white;">
            <div class="card" style="margin-top:20%; margin-bottom: 20%; box-shadow: none;">
                <div class="card-header"><b>{{ __('Reset Password') }}</b></div>

                <div class="card-body">
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

                    <form method="POST" action="{{ route('forget.password.post') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection