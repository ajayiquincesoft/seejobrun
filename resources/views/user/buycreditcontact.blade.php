@extends('user.layout.userdashboard')
@section('content')

  <!-- Content -->
        <div class="container-fluid content ">
            <div class="row">
				<div class="col-md-12">
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
				</div>
            </div>
            <div class="row">
                <div class="col-md-3 "></div>
                <div class="col-md-6 ">
                    <h5 class="text-white">Buy Credit Contacts</h5>
                    <div class="bg-white p-3">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h5>Credit Details</h5>
                            </div>
                        </div>
                       
                        <form action="{{ route('BuyCreditcontacts') }}" method="post"  data-cc-on-file="false" class="require-validation" id="add-client-form" enctype="multipart/form-data" autocomplete="off">
                           @csrf
                            <div class="card_dtls">
                                <div class="form-group required">
                                    <label>Card Number *</label>
                                    <input class="box card-number form-control" type="number" name="card_number" placeholder="" required/>
                                </div>
                                <div class="form-group mt-3">
                                    <div class="row">
                                        <div class="col-md-6 required">
                                            <label>Expiry Month *</label>
                                            <input class="box card-expiry-month form-control" type="number" name="exp_month" placeholder="" min="1" max="12" required/>
                                        </div>
                                        <div class="col-md-6 required">
                                            <label>Expiry Year *</label>
                                            <input class="box card-expiry-year form-control" type="number" name="exp_year" placeholder="" min="2024" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <div class="row mb-2">
                                        <div class="col-md-6 required">
                                            <label>CVV *</label>
                                            <input class="box card-cvc form-control" type="text" name="cvv" placeholder="" required/>
                                        </div>
                                        <div class="col-md-6 required">
                                            <label>PostCode *</label>
                                            <input type="text" name="postcode" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="plansummery">
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                    <h5>Contact Summery</h5>
                                    <hr>
                                    </div>
                                
                                    <div class="col-md-8">
                                    <span>Per Contact</span>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <span>  $2/MONTHLY </span>
                                        <input type="hidden" name="amount" value="{{ old('price') }}" id="plan_prc1">
                                        <input type="hidden" name="contact_credit" value="{{ old('contact_credit') }}" id="plan_credit1">
                                    </div>
                                </div>
                                
                                <div class="row mt-2">
                                    
                                    <div class="col-md-8">
                                    <span>Tatal Contacts</span>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <div class="counter-container text-end">
                                            <div class="counter-wrapper1">
                                                <div class="counter-btn1" id="decrement1">-</div>
                                                <div class="spacer"></div>
                                                <div class="counter-btn1" id="increment1">+</div>
                                            </div>
                                            <div class="counter-display1" id="contactCount1">{{ old('contact_credit') }}
                                            </div>
                                        </div>
                                    </div>
                                
                                </div>
                                <div class="row mt-2">
                        
                                    <div class="col-md-8">
                                    <span>Sub Total</span>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <span id="subtotal"> ${{ old('price') }} </span>
                                    </div>
                                </div>
                                <hr>
                                <div class="row mt-2">
                        
                                    <div class="col-md-8">
                                    <span>Total</span>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <span id="total"> ${{ old('price') }} </span>
                                    </div>
                                </div>
                                </div>
                            <div class="form-group mt-4">
                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <button class="btn btn-primary text-center add-new-job-btn w-100 my-3" type="submit" value="submit" id="submit_button">Buy Now</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
@endsection
    
   
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @section('script')
    
    
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
                   // var $form = $(".require-validation"),
                    /* token contains id, last4, and card type */
                    var token = response['id'];
                    $form.find('input[type=text]').empty();
                    $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
                   
                    $form.get(0).submit();
                }
            }
        });
    </script>
    
    <script>
        // Simple increment/decrement logic for contacts
        let contactCount1 = {{ old('contact_credit') }};
    
        document.getElementById('increment1').addEventListener('click', function () {
            contactCount1++;
            document.getElementById('contactCount1').textContent = contactCount1;
            
            var totalprc1 = contactCount1*2;
            $('#subtotal').html('$'+totalprc1);
            $('#total').html('$'+totalprc1);
            $('#plan_prc1').val(totalprc1);
            $('#plan_credit1').val(contactCount1);
    
        });
    
        document.getElementById('decrement1').addEventListener('click', function () {
            if (contactCount1 > 1) {
                contactCount1--;
                document.getElementById('contactCount1').textContent = contactCount1;
                var totalprc1 = contactCount1*2;
                $('#subtotal').html('$'+totalprc1);
                $('#total').html('$'+totalprc1);
                $('#plan_prc1').val(totalprc1);
                $('#plan_credit1').val(contactCount1);
            }
        });
    </script>
@stop
        