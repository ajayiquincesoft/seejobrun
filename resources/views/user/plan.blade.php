@extends('user.layout.userdashboard')
@section('content') 

<div class="container-fluid content ">
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
@php 
    //print_r($current_user_subscription);
    //echo 'hello->>'.$current_user_subscription['payment_gateway'];
@endphp
<div class="row plans_data mt-4">  
            <div class="col-md-6">
            <h3 class="text-white">Choose Your Plan</h3>
            <form method="post" action="">  
                @foreach($Plans as $plan)
                @if(@$plan->id==3)
                <div class="row mt-4">
                    <div class="col-md-12">
                        <label class="inputGroup d-block" style="cursor: pointer;">
                            <input type='hidden' value="{{ $current_user_subscription['payment_gateway'] }}" id="paymentgateway">
                            <input id="freeplan" value="3" name="plan" type="radio" style="margin-right: 10px;" {{ @$SelectedPlan->plan_id == 3 ? 'checked' : '' }}>
                            <div class="row">
                                <div class="col-md-6"><strong>Get Free Trial</strong><br><small style="font-size: 12px;">Try it for first 30Days</small></div>
                                <div class="col-md-6"><h2>FREE TRIAL <small style="font-size: 12px;font-weight: bold;">30Days</small></h2></div>
                            </div>
                        </label>
                    </div>
                </div>
                @endif
                @endforeach
                
               <div class="row mt-4"> <!-- Apply gap to the parent row -->
                @foreach($Plans as $plan)
                @if($plan->id==1)
                    <div class="col-md-6" style="width:48%;">
                        <label class="inputGroup d-block" style="cursor: pointer;"> 
                            <input id="freeplan" value="1" name="plan" type="radio" style="margin-right: 10px;" {{ @$SelectedPlan->plan_id == 1 ? 'checked' : '' }}>
                            <div class="row">
                                <div class="col-md-6" style="padding: 0px 9px;"><strong>Monthly</strong><br><small style="font-size: 12px;">Cancel anytime</small></div>
                                <div class="col-md-6" style="padding: 0;"><h2>{{ $plan->price }}<small style="font-size: 12px;font-weight: bold;">/Month</small></h2></div>
                            </div>
                        </label>
                    </div>
                @endif
                @if($plan->id==2)
                <div class="col-md-6" style="width:48%; margin-left:20px;">
                    <label class="inputGroup d-block" style="cursor: pointer;"> 
                        <input id="freeplan" value="2" name="plan" type="radio" style="margin-right: 10px;" {{ @$SelectedPlan->plan_id == 2 ? 'checked' : '' }}>
                        <div class="row">
                            <div class="col-md-7" style="padding: 0px 9px;"><strong>Annually</strong><br><small style="font-size: 12px;">1Month Discount</small></div>
                            <div class="col-md-5" style="padding: 0;"><h2>{{ $plan->price }}<small style="font-size: 12px;font-weight: bold;">/Yr</small></h2></div>
                        </div>
                    </label>
                </div>

                @endif
                @endforeach 
            </div>
            <div class="row mt-4">
            <button type="submit" class="btn btn-primary text-center add-new-job-btn w-100 my-3 p-3" id="subscribeButton" >Subscribe Now</button>
            </div>
        </form>
            </div>
       
            <div class="col-md-6">
                
                <h3 class="text-white">All Access Plan Features</h3>
                <div class="bg-white p-3 mt-4">
                <ul class="plan_text">
                    <li>Track new leads, set up jobs, contacts, clients, and employees.</li>
                    <li>Call for inspections, create checklists, and assign tasks to subcontractors, employees, or clients.</li>
                    <li>Have employees fill out time cards and track them with GPS while clocked in, allowing you to monitor their progress.</li>
                    <li>Add photos to each task for every job.</li>
                    <li>Use an easy change order template for quick client approvals with auto-send to your bookkeeper for billing.</li>
                    <li>Utilize a final punch list template with client sign-off to quickly wrap up and move on to the next job.</li>
                </ul>
                
                </div>
            </div>
        
</div>

<!-- Credit Card Modal -->
<div class="modal fade" id="creditCardModal" tabindex="-1" aria-labelledby="creditCardModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="creditCardModalLabel">Enter Credit Card Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm" method="POST" action="{{ route('PurchasePlan') }}" data-cc-on-file="false" class="require-validation" id="membership" enctype="multipart/form-data" autocomplete="off">
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
                    <button type="submit" class="btn btn-primary w-100 mt-4">Make Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>

</div>
@endsection
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
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


document.getElementById('subscribeButton').addEventListener('click', function(e) {
    e.preventDefault(); // Prevent the default form submission

    // Get the selected plan
    var selectedPlan = document.querySelector('input[name="plan"]:checked');
    
    // Check if a plan is selected
    if (!selectedPlan) {
        alert('Please select a plan before proceeding.');
        return; 
    }
    var paymentGateway = document.getElementById('paymentgateway').value;
    if (paymentGateway !='stripe') {
        alert('You Can not upgrade your plan from website');
        return; 
    }

    var planId = selectedPlan.value;
    console.log('Selected plan ID:', planId);

    // Create the hidden input
    var hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'selected_plan';  // The name of the hidden input field
    hiddenInput.value = planId;          // Set the value of the hidden input to the selected plan ID
    var membershipForm = document.getElementById('paymentForm');
    if (membershipForm) {
        membershipForm.appendChild(hiddenInput);
        console.log('Hidden input appended to the form');
    } else {
        console.error('Form with ID "membership" not found');
    }

    // Show the credit card modal
    $('#creditCardModal').modal('show');
});

</script>    
@stop