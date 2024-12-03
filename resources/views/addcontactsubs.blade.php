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
            <img src="{{asset('/Logo-01.png')}}" alt="privacy policy" class="login-card-img1" width="300" style="text-align: center;margin-top: -50px;margin-bottom: -50px;">
        </div>
    </div>
	 <div class="row">
        <div class="col-md-12" style="background-color:white;">
            <div class="card " style="margin-top:2%; margin-bottom: 20%; box-shadow: none;">
                <div class="card-header"><h1>Add Contact Subscription</h1></div>

                <div class="card-body">
				           @if(session('success'))
                <div class="alert alert-success">{{session('success')}}</div>
                @endif
                <form action="{{route('addcontactsubscription')}}" method="post" data-cc-on-file="false" class="require-validation" id="add-client-form" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    
                    <div class="card_form">
                        <div class="in-box">
                            <h5>Card Number</h5> <input class="box card-number" type="number" name="card_number" placeholder="" required/>
                        </div>
                        <div class="in-box">
                            <h5>Expiry Month</h5> <input class="box card-expiry-month" type="number" name="exp_month" placeholder="" min="1" max="12" required/>
                        </div>
                        <div class="in-box">
                            <h5>Expiry Year</h5> <input class="box card-expiry-year" type="number" name="exp_year" placeholder="" min="2022" required />
                        </div>
                        <div class="in-box">
                            <h5>CVV</h5> <input class="box card-cvc" type="text" name="cvv" placeholder="" required/>
                        </div>
                    </div>
					<br>
                    <hr>
                    <button class="log-btn" type="submit" value="submit" id="submit_button">Add Contact</button>
                </form><!-- form end here -->
            </div><!-- col end here -->
        </div><!-- row end here -->
    </div><!-- container end here -->
</div><!-- sign-sec end here -->





                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
 <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">

    $(function() {
        var $form = $(".require-validation");
        $('form.require-validation').bind('submit', function(e) {
            var $form = $(".require-validation"),
                inputSelector = ['input[type=email]', 'input[type=password]',
                    'input[type=text]', 'input[type=file]',
                    'textarea'
                ].join(', '),
                $inputs = $form.find('.required').find(inputSelector),
                $errorMessage = $form.find('div.error'),
                valid = true;
            $errorMessage.addClass('hide');
            $('.has-error').removeClass('has-error');
            $inputs.each(function(i, el) {
                var $input = $(el);
                if ($input.val() === '') {
                    $input.parent().addClass('has-error');
                    $errorMessage.removeClass('hide');
                    e.preventDefault();
                }
            });
            if (!$form.data('cc-on-file')) {
                e.preventDefault();
                Stripe.setPublishableKey("{{ config('app.stripe_key') }}");
                Stripe.createToken({
                    number: $('.card-number').val(),
                    cvc: $('.card-cvc').val(),
                    exp_month: $('.card-expiry-month').val(),
                    exp_year: $('.card-expiry-year').val()
                }, stripeResponseHandler);
            }
        });

        function stripeResponseHandler(status, response) {
            if (response.error) {
                // $('.error')
                //     .removeClass('hide')
                //     .find('.alert')
                //     .text(response.error.message);
                alert(response.error.message);
            } else {
                /* token contains id, last4, and card type */
                var token = response['id'];
                $form.find('input[type=text]').empty();
                $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
                $form.get(0).submit();
            }
        }
    });
</script>

@endsection