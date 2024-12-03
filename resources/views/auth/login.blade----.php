@extends('layouts.frontend')

@section('content')

  <div class="row">
            <div class="col-md-3 p-0">
                <div class="video-container">
                    <video class="video-background" autoplay muted loop>
                        <source src="{{ asset('assets') }}/images/LoginFormVideo.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <div class="video-overlay"></div>
                </div>
            </div>
            <div class="col-md-9 d-flex justify-content-center align-items-center">
                <div class="login-form-container">
                    <div class="d-flex justify-content-center py-4">
                        <img src="{{ asset('assets') }}/images/SeeJobRunLogo.png" alt="SeeJobRun" height="100" width="100">
                    </div>
                    <h3 class="login-text-color">Log In to your Account</h3>
                    <p class="font-size-14 fw-bold">Login in to See Job Run account to start working with other
                        Contractors & Clients</p>
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
                     <form method="POST" action="{{ route('login') }}">
						@csrf
                        <div class="form-group mb-4">
						 <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter your email address *" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus >

						@error('email')
							<span class="invalid-feedback" role="alert">
								<strong>{{ $message }}</strong>
							</span>
						@enderror
                        </div>
                        <div class="form-group mb-3">
							<input id="password" placeholder="Enter your password *" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

							@error('password')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
                        </div>
						
						<div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

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
                            Don’t have an account? <a href="" class="fw-bold text-decoration-none"> Sign Up</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

@endsection
