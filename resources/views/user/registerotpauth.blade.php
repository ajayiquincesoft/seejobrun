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
                <div class="text-center">
                    <h3 class="login-text-color">Enter Code Sent to Your Email or Phone</h3>
                    <p class="font-size-14 fw-bold">Verify Code sent to your Email or Phone</p>
                </div>
               
               
              <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
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
                 </div>
              </div>
                <form method="POST" action="{{ route('Verifyregisterotp') }}" onsubmit="collectOTPValues()">
                    @csrf
                    <div class="form-group mb-4">
                        <div class="otp-section mt-4">
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="col-md-1">
                                    <input type="text" maxlength="1" class="form-control otp-digit"  oninput="moveToNext(this, event)" required>
                                </div>
                                <div class="col-md-1">
                                    <input type="text" maxlength="1" class="form-control otp-digit"  oninput="moveToNext(this, event)" required>
                                </div>
                                <div class="col-md-1">
                                    <input type="text" maxlength="1" class="form-control otp-digit"  oninput="moveToNext(this, event)" required>
                                </div>
                                <div class="col-md-1">
                                    <input type="text" maxlength="1" class="form-control otp-digit"  oninput="moveToNext(this, event)" required>
                                </div>
                            </div>
                            <input type="hidden" id="otp-full" name="registerotp" value="">
                            <span class="invalid-feedback" role="alert" id="otp-error"></span>

                        </div>

                       
                    </div>
                    <div class="row">
                        <div class="col-md-6 text-end">
                            <a href="{{ route('register') }}"><button type="button" class="btn  btn-style "
                                style="padding: 6px 16px;">Back</button></a>
                        </div>
                        <div class="col-md-6 ">
                            <button type="submit" class="btn  btn-style">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

@section('script')

<script>
    function collectOTPValues() {
        let otpDigits = document.querySelectorAll('.otp-digit');
        let otpValue = '';

        otpDigits.forEach(function(digit) {
            otpValue += digit.value;
        });

        document.getElementById('otp-full').value = otpValue;
    }
    function moveToNext(currentInput, event) {
        if (currentInput.value.length === 1) {
            let nextInput = currentInput.nextElementSibling;
            if (nextInput && nextInput.classList.contains('otp-digit')) {
                nextInput.focus();
            }
        }
    }
</script>
@stop
   
