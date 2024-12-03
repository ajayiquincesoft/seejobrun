<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Stripe;
use Mail;
use Validator;
use App\Models\User;
use Carbon\Carbon;
use Auth;
use Session;
use App\Models\Addcontactpayment;
use App\Models\Addcontactsubscription;
class PrivacypolicyController extends Controller
{
    public function privacypolicy()
    {
        return view('privacypolicy');
    }
    public function addcontactsubs()
    {
        return view('addcontactsubs');
    }
	public function useraccountdelete()
    {
        return view('account_delete');
    }
	
	public function useraccountMessage(Request $request){
	 
	 $validator = Validator::make($request->all(), [
  
            'email' => 'required|email',
			
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
		
		$user = User::where('email','=',$request->email)->get();
		
		
		if($user->count()>0){
			return redirect()->back()->with(['success'=>'Thank you. We have processed your account deletion request. Your account will be deleted within 2 to 3 working days.']);
		}else{
		 return redirect()->back()->withErrors("Your email is not exist.Please enter the correct email id.");
		}
		
	 
	}
	
	
	public function addcontactsubscription(Request $request)
    {
		print_r($request->all());
		die;
        $validator = Validator::make($request->all(), [
            'stripeToken' => 'required',
         
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }


		// *** end with the update subscription
		
		Stripe\Stripe::setApiKey(config('app.stripe_secret'));
		
		//**** One time payment************************
		
		$payment_amount=10;
		$total_credits = $payment_amount/2;
		  try {
			   try {
					$customer = Stripe\Customer::create(array(
							'email' => 'ajay.iquincesoft@gmail.com',
							'source'  => $request->stripeToken
						));
						
						$stripe_customer_id = $customer->id;
						$user = User::findorfail(160);
						$user->stripe_customer_id=$stripe_customer_id;
						$user->save();
						
					} catch (Exception $e) {
						$api_error = $e->getMessage();
					}
				
                $pay = Stripe\Charge::create([
                    "amount" => $payment_amount * 100,
                    "currency" => "USD",
                    "customer" => $stripe_customer_id,
                    "description" => "One time payment for add contact",
                ]);
				
				
				$AddContactPayment = Addcontactpayment::make();
				$AddContactPayment->user_id =160;
				$AddContactPayment->amount = 10;
				$AddContactPayment->transaction_id = $pay->balance_transaction;
				$AddContactPayment->credits = $total_credits;
				$AddContactPayment->status = $pay->paid;
				$AddContactPayment->payment_date = date('Y-m-d H:i:s', $pay->created);
				$AddContactPayment->save();
				echo "Successfully one time payment done->".$pay->balance_transaction;
				
				if($pay->balance_transaction){
					
						
						if (empty($api_error) && $stripe_customer_id) {
							
							try {
								$planName ='Contact added by iQuincesoft';
								$planInterval = 'month';
								$priceCents = 2*100;
							
								for($i=0;$i<5; $i++){
									$plan = \Stripe\Plan::create(array(
										"product" => [
											"name" => $planName
										],
										"amount" => $priceCents,
										"currency" => 'USD',
										"interval" => $planInterval,
										"interval_count" => 1
									));
									
									$Addcontactsubscription = Addcontactsubscription::make();
									$Addcontactsubscription->user_id=160;
									$Addcontactsubscription->credits=2;
									$Addcontactsubscription->user_plan_id=$plan->id;
									$Addcontactsubscription->stripe_customer_id=$stripe_customer_id;
									$Addcontactsubscription->amount=2;
									$Addcontactsubscription->transaction_id=$pay->balance_transaction;
									$Addcontactsubscription->save();
	
								}
								echo 'helloo->'.$i;
								echo "Plan have been created successfully.";
							
							} catch (Exception $e) {
								$api_error = $e->getMessage();
							}	
							
						}
				}
		}catch (Exception $e) {
			$api_error = $e->getMessage();
		}
			
			
				
       

die;


		
		
        if (empty($api_error) && $stripe_customer_id) {
			try {
                $planName ='Contact added by iQuincesoft';
                $planInterval = 'month';
                $priceCents = 2*100;

                $plan = \Stripe\Plan::create(array(
                    "product" => [
                        "name" => $planName
                    ],
                    "amount" => $priceCents,
                    "currency" => 'USD',
                    "interval" => $planInterval,
                    "interval_count" => 1
                ));
            } catch (Exception $e) {
                $api_error = $e->getMessage();
            }

			 if (empty($api_error) && $plan) {
			   try {
					$subscription = \Stripe\Subscription::create(array(
							"customer" => $stripe_customer_id,
							"items" => array(
								array(
									"plan" =>$plan->id,
								),
							),
						));
					} catch (Exception $e) {
						$api_error = $e->getMessage();
					}
					if (empty($api_error) && $subscription) {
						echo 'Your subscription ID is->>'.$subscription->id;  
					} else {
						return redirect()->back()->withErrors("Unable to create subscription for subscription.Please try later or contact to support.");
					}
			  }else{
				return redirect()->back()->withErrors("Unable to create plan for subscription.Please try later or contact to support.");
			}   
        } else {
            return redirect()->back()->withErrors("Unable to create plan for subscription.Please try later or contact to support.");
        }
    }
}

