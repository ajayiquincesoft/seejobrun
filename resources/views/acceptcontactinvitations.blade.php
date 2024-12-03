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
@media (min-width: 320px) and (max-width: 767px) {
	.topcont{margin-top: -59% !important;}
}
</style>
<div class="container mt--8 pb-5 topcont">
    <div class="row">
        <div class="col-md-12" style="background-color:#e2e3eb;text-align: center;">
            <!-- <img src="admin_assets/img/bglogin.jpg" alt="login" class="login-card-img"> -->
            <img src="{{asset('/Logo-01.png')}}" alt="logo" class="login-card-img1" width="300" style="text-align: center;margin-top: -50px;margin-bottom: -50px;">
        </div>
    </div>
	
	<div class="row">
        <div class="col-md-12" style="background-color:white;">
            <div class="card " style="margin-top:2%; margin-bottom: 20%; box-shadow: none;">
				<div class="card-header"><h1 style="text-align:center">
				<?php if($errormsg==4){ ?>Invitation Already Accepted<?php }else{ ?>
				Invitation Accepted! <?php } ?></h1></div>
				<div class="card-body">
				
						<?php if($errormsg==2){ ?>
							<p>
								Please Download the iPhone or Android application from the following store links and create your account with See Job Run.<br>App Store <a href='https://apps.apple.com/in/app/see-job-run/id6443558941'><img src='https://phplaravel-718462-2697156.cloudwaysapps.com/apple-logo.png' width='100'> </a>.<br>Google Play Store  <a href='https://play.google.com/store/apps/details?id=com.clockk'><img src='https://phplaravel-718462-2697156.cloudwaysapps.com/google-play-logo.jpg' width='90'> </a>
							</p>
						<?php } ?>	
						<?php if($errormsg==3){ ?>
							<p>
								Please click on correct accept invitation link.
							</p>
						<?php } ?>
						<?php if($errormsg==4){ ?>
							<p>
								Sorry !. You have already accepted the invitation.
							</p>
						<?php } ?>
						
						<?php if($msg==1){ ?>
						<p>Thank you for accepting the invitation to connect with See Job Run. We’re excited to have you as part of our network!</p>
						<?php } ?>

						If you have any questions, contact us at <a href="mailto:info@seejobrun.com">info@seejobrun.com</a>.

					
				</div><!-- col end here -->
			</div><!-- row end here -->
		</div><!-- container end here -->
	</div><!-- sign-sec end here -->

</div>


@endsection