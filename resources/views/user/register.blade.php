@extends('layouts.frontend')

@section('content')
<style>
    .alert {
	text-align: center;
	padding: 5px;
	margin: 0;
}
.alert ul li {
	
	list-style: none;
	padding: 0px;
}
.alert ul {
	padding: 8px;
	margin: 0;
}
</style>
<div class="container-fluid vh-100" style="padding:0;">
  <div class="row h-100">
            <div class="col-md-3 p-0">
                <div class="video-container h-100">
                    <video class="video-background" autoplay muted loop>
                        <source src="{{ asset('assets') }}/images/LoginFormVideo.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <div class="video-overlay"></div>
                </div>
            </div>
            <div class="col-md-9 d-flex justify-content-center align-items-center flex-column">
                <div class="register-form-container">
                    <div class="d-flex justify-content-center py-4">
                        <img src="{{ asset('assets') }}/images/SeeJobRunLogo.png" alt="SeeJobRun" height="100" width="100">
                    </div>
                    <h3 class="login-text-color mb-3">Create your Account</h3>
              
					@if ($errors->any())
					<div class="alert alert-danger">
						<ul>
							@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
					@endif
					@if(session('success'))
					<div class="alert alert-success">{{session('success')}}</div>
					@endif
					@if(session('error'))
					   <div class="alert alert-danger">
						   {{ session('error') }}
					   </div>
				   @endif

                     <form method="POST" action="{{ route('postregistration') }}" enctype="multipart/form-data">
						@csrf
						<div class="form-step form-step-active" id="step1">
							<div class="form-group mb-4">
							 <input id="name" type="hidden" class="form-control" placeholder="Enter your full name*" name="devicetype" value="web">
							 <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Enter your full name*" name="name" value="{{ old('name') }}" required>

									@error('name')
										<span class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
							</div>
							<div class="form-group mb-4">
								<input id="mobile" type="text" class="form-control @error('mobile') is-invalid @enderror" placeholder="Enter your phone number*" name="mobile" value="{{ old('mobile') }}" required>

									@error('mobile')
										<span class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
							</div>
							<div class="form-group mb-4">
								<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter your email address*" name="email" value="{{ old('email') }}" required>

									@error('email')
										<span class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
							</div>	
							{{-- <div class="form-group mb-4">
								<input id="confirmemail" type="email" class="form-control @error('confirmemail') is-invalid @enderror" placeholder="Confirm your email address*" name="confirmemail" value="{{ old('confirmemail') }}" required>

									@error('confirmemail')
										<span class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
							</div> --}}
							<div class="form-group mb-4">
								<input id="password" placeholder="Create 4 digit password*" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
								<span id="password-error" class="text-danger" style="display: none;"></span>
								@error('password')
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
								@enderror
							</div>
							{{-- <div class="form-group mb-4">
								 <input id="password-confirm" placeholder="Confirm Password" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required>
								@error('password_confirmation')
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
								@enderror
							</div> --}}

							<div class="d-grid gap-2 col-12 mx-auto">
								<button type="button" onclick="nextStep()" class="btn  btn-style">Next</button>
							</div>
						</div>
						<div class="form-step" id="step2">
						
							<div class="form-group mb-4">
								<input id="business_name" type="text" class="form-control @error('business_name') is-invalid @enderror" placeholder="Enter your business name" name="business_name" value="{{ old('business_name') }}">

									@error('business_name')
										<span class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
							</div>
							<div class="form-group mb-4">
								<div class="custom-select-wrapper">
									<select name="timezone" value="" class="form-control custom-select" required>
										<option value="">Select time zone*</option>
										<option value="America/Denver">Mountain time</option>
										<option value="America/Los_Angeles">Pacific time</option>
										<option value="America/Chicago">Central time</option>
										<option value="America/New_York">Eastern time</option>
									</select>
								</div>
									@error('timezone')
										<span class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
							</div>
							<div class="form-group mb-4">
								<input id="license_no" type="text" class="form-control @error('license_no') is-invalid @enderror" placeholder="Enter your license number" name="license_no" value="{{ old('license_no') }}">

									@error('license_no')
										<span class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
							</div>
							<div class="form-group mb-4">
								<input id="address" type="text" class="form-control @error('address') is-invalid @enderror" placeholder="Enter your street address*" name="address" value="{{ old('address') }}" required>

									@error('address')
										<span class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
							</div>
							<div class="form-group mb-4">
								<input id="city" type="text" class="form-control @error('city') is-invalid @enderror" placeholder="Town/City" name="city" value="{{ old('city') }}">

									@error('city')
										<span class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
							</div>
							
							<div class="row">
								<div class="col-md-6">
									<div class="form-group mb-4">
										<input id="state" type="text" class="form-control @error('state') is-invalid @enderror" placeholder="State" name="state" value="{{ old('state') }}">

										@error('state')
											<span class="invalid-feedback" role="alert">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group mb-4">
										<input id="pincode" type="text" class="form-control @error('pincode') is-invalid @enderror" placeholder="Zipcode" name="pincode" value="{{ old('pincode') }}">

											@error('pincode')
												<span class="invalid-feedback" role="alert">
													<strong>{{ $message }}</strong>
												</span>
											@enderror
									</div>
								</div>
							</div>
							
							<div class="form-group mb-4">
								<button type="button" onclick="prevStep()" class="btn  btn-style" style="margin-right:10px">Previous</button>
								<button type="submit" class="btn  btn-style">Submit</button>
							</div>
						</div>
                        <div class="mt-3 ">
                           Already have an account? <a href="{{ route('user-login') }}" class="fw-bold text-decoration-none">Login</a>
                        </div>
                    </form>
					
                </div>
				<footer class="mt-auto py-2 w-100 text-center">
					<small class="text-muted">©See Job Run {{ date('Y') }}. All rights reserved.</small>
				</footer>
            </div>
        </div>
</div>		
<script>
	let currentStep = 1;

	function nextStep() {
		const passwordInput = document.querySelector('#password');
			const passwordError = document.querySelector('#password-error');

			// Check if the password is exactly 4 digits
			if (!/^\d{4}$/.test(passwordInput.value)) {
				passwordError.textContent = 'Password must be exactly 4 digits.';
				passwordError.style.display = 'block'; // Show the error message
				passwordInput.classList.add('is-invalid'); // Add a Bootstrap invalid class if desired
				return; // Stop the function if the password is not 4 digits
			} else {
				passwordError.textContent = ''; // Clear the error message if the password is valid
				passwordError.style.display = 'none'; // Hide the error message
				passwordInput.classList.remove('is-invalid'); // Remove the invalid class if it was added
			}
		document.getElementById('step' + currentStep).classList.remove('form-step-active');
		currentStep++;
		document.getElementById('step' + currentStep).classList.add('form-step-active');
	}

	function prevStep() {
		
		document.getElementById('step' + currentStep).classList.remove('form-step-active');
		currentStep--;
		document.getElementById('step' + currentStep).classList.add('form-step-active');
		
	}

	
</script>

@endsection
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

@section('script')
<script>
	const input = document.querySelector("#mobile");
	const iti = window.intlTelInput(input, {
	  initialCountry: "us", // Set the initial country to India
	  separateDialCode: true, // If you want the dial code visually separated
	  strictMode: true
	});
  
	// Modify the form before submission to include the full phone number
	document.querySelector('form').addEventListener('submit', function (event) {
		event.preventDefault(); // Prevent the form from submitting

		// Get the full number including the country code
		const fullNumber = iti.getNumber();

		// Set the full number to a hidden input field or update the mobile input value
		document.querySelector("#mobile").value = fullNumber;

		// Now submit the form
		this.submit();
	});
 </script>
@stop