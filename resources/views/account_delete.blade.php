@extends('layouts.admin')

@section('content')
<style>
strong{color: #000;
font-weight: 600;
}
p{  color: #0a0a0b;
  font-size: 16px;

}
li {
	font-size: 15px;
	line-height: 28px;
}
.topcont{margin-top: -18% !important;}
 .box {
  background-color: #fff;
  border: 1px solid #000;
  color: #000;
  margin: 0 0 20px;
  padding: 7px 10px;
}
.log-btn {
	background-color: #2A70AC;
	border: 1px solid #2A70AC;
	box-shadow: 4px 4px 10px rgba(0,0,0,0.2);
	font-size: 15px;
	font-family: SF-Pro-Medium;
	padding: 11px 65px;
}
.alert.alert-danger li {
	list-style: none;
	text-align: left;
	padding: 0;
}
@media (min-width: 320px) and (max-width: 767px) {
	.topcont{margin-top: -59% !important;}
}
</style>
<div class="container mt--8 pb-5 topcont">
    <div class="row">
        <div class="col-md-12" style="background-color:#e2e3eb;text-align: center;">
            <!-- <img src="admin_assets/img/bglogin.jpg" alt="login" class="login-card-img"> -->
            <img src="{{asset('/Logo-01.png')}}" alt="privacy policy" class="login-card-img1" width="300" style="text-align: center;margin-top: -50px;margin-bottom: -50px;">
        </div>
    </div>
	 <div class="row">
        <div class="col-md-12" style="background-color:white;">
            <div class="card " style="margin-top:2%; margin-bottom: 20%; box-shadow: none;">
                <div class="card-header"><h1 style="text-align: center;">Delete your Account</h1></div>

                <div class="card-body">
				<div class="row">
				 <div class="col-lg-3 col-md-3"></div>
					<div class="col-lg-6 col-md-6">
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
						<form autocomplete="off" action="{{route('account_delete')}}" method="post" id="user-login-form" enctype="multipart/form-data">
							@csrf
							<div class="in-box">
								<h5>EMail Address*</h5> <input class="box form-control" type="email" name="email" placeholder="" required />
							</div>
							<div class="in-box mt-2">
								<h5>Enter reason for delete account </h5> <textarea  name="reasonfordelete" class="form-control">Write reason for delete......</textarea>
							</div>
							<div class="in-box mt-3">
								<button type="submit" class="log-btn btn btn-default" value="submit" onclick='return confirm("Are you sure you want to delete this account ")'>Submit</button>
							</div>
						</form><!-- form end here -->
					</div><!-- col end here -->
				
				  </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
