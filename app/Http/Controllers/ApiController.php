<?php

namespace App\Http\Controllers;
use JWTAuth;
use Mail;
use Validator;
use App\Models\User;
use App\Models\Stage;
use App\Models\changeorder;
use App\Models\Template;
use App\Models\Job;
use App\Models\Lead;
use App\Models\Todosection;
use App\Models\Todosectiontask;
use App\Models\General_todo_section;
use App\Models\General_todo_task;
use App\Models\Contact;
use App\Models\Jobstage;
use App\Models\Jobmedia;
use App\Models\Media;
use App\Models\Jobcontacts;
use App\Models\Stagetemplate;
use App\Models\SelectedPlan;
use App\Models\Punchlist;
use App\Models\Punchlistimg;
use App\Models\Taskassignment;
use App\Models\Taskassignmentimages;
use App\Models\FcmToken;
use App\Models\Notification;
use App\Models\Contactshared;
use App\Models\Events;
use App\Mail\TestMail;
use App\Models\Plan;
use App\Models\Clocktime;
use App\Models\Payment;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Config;
use Google_Client;
use Google_Service_AndroidPublisher;
use Stripe;
use Twilio\Rest\Client;
use App\Models\Addcontactpayment;
use App\Models\Addcontactsubscription;
class ApiController extends Controller 
{ 
  public $token = true;
  
  public function register(Request $request)
   {
        $validator = Validator::make($request->all(), 
        [ 
           'name' => 'required',
           'email' => 'required|email|unique:users',
           'password' => 'required',  
           'mobile' => 'required',
           'address' => 'required',
           'city' => 'required',
           'state' => 'required',
           'pincode' => 'required',
           'devicetype' => 'required',
		   'timezone' => 'required',
        ]);  
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   

        try 
        {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            if ($request->profile_pic) 
            {
                $frontimage = str_replace("data:image/jpeg;base64,", '', $request->profile_pic);
                $frontimage1 = str_replace(" ","+",$frontimage);

                $profile_pic = time() . '.jpeg';
                file_put_contents($profile_pic, base64_decode($frontimage1));
                $user->profile_pic = $profile_pic;
            }
			$user->devicetype = $request->devicetype;
			$user->timezone = $request->timezone;
            $user->save();
			
            $user_save = $user->createMeta([
                'Mobile' => $request->mobile,
                'Business_name' => $request->business_name,
                'License_no' => $request->license_no,
                'Address' => $request->address,
                'City' => $request->city,
                'State' => $request->state,
                'Pincode' => $request->pincode,
            ]);

           /*  $selectedPlan = $user->selectedPlan()->make();
            $selectedPlan->plan_id = $request->plan_id;
            $selectedPlan->start_date = Carbon::now();
            $selectedPlan->end_date = Carbon::now()->addDays(7);
            $selectedPlan->save(); */

            $encrypt_user_id = $user->id;

            $getcontact = Contact::where('email','=',$request->email)->update(['contact_user_id' => $encrypt_user_id ]);

            $from_email = Config::get('mail.from.address');
            $email = $request->email;
            $name = $request->name;
            $subject = "Verify Email";

            $body = @Template::where('type', 11)->orderBy('id', 'DESC')->first()->content;
            $content = array('name' => $name, 'user_id' => $encrypt_user_id);
            foreach ($content as $key => $parameter) 
            {
                $body = str_replace('{{' . $key . '}}', $parameter, $body);
            }

            // if($from_email)
            // {
                Mail::send('emails.name', ['template' => $body, 'name' => $name, 'user_id' => $encrypt_user_id], function ($m) use ($from_email, $email, $name, $subject) 
                {
                    $m->from($from_email, 'See Job Run');

                    $m->to($email, $name)->subject($subject);
                });
            // }

            return response()->json(
            [
                'success' => true,
                'message' => 'To complete your registration please verify your email now.',
            ]);
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }
    public function registerNew(Request $request)
    {
        $validator = Validator::make($request->all(), 
        [ 
           'name' => 'required',
           'email' => 'required|email|unique:users',
           'password' => 'required',  
           'mobile' => 'required',
           'address' => 'required',
           'city' => 'required',
           'state' => 'required',
           'pincode' => 'required',
           'devicetype' => 'required',
		   'timezone' => 'required',
        ]);  
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   

		
        try 
        {
			
			
			$now = Carbon::now();
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            if ($request->profile_pic) 
            {
                $frontimage = str_replace("data:image/jpeg;base64,", '', $request->profile_pic);
                $frontimage1 = str_replace(" ","+",$frontimage);

                $profile_pic = time() . '.jpeg';
                file_put_contents($profile_pic, base64_decode($frontimage1));
                $user->profile_pic = $profile_pic;
            }
			$user->devicetype = $request->devicetype;
			$user->timezone = $request->timezone;
            $register_otp = rand(1000, 9999);
			$user->register_otp = $register_otp;
			$user->created_at = $now;
			
			$user->save();
			  $user_save = $user->createMeta([
				'Mobile' => $request->mobile,
				'Business_name' => $request->business_name,
				'License_no' => $request->license_no,
				'Address' => $request->address,
				'City' => $request->city,
				'State' => $request->state,
				'Pincode' => $request->pincode,
			]);



            
					/**************Twilio OTP Send*****************/
					$phoneNumber = $request->mobile ;
					// Remove brackets and dashes
					   $cleanedPhoneNumber = str_replace(['(', ')', '-', ' '], '', $phoneNumber);
						
					    $receiverNumber = $cleanedPhoneNumber;
						$message = "Your 4 digit code for See Job Run is: $register_otp. Please use this code to verify your phone number. \n\nThank you,\nAdmin See Job Run";
						
							$account_sid = getenv("TWILIO_SID");
							$auth_token = getenv("TWILIO_TOKEN");
							$twilio_number = getenv("TWILIO_FROM");
						
							$client = new Client($account_sid, $auth_token);
							$client->messages->create($receiverNumber, [
								'from' => $twilio_number, 
								'body' => $message]);
							
							
							
							
							$encrypt_user_id = $user->id;

							$getcontact = Contact::where('email','=',$request->email)->update(['contact_user_id' => $encrypt_user_id ]);

							$from_email = Config::get('mail.from.address');
							$email = $request->email;
							$name = $request->name;
							$subject = "Please Verify Your Email Address";

							$body = @Template::where('type', 2)->orderBy('id', 'DESC')->first()->content;
							$content = array('name' => $name, 'user_id' => $encrypt_user_id,'register_otp'=> $register_otp);
							foreach ($content as $key => $parameter) 
							{
								$body = str_replace('{{' . $key . '}}', $parameter, $body);
							}

						  
								// Mail::send('emails.name', ['template' => $body, 'name' => $name, 'user_id' => $encrypt_user_id,'register_otp'=> $register_otp], function ($m) use ($from_email, $email, $name, $subject,$register_otp) 
								// {
								// 	$m->from($from_email, 'See Job Run');

								// 	$m->to($email, $name)->subject($subject);
								// });
                                try {
                                    Mail::send('emails.name', ['template' => $body, 'name' => $name, 'user_id' => $encrypt_user_id, 'register_otp' => $register_otp], function ($m) use ($from_email, $email, $name, $subject,$register_otp) {
                                        $m->from($from_email, 'See Job Run');
                                        $m->to($email, $name)->subject($subject);
                                    });
                                } catch (\Exception $e) {
                                    // Log the error but do not show it to the user
                                    //Log::error('Error sending email: ' . $e->getMessage());
                                }
         
						
						
						
					
			
	
			/**************End Twilio OTP Send*****************/
			

			
           /*$selectedPlan = $user->selectedPlan()->make();
            $selectedPlan->plan_id = $request->plan_id;
            $selectedPlan->start_date = Carbon::now();
            $selectedPlan->end_date = Carbon::now()->addDays(7);
            $selectedPlan->save(); */
			
			
			
			
/* 
            $encrypt_user_id = $user->id;

            $getcontact = Contact::where('email','=',$request->email)->update(['contact_user_id' => $encrypt_user_id ]);

            $from_email = Config::get('mail.from.address');
            $email = $request->email;
            $name = $request->name;
            $subject = "Verify Email";

            $body = @Template::where('type', 2)->orderBy('id', 'DESC')->first()->content;
            $content = array('name' => $name, 'user_id' => $encrypt_user_id);
            foreach ($content as $key => $parameter) 
            {
                $body = str_replace('{{' . $key . '}}', $parameter, $body);
            }

         
                Mail::send('emails.name', ['template' => $body, 'name' => $name, 'user_id' => $encrypt_user_id], function ($m) use ($from_email, $email, $name, $subject) 
                {
                    $m->from($from_email, 'See Job Run');

                    $m->to($email, $name)->subject($subject);
                });
          */

            return response()->json(
            [
                'success' => true,
				'user_id'=> $user->id,
                'message' => 'To complete your registration please verify your email now.',
            ]);
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function RegisterOtpAuth(Request $request)
    {
		$validator = Validator::make($request->all(), 
        [ 
           'register_otp' => 'required',
		   'user_id'=> 'required',
         
        ]);  
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }  
		
		$now = Carbon::now();
		$register_otp = $request->register_otp;
		$user_details = User::where('id',$request->user_id)->where('register_otp', $register_otp)->first();
	
        if ($user_details) 
        {
            if ($user_details->is_varified) 
            {
				return response()->json(
					[
						'success' => true,
						'message' =>  'Your email already confirmed.'
					]);
            } 
            else 
            {
                $update = User::where('register_otp', $register_otp)->update(['is_varified' => 1, 'email_verified_at' => $now]);

				return response()->json(
					[
						'success' => true,
						'message' =>  'Your OTP has been successfully verified. Thank you for confirmation with SeeJobRun.'
					]);
            }
        } 
        else 
        {
            
			return response()->json(
					[
						'success' => false,
						'message' =>  'Your OTP is invalid. Please contact to the admin.'
					]);
			
        }

    } 

	public function verifyEmail($id)
    {
        $now = Carbon::now();
        $user_id = $id;

        $user_details = User::where('id', $user_id)->first();
        if ($user_details) 
        {
            if ($user_details->is_varified) 
            {
                $msg = "Your email already confirmed.";
            } 
            else 
            {
                $update = User::where('id', $user_id)->update(['is_varified' => 1, 'email_verified_at' => $now]);
                $msg = "Thank you for confirming your email.";
            }
        } 
        else 
        {
            $msg =  "No such user found. please contact to the admin.";
        }

        return view('confirm', compact('msg'));
    }

    public function get_user(Request $request)
    {
        $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
        ]); 

        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            $slectedPlans = $user->selectedPlan()->orderBy('id', 'desc')->first();

            if ($slectedPlans) 
            {
                if (strtotime(date('Y-m-d h:i:s', strtotime($slectedPlans->end_date))) < strtotime(date('Y-m-d h:i:s')) && $slectedPlans->subscription_status == 1) 
                {
                    if ($this->checkSubscriptionStatusforcoach($slectedPlans->subscription_id)) 
                    {
                        if ($slectedPlans->plan_id == 2) 
                        {
                            $slectedPlans->end_date = date('Y-m-d H:i:s', strtotime('+1 year', strtotime($slectedPlans->end_date)));
                        } 
                        else 
                        {

                            $slectedPlans->end_date = date('Y-m-d H:i:s', strtotime('+1 month', strtotime($slectedPlans->end_date)));
                        }

                        $slectedPlans->save();
                    }
                }
            }

            return response()->json(['user' => $user, 'selectedPlans' => $slectedPlans]);
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }
	
	
	
    public function makePayment(Request $request)
    {
        $validator = Validator::make($request->all(), 
        [
            'token' => 'required',
            'stripe_token' => 'required',
            'amount' => 'required',
            'plan_id' => 'required'
        ]);
    
        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        if ($user) 
        {
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            $pay = Stripe\Charge::create([
                "amount" => $request->amount * 100,
                "currency" => "USD",
                "source" => $request->stripe_token,
                "description" => "test",
            ]);

            $customerpay = Payment::make();
            $customerpay->user_id = $user->id;
            $customerpay->amount = $pay->amount / 100;
            $customerpay->transaction_id = $pay->balance_transaction;
            $customerpay->status = $pay->paid;
            $customerpay->payment_date = date('Y-m-d H:i:s', $pay->created);
            $customerpay->save();
        } 
        else 
        {
            return response()->json(
            [
                'success' => false,
                'message' => 'Token is not valid. please contact to the admin.',
            ]);
        }
    }

    public function login(Request $request)
    {
		$validator = Validator::make($request->all(), [
			'email' => 'required|email',
			'password' => 'required',
		]);
		if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }
        $input = $request->only('email', 'password');
        $jwt_token = null;
		
        $input['is_varified'] = 1;
		
		
		
	
        try
        {
			
			 /* $user = User::where('email', $request->email)->where('devicetype', $request->devicetype)->first();
			 if(!$user){
				return response()->json([
					'success' => false,
					'message' => 'Invalid device type. Please log in from the correct device.',
				]);
			 } */	
            if (!$jwt_token = JWTAuth::attempt($input, ['exp' => \Carbon\Carbon::now()->addDays(365)->timestamp])) 
            {
                return response()->json(
                [
                    'success' => false,
                    'message' => 'Login credentials are invalid or you have not verified the email yet.To verify email please click on received email link.',
                ]);
            }

            return response()->json(
            [
                'success' => true,
                'token' => $jwt_token,
            ]);
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function getUser(Request $request)
    {
        $this->validate($request, 
        [
            'token' => 'required'
        ]);

        $user = JWTAuth::authenticate($request->token);
		
        try
        {
            $slectedPlans = SelectedPlan::where('user_id','=',$user->id)->orderBy('id', 'desc')->first();
			
			if(@$slectedPlans){
				if($slectedPlans->plan_id !=3){
					$purchaseToken = $slectedPlans->purchase_token;
					$receipt = $slectedPlans->receipt;
					
					$subscription_id = $slectedPlans->subscription_id;
					
					$substr = substr($subscription_id, 0, 3);;
					
					//if($request->deviceType=='android' && $purchaseToken){
						
					if($substr=='GPA'){
						if($slectedPlans->plan_id==1){
							$productId = 'plan_monthly';
						}elseif($slectedPlans->plan_id==2){
							$productId = 'plan_yearly';
						}
						//$slectedPlans->subscription_id;
						
						$client = new Google_Client();
						$client->setApplicationName('seejobrun');
						$client->setAuthConfig(storage_path('app/google_play_credentials.json'));
						$client->setScopes([Google_Service_AndroidPublisher::ANDROIDPUBLISHER]);
						
						
						$androidPublisher = new Google_Service_AndroidPublisher($client);
						$packageName = 'com.clockk';
						
						$subscription = $androidPublisher->purchases_subscriptions->get($packageName, $productId, $purchaseToken);
						
						$acknowledgementState = $subscription['acknowledgementState'];
						/* if($acknowledgementState==0){
			
						 $acknowledgedPurchase = $androidPublisher->purchases_subscriptions->acknowledge(
							$packageName,
							$productId,
							$purchaseToken
						);

						// Check the acknowledgment status
						$acknowledgementState = $acknowledgedPurchase->getAcknowledgementState();
						if ($acknowledgementState == 1) {
							// Acknowledgment was successful
							echo 'Purchase acknowledged successfully.';
						} else {
							// Acknowledgment failed
							echo 'Purchase acknowledgment failed.';
						} 
						
						} */
						
					
						
						$cancelReason = $subscription['cancelReason'];
						$paymentState = $subscription['paymentState'];
						
						if($paymentState==1 && $cancelReason==''){
							 $slectedPlans = SelectedPlan::where('user_id','=',$user->id)->orderBy('id', 'desc')->first();
							 
							 $slectedPlans->status=0;
							 $slectedPlans->subscription_status=0;
							 $slectedPlans->save();
							return response()->json(
							[
								'success' => true,
								'data' =>  $slectedPlans,
							]); 
						}elseif($paymentState==1 && $cancelReason==0){
							
							 $slectedPlans = SelectedPlan::where('user_id','=',$user->id)->orderBy('id', 'desc')->first();
							 
							 $slectedPlans->status=1;
							 $slectedPlans->subscription_status=1;
							 $slectedPlans->save();
							 
							return response()->json(
							[
								'success' => false,
								'message' =>  'Subscription is currently active, and payment for the current billing cycle is pending.',
								'data' =>  $slectedPlans,
							]);
							
						}elseif($paymentState=='' && $cancelReason==1){
							
							 $slectedPlans = SelectedPlan::where('user_id','=',$user->id)->orderBy('id', 'desc')->first();
							 
							 $slectedPlans->status=1;
							 $slectedPlans->subscription_status=1;
							 $slectedPlans->save();
							 
							return response()->json(
							[
								'success' => false,
								'message' =>  'Your Plan Has been Expired.',
								'data' =>  $slectedPlans,
							]);
							
						}elseif($paymentState=='' && $cancelReason==0){
							 $slectedPlans = SelectedPlan::where('user_id','=',$user->id)->orderBy('id', 'desc')->first();
							 
							 $slectedPlans->status=1;
							 $slectedPlans->subscription_status=1;
							 $slectedPlans->save();
							 
							return response()->json(
							[
								'success' => false,
								'message' =>  'Your Plan Has been cancel.',
								'data' =>  $slectedPlans,
							]);
							
						}elseif($paymentState=='' && $cancelReason==3){
							 $slectedPlans = SelectedPlan::where('user_id','=',$user->id)->orderBy('id', 'desc')->first();
							 
							 $slectedPlans->status=1;
							 $slectedPlans->subscription_status=1;
							 $slectedPlans->save();
							 
							return response()->json(
							[
								'success' => false,
								'message' =>  'Subscription was replaced with a different subscription plan."',
								'data' =>  $slectedPlans,
							]);
							
						}
						
					}//elseif($request->deviceType=='ios' && $receipt){
						elseif($substr !='GPA'){
						
							$curl = curl_init();
							curl_setopt_array($curl, array(
							/*CURLOPT_URL => 'https://sandbox.itunes.apple.com/verifyReceipt',*/
							 CURLOPT_URL => 'https://buy.itunes.apple.com/verifyReceipt', 
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_ENCODING => '',
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 0,
							CURLOPT_FOLLOWLOCATION => true,
							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							CURLOPT_CUSTOMREQUEST => 'POST',
							CURLOPT_POSTFIELDS =>'{
							"receipt-data":"'.$receipt.'",
							"password":"d664e45baee94e9c8fbbbae38205129e",
							"exclude-old-transactions":true
							}',
							CURLOPT_HTTPHEADER => array(
							'Content-Type: application/json'
							),
							));
							$response = curl_exec($curl);
							curl_close($curl);						
							$responseData = json_decode($response);	
							
							
							if ($responseData->status === 0) {
								$latestReceiptInfo = $responseData->latest_receipt_info;
								
								$numericTimestamp = strtotime($latestReceiptInfo[0]->expires_date);
								$currentTimestamp = time();
								if($numericTimestamp > $currentTimestamp){
									$slectedPlans = SelectedPlan::where('user_id','=',$user->id)->orderBy('id', 'desc')->first();
									 $slectedPlans->status=0;
									 $slectedPlans->subscription_status=0;
									 $slectedPlans->save();
									
									return response()->json(
									[
										'success' => true,
										'data' =>  $slectedPlans,
									]);
									
								}else{
									
									$slectedPlans = SelectedPlan::where('user_id','=',$user->id)->orderBy('id', 'desc')->first();
									 $slectedPlans->status=1;
									 $slectedPlans->subscription_status=1;
									 $slectedPlans->save();
									return response()->json(
									[
										'success' => false,
										'message' =>  'Your Plan Has been Expired.',
										'data' =>  $slectedPlans,
									]);
								}
							} 
					}else{
						if ($slectedPlans->end_date > Carbon::now()) 
							{
							return response()->json(
							[
								'success' => true,
								'data' =>  $slectedPlans,
							]); 
							
						}
						else
						{
							return response()->json(
							[
								'success' => false,
								'message' =>  'Your Plan Has been Expired.',
								'data' =>  $slectedPlans,
							]);
						}
				}
			}else{
				
				if ($slectedPlans->end_date > Carbon::now()) 
						{
						return response()->json(
						[
							'success' => true,
							'data' =>  $slectedPlans,
						]); 
						
					}
					else
					{
						return response()->json(
						[
							'success' => false,
							'message' =>  'Your Plan Has been Expired.',
							'data' =>  $slectedPlans,
						]);
					}
				
			}				
			}else{
				$data = array('plan_id'=>0);
				return response()->json(
						[
							'success' => false,
							'message' =>  'You have no plan',
							'data' =>  $data
						]);
			}	
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }
	public function selectPlan(Request $request){
		
        $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
		   'plan_id' => 'required'
        ]); 

        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }
		
        $user = JWTAuth::authenticate($request->token);
		
		if($user){
			
			$userselectedPlan = $user->selectedPlan()->where('user_id','=',$user->id)->first();
			if(@$userselectedPlan){
				$userselectedPlan->user_id = $user->id; 
				$userselectedPlan->plan_id = $request->plan_id;
				$userselectedPlan->status = 1;
				$userselectedPlan->start_date = Carbon::now();
				
				if($request->plan_id==3){
					$userselectedPlan->end_date = Carbon::now()->addDays(60);
					
				}
				/* if($request->plan_id==2){
					$userselectedPlan->end_date = Carbon::now()->addYear();
					$userselectedPlan->subscription_id = $request->subscription_id;
					$userselectedPlan->subscription_status = $request->subscription_status;
				}
				if($request->plan_id==1){
					$userselectedPlan->end_date = Carbon::now()->addMonth();
					$userselectedPlan->subscription_id = $request->subscription_id;
					$userselectedPlan->subscription_status = $request->subscription_status;
				} */
				$userselectedPlan->save();
				return response()->json(
					[
						'success' => true,
						'message' =>  'You have saved selected plan.'
					]);
				
			}else{
				$selectedPlan = $user->selectedPlan()->make();
				$selectedPlan->user_id = $user->id; 
				$selectedPlan->plan_id = $request->plan_id;
				$selectedPlan->status = 1;
				$selectedPlan->start_date = Carbon::now();
				
				if($request->plan_id==3){
					$selectedPlan->end_date = Carbon::now()->addDays(60);
					
				}
				/* if($request->plan_id==2){
					$selectedPlan->end_date = Carbon::now()->addYear();
					$selectedPlan->subscription_id = $request->subscription_id;
					$selectedPlan->subscription_status = $request->subscription_status;
				}
				if($request->plan_id==1){
					$selectedPlan->end_date = Carbon::now()->addMonth();
					$selectedPlan->subscription_id = $request->subscription_id;
					$selectedPlan->subscription_status = $request->subscription_status;
				} */
				$selectedPlan->save();
				return response()->json(
					[
						'success' => true,
						'message' =>  'You have saved selected plan.'
					]);
		 }
		}else{
			return response()->json(
				[
					'success' => false,
					'message' =>  'Something went wrong !'
				]);
		}
		
	}
	
	public function selectPlanTest(Request $request){
		
        $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
		   'plan_id' => 'required'
        ]); 

        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }
		
        $user = JWTAuth::authenticate($request->token);
		
		if($user){
			
			$userselectedPlan = $user->selectedPlan()->where('user_id','=',$user->id)->first();
			if(@$userselectedPlan){
				$userselectedPlan->user_id = $user->id; 
				$userselectedPlan->plan_id = $request->plan_id;
				$userselectedPlan->status = 1;
				$userselectedPlan->start_date = Carbon::now();
				
				if($request->plan_id==3){
					$userselectedPlan->end_date = Carbon::now()->addDays(30);
					
				}
				/* if($request->plan_id==2){
					$userselectedPlan->end_date = Carbon::now()->addYear();
					$userselectedPlan->subscription_id = $request->subscription_id;
					$userselectedPlan->subscription_status = $request->subscription_status;
				}
				if($request->plan_id==1){
					$userselectedPlan->end_date = Carbon::now()->addMonth();
					$userselectedPlan->subscription_id = $request->subscription_id;
					$userselectedPlan->subscription_status = $request->subscription_status;
				} */
				$userselectedPlan->save();
				return response()->json(
					[
						'success' => true,
						'message' =>  'You have saved selected plan.'
					]);
				
			}else{
				$selectedPlan = $user->selectedPlan()->make();
				$selectedPlan->user_id = $user->id; 
				$selectedPlan->plan_id = $request->plan_id;
				$selectedPlan->status = 1;
				$selectedPlan->start_date = Carbon::now();
				
				if($request->plan_id==3){
					$selectedPlan->end_date = Carbon::now()->addDays(30);
					
				}
				/* if($request->plan_id==2){
					$selectedPlan->end_date = Carbon::now()->addYear();
					$selectedPlan->subscription_id = $request->subscription_id;
					$selectedPlan->subscription_status = $request->subscription_status;
				}
				if($request->plan_id==1){
					$selectedPlan->end_date = Carbon::now()->addMonth();
					$selectedPlan->subscription_id = $request->subscription_id;
					$selectedPlan->subscription_status = $request->subscription_status;
				} */
				$selectedPlan->save();
				return response()->json(
					[
						'success' => true,
						'message' =>  'You have saved selected plan.'
					]);
		 }
		}else{
			return response()->json(
				[
					'success' => false,
					'message' =>  'Something went wrong !'
				]);
		}
		
	}
    public function logout(Request $request)
    {
        $this->validate($request, 
        [
            'token' => 'required'
        ]);
  
        try 
        {
            JWTAuth::invalidate($request->token);
  
            return response()->json(
            [
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } 
        catch (JWTException $exception) 
        {
            return response()->json(
            [
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ]);
        }
    }

    public function showUser(Request $request)
    {
        $this->validate($request, 
        [
            'token' => 'required'
        ]);

        $user = JWTAuth::authenticate($request->token);

        try
        {
			
            $user->getMetas();
			$plans = SelectedPlan::where('user_id','=',$user->id)->first();
            return response()->json([ 'success' => true,'user' => $user,'plan_id'=>@$plans->plan_id]);
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function user_meta_save(Request $request)
    {
        $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'meta_key' => 'required',
           'meta_value' => 'required',
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }

        $user = JWTAuth::authenticate($request->token);

        /*$user_meta = new userMetas();
        $user_meta->meta_key = $request->meta_key;
        $user_meta->meta_value = $request->meta_value;
        $user->userMetas()->save($user_meta);*/

        for ($x = 0; $x < count($request->meta_key); $x++) 
        {
            $get_key = $user->getMeta($request->meta_key[$x]);

            if($get_key)
            {
                $user_save = $user->updateMeta([
                    $request->meta_key[$x] => $request->meta_value[$x],
                ]);

                //$user->deleteMeta($request->meta_key[$x]);
            }
            else
            {
                $user_save = $user->createMeta([
                    $request->meta_key[$x] => $request->meta_value[$x],
                ]);
            }
        }
        
        return response()->json(
        [
            'success' => $user_save,
        ]);
    }

    public function addStage(Request $request)
    {
        $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'name' => 'required',
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
               /* $stage = $user->stage()->where('name', '=', $request->name)->first();

                 if($stage)
                {
                    return response()->json([
                        'success' => false,
                        'message' => 'This Stage name is Already Exits.'
                    ]);
                }
                else
                { */
                    $stage = $user->stage()->make();
                    $stage->name = $request->name;
                    $stage->status = '1';
                    $stage->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Your Stage has been Saved Successfully.'
                    ]);
                /* } */
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }
	
    public function updateStage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'id'    => 'required',
        ]);

        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }  

        $user = JWTAuth::authenticate($request->token);
		
        try
        {
            if($user)
            {
               /*  $stage_exits = $user->stage()->where('id', '=', $request->id)->first();

                if($stage_exits)
                { */
                    $stage = stage::findorfail($request->id);
					
                    if($request->name)
                    {
                        $stage->name = $request->name;
                    }
                    if(!empty($request->progress_status))
                    {	
						
                        $stage->progress_status = $request->progress_status;
                    }
					if($request->progress_status==0)
                    {	
						
                        $stage->progress_status = 0;
                    }
					
                    if ($stage->save()) 
                    {
                        return response()->json([
                            'success' => true, 
                            'message' => 'Stage has been updated successfully.',
                        ]);
                    } 
                    else 
                    {
                        return response()->json([
                            'success' => false, 
                            'message' => 'Unable to update Stage.Please try again or contact to admin.',
                        ]);
                    } 
                 /*}
                else
                {
                    return response()->json([
                        'success' => false, 
                        'message' => 'You can not update this Stage.',
                    ]);
                } */
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function update_gateno(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'job_id'  => 'required',
  
        ]);

        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }  

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $job_exits = $user->job()->where('id', '=', $request->job_id)->first();

                if($job_exits)
                {
                    $job = job::findorfail($request->job_id);
					
					if($request->gate_no==''){
							$job->gate_no ='';
					}elseif($request->gate_no==0){
							$job->gate_no = 0;
					}else{
							$job->gate_no = $request->gate_no;
					}
						/* if($request->gate_no==0){
							$job->gate_no = 0;
						}elseif($request->gate_no==''){
							$job->gate_no ='';
						}else{
							$job->gate_no = $request->gate_no;
						} */
						if($request->permit_no==' '){
							$job->permit_no = '';
						}elseif($request->permit_no==0){
							$job->permit_no = 0;
						}else{
							$job->permit_no = $request->permit_no;
						}
						
						if($request->job_type){
							$job->job_type = $request->job_type;
						}
						if($request->contract_status==0 OR $request->contract_status==1){
							$job->contract_status = $request->contract_status;
						}
						
						if($request->job_type=='Archived'){
								$job->status = 2;
						}
						if($request->job_type !='Archived'){
								$job->status = 1;
						}
					
                    if ($job->save()) 
                    {
                        return response()->json([
                            'success' => true, 
                            'message' => 'Job has been updated successfully.',
                        ]);
                    } 
                    else 
                    {
                        return response()->json([
                            'success' => false, 
                            'message' => 'Unable to update Job.Please try again or contact to admin.',
                        ]);
                    } 
                }
                else
                {
                    return response()->json([
                        'success' => false, 
                        'message' => 'You can not update this Job.',
                    ]);
                }
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }
    public function testupdate_gateno(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'job_id'  => 'required',
  
        ]);

        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }  

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $job_exits = $user->job()->where('id', '=', $request->job_id)->first();

                if($job_exits)
                {
                    $job = job::findorfail($request->job_id);
					
					if($request->gate_no==''){
							$job->gate_no ='';
					}elseif($request->gate_no==0){
							$job->gate_no = 0;
					}else{
							$job->gate_no = $request->gate_no;
					}
                    if($request->Lock_box_code==''){
                        $job->Lock_box_code ='';
                        }elseif($request->Lock_box_code==0){
                                $job->Lock_box_code = 0;
                        }else{
                                $job->Lock_box_code = $request->Lock_box_code;
                        }
                        
						/* if($request->gate_no==0){
							$job->gate_no = 0;
						}elseif($request->gate_no==''){
							$job->gate_no ='';
						}else{
							$job->gate_no = $request->gate_no;
						} */
                    	if($request->name){
							$job->name = $request->name;
						}
						if($request->permit_no==' '){
							$job->permit_no = '';
						}elseif($request->permit_no==0){
							$job->permit_no = 0;
						}else{
							$job->permit_no = $request->permit_no;
						}
						
						if($request->job_type){
							$job->job_type = $request->job_type;
						}
						if($request->contract_status==0 OR $request->contract_status==1){
							$job->contract_status = $request->contract_status;
						}
						
						if($request->job_type=='Archived'){
								$job->status = 2;
						}
						if($request->job_type !='Archived'){
								$job->status = 1;
						}
					
                    if ($job->save()) 
                    {
                        return response()->json([
                            'success' => true, 
                            'message' => 'Job has been updated successfully.',
                        ]);
                    } 
                    else 
                    {
                        return response()->json([
                            'success' => false, 
                            'message' => 'Unable to update Job.Please try again or contact to admin.',
                        ]);
                    } 
                }
                else
                {
                    return response()->json([
                        'success' => false, 
                        'message' => 'You can not update this Job.',
                    ]);
                }
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function deleteStage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'id' => 'required'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $delete_stage = $user->stage()->where('id', '=', $request->id)->delete();

                if($delete_stage)
                {   
                    return response()->json([
                        'success' => true, 
                        'message' => 'Stage has been deleted successfully.',
                    ]);
                }
                else
                {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Unable to delete Stage.Please try again or contact to admin.',
                    ]);
                }
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function getStage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $st = array();
                $stagetemplate = $user->stagetemplate()->where('status', '=', 1)->get();

                //dd($stagetemplate);

                foreach ($stagetemplate as $value) 
                {
                    $st= array_merge($st,unserialize($value->stage_id));
                    //array_push($st,unserialize($value->stage_id));
                    //break;
                }

                $stage = $user->stage()->doesnthave('jobstage')->where('status', '=', 1)->whereNotIn('id',$st)->get();

                return response()->json([
                    'success' => true,
                    'stage' => $stage
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function addMedia(Request $request)
    {
		
        $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'name' => 'required',
           'file_name' => 'required',
           'type' => 'required'
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $media = $user->media()->make();

                /*if($request->type == "1")
                {
                    $frontimage = str_replace("data:image/jpeg;base64,", '', $request->file_name);
                    $frontimage1 = str_replace(" ","+",$frontimage);
                    $files_name = 'media/'.time() . '.jpeg';
                    file_put_contents($files_name, base64_decode($frontimage1));
                }
                else
                {
                    $image = $request->file('file_name');
                    $files_name = 'media/'.time() .'.'. $image->extension();
                    $ImageFilePath = public_path('media');
                    $image->move($ImageFilePath, $files_name);
                }*/

                /*if($request->type == "1")
                {
                    $files_name = mediaImage($request->file_name,$request->type);
                }
                else
                {
                    $files_name = mediaImage($request->file('file_name'),$request->type);
                }*/
				
				$media->name = $request->name;
                
				if($request->type==1){
					$files_name1 = mediaDocument($request->file_name,$request->extension);
					$media->image = $files_name1;
				}else{
					$files_name2 = mediaImage($request->file_name,$request->type);
					$media->image = $files_name2;
				}
                
                $media->status = '1';
                $media->type = $request->type;
                $media->save();

                if($request->type == "1")
                {
                    return response()->json([
                        'success' => true,
                        'message' => 'Your documents have been saved.'
                    ]);
                }
                else
                {
                    return response()->json([
                        'success' => true,
                        'message' => 'Your photo has been saved.'
                    ]);
                }
                

                /*$folderPath = "media/";
            
                $base64Image = explode(";base64,", $request->files);
                $explodeImage = explode("image/", $base64Image[0]);
                $imageType = $explodeImage[1];
                $image_base64 = base64_decode($base64Image[1]);
                $file = $folderPath . time() . '. '.$imageType;
                file_put_contents($file, $image_base64);*/
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function getMedia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'type' => 'required'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                return response()->json([
                    'success' => true,
                    'media' => $user->media()->doesnthave('jobmedia')->where('status', '=', 1)->where('type','=',$request->type)->get()
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    } 

	public function getMediaByJobId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'type' => 'required'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);
		
        try
        {
            if($user)
            { 
				$jobmedia ='';
				
				if($request->job_id){
					$jobmedia = Media::with('jobmedia')
						->whereHas('jobmedia', function ($query) use ($request, $user) {
							$query->where('job_id', '=', $request->job_id);
							$query->where('user_id', '=', $user->id);
						})
						->where('status', '=', 1)
						->where('type', '=', $request->type)
						->where('user_id', '=', $user->id)
						->get();
					
				}else{
					$jobmedia= $user->media()->doesnthave('jobmedia')->where('status', '=', 1)->where('type','=',$request->type)->get();
				}
				
                return response()->json([
                    'success' => true,
                    'media' => $jobmedia
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function deleteMedia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'id' => 'required',
            'type' => 'required'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $delete_stage = $user->media()->where('id', '=', $request->id)->delete();

                if($delete_stage)
                {   
                    if($request->type == "1")
                    {
                        return response()->json([
                            'success' => true, 
                            'message' => 'Your document has been delete successfully.',
                        ]);
                    }
                    else
                    {
                        return response()->json([
                            'success' => true, 
                            'message' => 'Your picture has been delete successfully.',
                        ]);
                    }
                    
                }
                else
                {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Unable to delete Media.Please try again or contact to admin.',
                    ]);
                }
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function addContact(Request $request)
    {
        if($request->type)
        {
            if($request->type == "1" || $request->type == "7")
            {
                $validator = Validator::make($request->all(), 
                [ 
                   'token' => 'required',
                   
                   'name' => 'required',
                   'mobile' => 'required',
                   'email' => 'required',
                   'address' => 'required',
                   'city' => 'required',
                   'state' => 'required',
                   'pincode' => 'required',
                  
                 /*   'jobnotepad' => 'required',
                   'punchlist' => 'required',
                   'stage' => 'required',
                   'contact' => 'required',
                   'document' => 'required',
				   'calendar' => 'required',
                   'pictures' => 'required', */
                ]);
            }
            else if($request->type == "2" || $request->type == "4" || $request->type == "5" || $request->type == "6")
            {
                $validator = Validator::make($request->all(), 
                [ 
                   'token' => 'required',
                   
                   'name' => 'required',
                   'business_name' => 'required',
      
                   'mobile' => 'required',
                   'email' => 'required',
                   'address' => 'required',
                   'city' => 'required',
                   'state' => 'required',
                   'pincode' => 'required',
                   
                   /* 'jobnotepad' => 'required',
                   'punchlist' => 'required',
                   'stage' => 'required',
                   'contact' => 'required',
                   'document' => 'required',
				   'calendar' => 'required',
                   'pictures' => 'required', */
                ]);
            }
            else if($request->type == "3")
            {
                $validator = Validator::make($request->all(), 
                [ 
                   'token' => 'required',
             
                   'name' => 'required',
                   
                   'mobile' => 'required',
                   'email' => 'required',
                   'address' => 'required',
                   'city' => 'required',
                   'state' => 'required',
                   'pincode' => 'required',
                   
                   /* 'jobnotepad' => 'required',
                   'punchlist' => 'required',
                   'stage' => 'required',
                   'contact' => 'required',
                   'document' => 'required',
				   'calendar' => 'required',
                   'pictures' => 'required', */
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'The Type is not valid.',
                ]);
            }

            if ($validator->fails()) 
            {
                return response()->json(['error' => $validator->messages()]);
            }

            $user = JWTAuth::authenticate($request->token);

            try
            {
                if($user)
                {
					
					$contact_user = Contact::where('user_id','=',$user->id)->where('email','=',$request->email)->first();
					if($contact_user){
						return response()->json([
							'success' => true,
							'message' => 'Contact user already exist.Please try with another email id',
						]);	
						
					}else{
						
                    $contact = $user->contact()->make();
					if($request->email){
						$contact_user = User::where('email','=',$request->email)->first();
						if($contact_user){
							$contact->contact_user_id = $contact_user->id;
							$contact_user_id = $contact_user->id;
						}
					}
                   if($request->profile_pic){
						$frontimage = str_replace("data:image/jpeg;base64,", '', $request->profile_pic);
						$frontimage1 = str_replace(" ","+",$frontimage);
						$files_name = time() . '.jpeg';
						file_put_contents($files_name, base64_decode($frontimage1));
						$contact->profile_pic = $files_name;
				   }
                    $contact->name = $request->name;
                    $contact->mobile = $request->mobile;
                    $contact->email = $request->email;
                    $contact->address = $request->address;
                    $contact->city = $request->city;
                    $contact->state = $request->state;
                    $contact->pincode = $request->pincode;
                    /*$contact->shared_contact = $request->shared_contact;*/
					/* if($request->contact_notes){
						$contact->contact_notes = $request->contact_notes;
					} */
					if(!empty($request->contact_notes) && $request->contact_notes !=null && $request->contact_notes !='null' ){
						$contact->contact_notes = $request->contact_notes;
					}else{
						$contact->contact_notes ='' ;
					}
                    $contact->status = '0';
                    $contact->type = $request->type;

                    if($request->type == "2" || $request->type == "4" || $request->type == "5" || $request->type == "6")
                    {
                        $contact->business_name = $request->business_name;
						if($request->license_no){
							$contact->license_no = $request->license_no;
						}
						if($request->trade){
							$contact->trade = $request->trade;
						}
                    }
                    else if($request->type == "3")
                    {
						if($request->social_security_no){
							$contact->social_security_no = $request->social_security_no;
						}
						if($request->gps_tracker){
							$contact->gps_tracker = $request->gps_tracker;
						}
						if($request->trade){
							$contact->trade = $request->trade;
						}
                    }
					
					
                    $contact->save();

                   /*  $contactshared = $user->contactshared()->make();
                    $contactshared->contact_id = $contact->id;
                    $contactshared->jobnotepad = $request->jobnotepad;
                    $contactshared->punchlist = $request->punchlist;
                    $contactshared->stage = $request->stage;
                    $contactshared->contact = $request->contact;
                    $contactshared->document = $request->document;
					$contactshared->calendar= $request->calendar;
                    $contactshared->pictures = $request->pictures;
                    $contactshared->save(); */



					//***** Update user credits
					
					$user_id = $user->id;
					$user_credits = $user->credit_contact;
					$usr = User::findOrFail($user_id);
						// Check if the user has more than 0 credits
						if ($user_credits > 0) {
							$usr->credit_contact = $user_credits - 1;
							$usr->save();
						} else {
							return response()->json([
									'success' => false,
									'message' => 'User does not have enough credits.',
								]);
						}
					
                   
					$from_email = Config::get('mail.from.address');
					
					//** Contractor Details
					$contractor_email = $user->email;
					$contractor_name = $user->name;
					$owner_name = $user->name;
					
					
					//** Contact Details
					
					$contact_firstname = $request->name;
					$contact_email =$request->email;
					$contactID = $contact->id;
					$subject = "Invitation to Join See Job Run App from Contractor ".$contractor_name;
					if(@$contact_user_id){
						//notification send	
						
						/* $msg["title"] = "New Contact Added";
						$msg["body"] = "You have added in a ". $user->name. " user list.";
						$msg['type'] = "Contact";
						$msg['client_id'] = $contact_user_id;
						$msg['user_type'] = $this->user_type($request->type);
						$msg['move'] = 'Home';
						$this->sendNotification($user->id, $msg); */
						
						
							$body = @Template::where('type', 9)->orderBy('id', 'DESC')->first()->content;
							$content = array('contact_firstname' => $contact_firstname,'contact_id'=> $contactID,'contractor_id' => $user_id,'contractor_name'=>$contractor_name);
							foreach ($content as $key => $parameter) 
							{
								$body = str_replace('{{' . $key . '}}', $parameter, $body);
							}
							if($from_email)
							{
								Mail::send('emails.name', ['template' => $body, 'contact_firstname' => $contact_firstname,'contact_id'=> $contactID, 'contractor_id' => $user_id,'contractor_name'=>$contractor_name], function ($m) use ($from_email, $contact_email, $contact_firstname,$contactID,$contractor_name,$subject) 
								{
									$m->from($from_email, 'See Job Run');

									$m->to($contact_email, $contact_firstname)->subject($subject);
								});
							}
						
						
					}else{
						
						$body = @Template::where('type', 10)->orderBy('id', 'DESC')->first()->content;
						$content = array('contact_firstname' => $contact_firstname,'contact_id'=> $contactID,'contractor_id' => $user_id,'contractor_name'=>$contractor_name);
						foreach ($content as $key => $parameter) 
						{
							$body = str_replace('{{' . $key . '}}', $parameter, $body);
						}
						if($from_email)
						{
							Mail::send('emails.name', ['template' => $body, 'contact_firstname' => $contact_firstname,'contact_id'=> $contactID, 'contractor_id' => $user_id,'contractor_name'=>$contractor_name,'owner_name'=>$owner_name], function ($m) use ($from_email, $contact_email, $contact_firstname,$contactID,$contractor_name,$owner_name,$subject) 
							{
								$m->from($from_email, 'See Job Run');
								$m->to($contact_email, $contact_firstname)->subject($subject);
							});
						}
						
							/* $from_email = Config::get('mail.from.address');
							$email = $request->email;
							$name = $request->name;
							$owner_name = $user->name;
							$subject = $owner_name." has added you in his Team";

							$body = @Template::where('type', 5)->orderBy('id', 'DESC')->first()->content;
							$content = array('name' => $name, 'user_id' => $user_id,'owner_name'=>$owner_name);
							foreach ($content as $key => $parameter) 
							{
								$body = str_replace('{{' . $key . '}}', $parameter, $body);
							}
							if($from_email)
							{
								Mail::send('emails.name', ['template' => $body, 'name' => $name, 'user_id' => $user_id,'owner_name'=>$owner_name], function ($m) use ($from_email, $email, $name,$owner_name, $subject) 
								{
									$m->from($from_email, 'See Job Run');

									$m->to($email, $name)->subject($subject);
								});
							}  */
					}
                    return response()->json([
                        'success' => true,
                        'message' => 'Your Contact has been Saved Successfully.'
                   ]);
				  }
                }
                else
                {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token is not valid. please contact to the admin.',
                    ]);
                }
            }
            catch (\Exception $e) 
            {
                return response()->json(
                [
                    'success' => false,
                    'message' =>  $e->getMessage(),
                ]);
            }
        }
        else
        {
            return response()->json([
                'success' => false,
                'message' => 'The Type field is required.',
            ]);
        }
    }
	
    public function addContactNew(Request $request)
    {
        if($request->type)
        {
            if($request->type == "1" || $request->type == "7")
            {
                $validator = Validator::make($request->all(), 
                [ 
                   'token' => 'required',
                   
                   'name' => 'required',
                   'mobile' => 'required',
                   'email' => 'required',
                   
                ]);
            }
            else if($request->type == "2" || $request->type == "4" || $request->type == "5" || $request->type == "6")
            {
                $validator = Validator::make($request->all(), 
                [ 
                   'token' => 'required',
                   
                   'name' => 'required',
                   'business_name' => 'required',
      
                   'mobile' => 'required',
                   'email' => 'required',
                   

                ]);
            }
            else if($request->type == "3")
            {
                $validator = Validator::make($request->all(), 
                [ 
                   'token' => 'required',
             
                   'name' => 'required',
                   
                   'mobile' => 'required',
                   'email' => 'required',
                   
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'The Type is not valid.',
                ]);
            }

            if ($validator->fails()) 
            {
                return response()->json(['error' => $validator->messages()]);
            }

            $user = JWTAuth::authenticate($request->token);

            try
            {
                if($user)
                {
					
					$contact_user = Contact::where('user_id','=',$user->id)->where('email','=',$request->email)->first();
					if($contact_user){
						return response()->json([
							'success' => true,
							'message' => 'Contact user already exist.Please try with another email id',
						]);	
						
					}else{
						
                    $contact = $user->contact()->make();
					if($request->email){
						$contact_user = User::where('email','=',$request->email)->first();
						if($contact_user){
							$contact->contact_user_id = $contact_user->id;
							$contact_user_id = $contact_user->id;
						}
					}
                   if($request->profile_pic){
						$frontimage = str_replace("data:image/jpeg;base64,", '', $request->profile_pic);
						$frontimage1 = str_replace(" ","+",$frontimage);
						$files_name = time() . '.jpeg';
						file_put_contents($files_name, base64_decode($frontimage1));
						$contact->profile_pic = $files_name;
				   }
                    $contact->name = $request->name;
                    $contact->mobile = $request->mobile;
                    $contact->email = $request->email;
                    $contact->address = $request->address;
                    $contact->city = $request->city;
                    $contact->state = $request->state;
                    $contact->pincode = $request->pincode;
                 
					/* if(!empty($request->contact_notes) && $request->contact_notes !=null && $request->contact_notes !='null' ){
						$contact->contact_notes = $request->contact_notes;
					}else{
						$contact->contact_notes ='' ;
					} */
					if (!empty($request->contact_notes) && $request->contact_notes !== 'null') {
						$contact->contact_notes = $request->contact_notes;
					} else {
						$contact->contact_notes = '';
					}

                    $contact->status = '0';
                    $contact->type = $request->type;

                    if($request->type == "2" || $request->type == "4" || $request->type == "5" || $request->type == "6")
                    {
                        $contact->business_name = $request->business_name;
						if($request->license_no){
							$contact->license_no = $request->license_no;
						}
						if($request->trade){
							$contact->trade = $request->trade;
						}
                    }
                    else if($request->type == "3")
                    {
						if($request->social_security_no){
							$contact->social_security_no = $request->social_security_no;
						}
						if($request->gps_tracker){
							$contact->gps_tracker = $request->gps_tracker;
						}
						if($request->trade){
							$contact->trade = $request->trade;
						}
                    }

					//***** Update user credits
					
					$user_id = $user->id;
					$user_credits = $user->credit_contact;
					$usr = User::findOrFail($user_id);
						// Check if the user has more than 0 credits
						if ($user_credits > 0) {
							$usr->credit_contact = $user_credits - 1;
							$usr->save();
							$contact->save();
							$from_email = Config::get('mail.from.address');
							
							//** Contractor Details
							$contractor_email = $user->email;
							$contractor_name = $user->name;
							$owner_name = $user->name;
							
							//** Contact Details
							
							$contact_firstname = $request->name;
							$contact_email =$request->email;
							$contactID = $contact->id;
							$subject = "Invitation to Join See Job Run App from Contractor ".$contractor_name;
							$phoneNumber = $request->mobile;
							$cleanedPhoneNumber = str_replace(['(', ')', '-', ' '], '', $phoneNumber);
							$receiverNumber = $cleanedPhoneNumber;
							if(@$contact_user_id){
								

									$body = @Template::where('type', 12)->orderBy('id', 'DESC')->first()->content;
									$content = array('contact_firstname' => $contact_firstname,'contact_id'=> $contactID,'contractor_id' => $user_id,'contractor_name'=>$contractor_name);
									foreach ($content as $key => $parameter) 
									{
										$body = str_replace('{{' . $key . '}}', $parameter, $body);
									}
									if($from_email)
									{
                                    try {
										Mail::send('emails.name', ['template' => $body, 'contact_firstname' => $contact_firstname,'contact_id'=> $contactID, 'contractor_id' => $user_id,'contractor_name'=>$contractor_name], function ($m) use ($from_email, $contact_email, $contact_firstname,$contactID,$contractor_name,$subject) 
										{
											$m->from($from_email, 'See Job Run');

											$m->to($contact_email, $contact_firstname)->subject($subject);
										});
                                        } catch (\Exception $e) {
                                            // Log the error but do not show it to the user
                                            //Log::error('Error sending email: ' . $e->getMessage());
                                        }
									}
								
								// text messages
									
									$message = "Hi {$contact_firstname},\n\n{$contractor_name} added you to SEE JOB RUN for project management.\n\nThank you,\nAdmin See Job Run";

									
										$account_sid = getenv("TWILIO_SID");
										$auth_token = getenv("TWILIO_TOKEN");
										$twilio_number = getenv("TWILIO_FROM");
									
										$client = new Client($account_sid, $auth_token);
										$client->messages->create($receiverNumber, [
											'from' => $twilio_number, 
											'body' => $message]);
											
									
									
							}else{
								
								$body = @Template::where('type', 13)->orderBy('id', 'DESC')->first()->content;
								$content = array('contact_firstname' => $contact_firstname,'contact_id'=> $contactID,'contractor_id' => $user_id,'contractor_name'=>$contractor_name);
								foreach ($content as $key => $parameter) 
								{
									$body = str_replace('{{' . $key . '}}', $parameter, $body);
								}
								if($from_email)
								{
                                    try{
									Mail::send('emails.name', ['template' => $body, 'contact_firstname' => $contact_firstname,'contact_id'=> $contactID, 'contractor_id' => $user_id,'contractor_name'=>$contractor_name,'owner_name'=>$owner_name], function ($m) use ($from_email, $contact_email, $contact_firstname,$contactID,$contractor_name,$owner_name,$subject) 
									{
										$m->from($from_email, 'See Job Run');
										$m->to($contact_email, $contact_firstname)->subject($subject);
									});
                                    } catch (\Exception $e) {
                                        // Log the error but do not show it to the user
                                        //Log::error('Error sending email: ' . $e->getMessage());
                                    }
								}
						
							// text messages
									
								$message = "Hi {$contact_firstname},\n\n{$contractor_name} added you to SEE JOB RUN for project management.\nPlease download the application from the following links:\n\nAndroid – https://play.google.com/store/apps/details?id=com.clockk\niPhone – https://apps.apple.com/in/app/see-job-run/id6443558941\n\nThank you,\nAdmin See Job Run";

								
									$account_sid = getenv("TWILIO_SID");
									$auth_token = getenv("TWILIO_TOKEN");
									$twilio_number = getenv("TWILIO_FROM");
								
									$client = new Client($account_sid, $auth_token);
									$client->messages->create($receiverNumber, [
										'from' => $twilio_number, 
										'body' => $message]);
										
								
								
			
							}
							
						 return response()->json([
								'success' => true,
								'message' => 'Your Contact has been Saved Successfully.'
						   ]);	
							
						} else {
							return response()->json([
									'success' => true,
									'message' => 'You don’t have sufficient credit. Please purchase more credit before proceeding.',
								]);
						}
				  }
                }
                else
                {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token is not valid. please contact to the admin.',
                    ]);
                }
            }
            catch (\Exception $e) 
            {
                return response()->json(
                [
                    'success' => false,
                    'message' =>  $e->getMessage(),
                ]);
            }
        }
        else
        {
            return response()->json([
                'success' => false,
                'message' => 'The Type field is required.',
            ]);
        }
    }
	
    public function getallContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                if($request->type != "")		
                {
					if($request->type==1){
						 $getcontact = $user->contact()->with(['contactshared'])->where('type','=',$request->type)->get();

					}else{
						$getcontact = $user->contact()->with(['contactshared'])->where('status', '=', 1)->where('type','=',$request->type)->get();
					}
				}
                else
                {
                    //$getcontact = $user->contact()->with(['contactshared'])->where('type','!=',1)->where('status', '=', 1)->get(); 
					$getcontact = $user->contact()->with(['contactshared'])->where('status', '=', 1)->get();
                }

                return response()->json([
                    'success' => true,
					'contact' => $getcontact
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }
    public function getAllTypeContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                if($request->type != "")
                {
                    $getcontact = $user->contact()->with(['contactshared'])->where('status', '=', 1)->where('type','=',$request->type)->get();
                }
                else
                {
                    $getcontact = $user->contact()->with(['contactshared'])->get();
                }

                return response()->json([
                    'success' => true,
					 'credit_contact' => $user->credit_contact,
                    'contact' => $getcontact
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }
	
    public function getContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'id' => 'required'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                return response()->json([
                    'success' => true,
                    'contact' => $user->contact()->with(['contactshared'])->where('id', '=', $request->id)->get()
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }
	public function UpdateContactProfile(Request $request){
		
		$validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'id' => 'required',
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }  
		
        $user = JWTAuth::authenticate($request->token);	
		if($user){
				$contact = Contact::find($request->id);
                if($request->name){
                    $contact->name = $request->name;
                }
                if($request->mobile){
                    $contact->mobile = $request->mobile;
                }
                if($request->address){
                    $contact->address = $request->address;
                }
                if($request->city){
                    $contact->city = $request->city;
                }
                if($request->state){
                    $contact->state = $request->state;
                }
                if($request->pincode){
                    $contact->pincode = $request->pincode;
                }
                if($request->contact_notes){
                    $contact->contact_notes = $request->contact_notes;
                }
                if($request->business_name){
                    $contact->business_name = $request->business_name;
                }
                if($request->license_no){
                    $contact->license_no = $request->license_no;
                }
                if($request->trade){
                    $contact->trade = $request->trade;
                }
                if($request->social_security_no){
                    $contact->social_security_no = $request->social_security_no;
                }
               
                
				if($request->profile_pic){
					$frontimage = str_replace("data:image/jpeg;base64,", '', $request->profile_pic);
					$frontimage1 = str_replace(" ","+",$frontimage);
					$files_name = time() . '.jpeg';
					file_put_contents($files_name, base64_decode($frontimage1));
					$contact->profile_pic = $files_name;
					
				}
                $contact->save();
				
				
					/* $contactshared = Contactshared::where('contact_id','=',$contact->id)->first();
					//if($request->jobnotepad){
						$contactshared->jobnotepad = $request->jobnotepad;
					//}
					//if($request->punchlist){
						$contactshared->punchlist = $request->punchlist;
					//}
					//if($request->stage){
						$contactshared->stage = $request->stage;
					//}
					//if($request->contact){
						$contactshared->contact = $request->contact;
					//}
					//if($request->document){
						$contactshared->document = $request->document;
					//}
					//if($request->calendar){
						$contactshared->calendar= $request->calendar;
					//}
					//if($request->pictures){
						$contactshared->pictures = $request->pictures;
					//}
					$contactshared->save(); */
					
					return response()->json([
                        'success' => true,
                        'message' => 'You have updated Contact Successfully.'
                    ]);
		}else{
			 return response()->json(
				[
					'success' => false,
					'message' =>  $e->getMessage(),
				]);
		}
			
	}
	public function UpdateContactSharedPermission(Request $request){
		
		$validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'contact_id' => 'required',
		   'job_id' => 'required',
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }  
		
        $user = JWTAuth::authenticate($request->token);	
		if($user){
				
				
					$contactshared = Contactshared::where('contact_id','=',$request->contact_id)->where('job_id','=',$request->job_id)->first();
					//if($request->jobnotepad){
						$contactshared->jobnotepad = $request->jobnotepad;
					//}
					//if($request->punchlist){
						$contactshared->punchlist = $request->punchlist;
					//}
					//if($request->stage){
						$contactshared->stage = $request->stage;
					//}
					//if($request->contact){
						$contactshared->contact = $request->contact;
					//}
					//if($request->document){
						$contactshared->document = $request->document;
					//}
					//if($request->calendar){
						$contactshared->calendar= $request->calendar;
					//}
					//if($request->pictures){
						$contactshared->pictures = $request->pictures;
					//}
					$contactshared->general = $request->general;
                    $contactshared->todo = $request->todo;
					$contactshared->save();
					
					return response()->json([
                        'success' => true,
                        'message' => 'You have updated Contactshared Permission Successfully.'
                    ]);
		}else{
			 return response()->json(
				[
					'success' => false,
					'message' =>  $e->getMessage(),
				]);
		}
			
	}
	public function deleteContact(Request $request){
		
		$validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'id' => 'required',
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }  
		
        $user = JWTAuth::authenticate($request->token);	
		
		if($user){
			$contact = Contact::find($request->id);
			$exiting_susc = Contact::where('id','=',$request->id)->first();
			$existing_susb_id = $exiting_susc->subscription_id;
			$contact_type = $exiting_susc->type;
			if(@$contact_type==1){
				$assignedjobCount = Job::where('client_id', $contact->id)->count();
				if($assignedjobCount>0){
					return response()->json([
						'success' => true,
						'message' => 'This client cannot be deleted as they are associated with existing jobs.',
					]);
				}else{
					if(@$existing_susb_id){
						Stripe\Stripe::setApiKey(config('app.stripe_secret'));
						$current_subscription = \Stripe\Subscription::retrieve($existing_susb_id);
						if($current_subscription->status=='active'){
							$current_subscription->cancel();	
							$contact->delete();
							 return response()->json([
									'success' => true,
									'message' => 'You have successfully deleted contact',
								]);
						}else{
							$contact->delete();
							 return response()->json([
								'success' => true,
								'message' => 'You have successfully deleted contact',
							]);
						}
					
						
					}else{
						
						$contact->delete();
						 return response()->json([
							'success' => true,
							'message' => 'You have successfully deleted contact',
						]);
					}
					
				}
			}else{
					if(@$existing_susb_id){
							Stripe\Stripe::setApiKey(config('app.stripe_secret'));
							$current_subscription = \Stripe\Subscription::retrieve($existing_susb_id);
							if($current_subscription->status=='active'){
								$current_subscription->cancel();	
								$contact->delete();
								 return response()->json([
									'success' => true,
									'message' => 'You have successfully deleted contact',
								]);
							}else{
								$contact->delete();
								 return response()->json([
									'success' => true,
									'message' => 'You have successfully deleted contact',
								]);
							}
						 return response()->json([
							'success' => true,
							'message' => 'You have successfully deleted contact',
						]);	
						
					}else{
						
						$contact->delete();
						 return response()->json([
							'success' => true,
							'message' => 'You have successfully deleted contact',
						]);
					}			
			}
			
			
			
		}else{
			 return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
		}
	}
	
	public function addJobStageByJobId(Request $request){
		 $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'job_id' => 'required',
		   'stage_id' => 'required'
           
         
        ]);
		
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   

         $user = JWTAuth::authenticate($request->token);
		  try
        {
            if($user)
            {
				
				$jobstage = $user->jobstage()->make();
				$jobstage->job_id = $request->job_id;
				$jobstage->stage_id = $request->stage_id;
				$jobstage->template_id = 0;
				$jobstage->save();
				
				/*  foreach($request->stage as $stagekey) 
				{
					$jobstage = $user->jobstage()->make();

					$jobstage->job_id = $request->job_id;
					$jobstage->stage_id = $stagekey;
					$jobstage->save();
				} */
				
				/*   if($request->templateid != 0 && $request->templateid != null)
                {
                    $template_exits = $user->stagetemplate()->where('id', '=', $request->templateid)->first();
                    $getStage = $template_exits->stage_id;
                    $arrayData = unserialize($getStage);

                    foreach($arrayData as $stagekey) 
                    {
                        if($stagekey != 0)
                        {
                            $jobstage = $user->jobstage()->make();

                            $jobstage->job_id = $request->job_id;
                            $jobstage->stage_id = $stagekey;
                            $jobstage->template_id = $request->templateid;
                            $jobstage->save();
                        }
                    }
                } */
				
				return response()->json([
				'success' => true,
				'message' => 'Your Job Stage has been Saved Successfully.'
				]);
				
			}else{
				return response()->json([
					'success' => true,
					'message' => 'Your Job Stage has been Saved Successfully.'
				]);
			}
		}catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
		 
	}	
	public function addJobDocumentByJobId(Request $request){
		 $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'job_id' => 'required',
           
         
        ]);
		
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   

         $user = JWTAuth::authenticate($request->token);
		  try
        {
            if($user)
            {
				if($request->document != null)
                {
                    foreach($request->document as $documentkey) 
                    {
                        $jobmedia = $user->jobmedia()->make();

                        $jobmedia->job_id = $request->job_id;
                        $jobmedia->media_id = $documentkey;
                        $jobmedia->save();
                    } 
                }
				
				return response()->json([
				'success' => true,
				'message' => 'Your Job Document has been Saved Successfully.'
				]);
				
			}else{
				return response()->json([
					'success' => true,
					'message' => 'Your Job Document has been Saved Successfully.'
				]);
			}
		}catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
		 
	}	
	
	public function addJobPictureByJobId(Request $request){
		 $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'job_id' => 'required',
           
         
        ]);
		
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   

         $user = JWTAuth::authenticate($request->token);
		  try
        {
            if($user)
            {
				
				
				  if($request->picture != null)
					{
						foreach($request->picture as $picturekey) 
						{
							$jobmedia = $user->jobmedia()->make();

							$jobmedia->job_id = $request->job_id;
							$jobmedia->media_id = $picturekey;
							$jobmedia->save();
						}
					}
				return response()->json([
				'success' => true,
				'message' => 'Your Job Picture has been Saved Successfully.'
				]);
				
			}else{
				return response()->json([
					'success' => true,
					'message' => 'Your Job Picture has been Saved Successfully.'
				]);
			}
		}catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
		 
	}


	public function addJobContactsByJobId(Request $request){
		 $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'job_id' => 'required',
           
         
        ]);
		
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   

         $user = JWTAuth::authenticate($request->token);
		  try
        {
            if($user)
            {
			  if($request->contact != null){
				foreach($request->contact as $contactkey) 
				 {
                    $jobcontact = $user->jobcontact()->make();
                    $jobcontact->job_id = $request->job_id;
                    $jobcontact->contact_id = $contactkey;
                    $jobcontact->save();
					
					// add default value in shared contact table
					$contactshared = Contactshared::make();
                    $contactshared->user_id = $user->id;
					$contactshared->contact_id = $contactkey;
                    $contactshared->job_id = $request->job_id;
					$contactshared->jobnotepad = 0;
                    $contactshared->punchlist = 0;
                    $contactshared->stage = 0;
                    $contactshared->contact = 0;
                    $contactshared->document = 0;
					$contactshared->calendar= 0;
                    $contactshared->pictures = 0;
                    $contactshared->save();
					
					$job = Job::where('id','=',$request->job_id)->first();
					$contacts = Contact::find($contactkey);
					$contact_user_id = $contacts->contact_user_id;
					if($contact_user_id){
						//notification send	
						$msg["title"] = "New Job";
						$msg["body"] = "You are invited to join this job ".$job->name;
						$msg['type'] = "job";
						$msg['client_id'] = $contacts->contact_user_id;
						$msg['user_type'] = $this->user_type($contacts->type);
						$msg['move'] = 'Home';
						$this->sendNotification($user->id, $msg);
						
					}
					/* else{
							$from_email = Config::get('mail.from.address');
							$email = $contacts->email;
							$name = $contacts->name;
							$subject = "New job added";

							$body = @Template::where('type', 5)->orderBy('id', 'DESC')->first()->content;
							
							$content = array('name' => $name);
							foreach ($content as $key => $parameter) 
							{
								$body = str_replace('{{' . $key . '}}', $parameter, $body);
							}

							Mail::send('emails.name', ['template' => $body, 'name' => $name], function ($m) use ($from_email, $email, $name, $subject) 
							{
								$m->from($from_email, 'See Job Run');

								$m->to($email, $name)->subject($subject);
							});

						
					} */
					
					
					
                }
			}
				return response()->json([
				'success' => true,
				'message' => 'Your Job Contact has been Saved Successfully.'
				]);
				
			}else{
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
			}
		}catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
		 
	}
	
  public function addJobInpectionByJobId(Request $request){
		 $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'job_id' => 'required',
           
         
        ]);
		
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   

         $user = JWTAuth::authenticate($request->token);
		  try
        {
            if($user)
            {
				
				
				 if($request->inspection){
						
						$jobinspection1 = $user->jobinspection()->make();
						$jobinspection1->job_id = $request->job_id;
						$jobinspection1->contact_id = $request->inspection;
						$jobinspection1->save();
					}
				return response()->json([
				'success' => true,
				'message' => 'Your Job Inspection has been Saved Successfully.'
				]);
				
			}else{
				return response()->json([
					'success' => true,
					'message' => 'Your Job Inspection has been Saved Successfully.'
				]);
			}
		}catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
		 
	}
	
    public function addJob(Request $request)
    {
        $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'name' => 'required',
           'mobile' => 'required',
           'client_id' => 'required',
           'job_type' => 'required',
           'address' => 'required',
           'city' => 'required',
           'state' => 'required',
           'pincode' => 'required',
           'contract_status' => 'required',
           'stage' => 'required',
           'inspection' => 'required',
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   

            $user = JWTAuth::authenticate($request->token);
        try
        {
            if($user)
            {
				$job_name = $request->name;
                $job = $user->job()->make();

                $job->name = $request->name;
                $job->mobile = $request->mobile;
                $job->client_id = $request->client_id;
				if($request->gate_no){
					$job->gate_no = $request->gate_no;
				}
                $job->job_type = $request->job_type;
				if($request->permit_no){
					$job->permit_no = $request->permit_no;
				}
                $job->address = $request->address;
                $job->city = $request->city;
                $job->state = $request->state;
                $job->pincode = $request->pincode;
                $job->contract_status = $request->contract_status;
                $job->status = '1';
                $job->save();
				$jobname = $request->name;
                $job_id = $job->id;
				
                foreach($request->stage as $stagekey) 
                {
                    $jobstage = $user->jobstage()->make();

                    $jobstage->job_id = $job_id;
                    $jobstage->stage_id = $stagekey;
                    $jobstage->save();
                }

                foreach($request->document as $documentkey) 
                {
                    $jobmedia = $user->jobmedia()->make();

                    $jobmedia->job_id = $job_id;
                    $jobmedia->media_id = $documentkey;
                    $jobmedia->save();
                } 

                foreach($request->picture as $picturekey) 
                {
                    $jobmedia = $user->jobmedia()->make();

                    $jobmedia->job_id = $job_id;
                    $jobmedia->media_id = $picturekey;
                    $jobmedia->save();
                }

                foreach($request->contact as $contactkey) 
                {
                    $jobcontact = $user->jobcontact()->make();
                    $jobcontact->job_id = $job_id;
                    $jobcontact->contact_id = $contactkey;
                    $jobcontact->save();
					
					$contacts = Contact::find($contactkey);
					$contact_user_id = $contacts->contact_user_id;
					if($contact_user_id){
						//notification send	
						$msg["title"] = "New Job";
						$msg["body"] = "You are invited to join this job ".$jobname;
						$msg['type'] = "job";
						$msg['client_id'] = $contacts->contact_user_id;
						$msg['user_type'] = $this->user_type($contacts->type);
						$msg['move'] = 'Home';
						$this->sendNotification($user->id, $msg);
						
					}else{
							$from_email = Config::get('mail.from.address');
							$email = $contacts->email;
							$name = $contacts->name;
							$subject = "New job added";

							$body = @Template::where('type', 5)->orderBy('id', 'DESC')->first()->content;
							
							$content = array('name' => $name);
							foreach ($content as $key => $parameter) 
							{
								$body = str_replace('{{' . $key . '}}', $parameter, $body);
							}
                            try{ 
                                Mail::send('emails.name', ['template' => $body, 'name' => $name], function ($m) use ($from_email, $email, $name, $subject) 
                                {
                                    $m->from($from_email, 'See Job Run');

                                    $m->to($email, $name)->subject($subject);
                                });
                            } catch (\Exception $e) {
                                // Log the error but do not show it to the user
                                //Log::error('Error sending email: ' . $e->getMessage());
                            }
						
					}
					
					
					
                }
				
				
				
               /* foreach($request->inspection as $inspectionkey) 
                {*/
				
					if($request->inspection){
						
						$jobinspection1 = $user->jobinspection()->make();
						$jobinspection1->job_id = $job_id;
						$jobinspection1->contact_id = $request->inspection;
						$jobinspection1->save();
					}
                /*}*/

				
				

                return response()->json([
                    'success' => true,
                    'message' => 'Your Job has been Saved Successfully.'
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }
	
	public function addHalfJob(Request $request){
		 $validator = Validator::make($request->all(), 
			[ 
			   'token' => 'required',
			   'name' => 'required',
			   'mobile' => 'required',
			   'client_id' => 'required',
			   'job_type' => 'required',
			   'address' => 'required',
			   'city' => 'required',
			   'state' => 'required',
			   'pincode' => 'required',
			   'contract_status' => 'required',
			  
			]);
	 
			if ($validator->fails()) 
			{  
				return response()->json(['error'=>$validator->errors()]); 
			}   
			$user = JWTAuth::authenticate($request->token);
			try
			{
				if($user)
				{
					$jobname = $request->name;
					$job = $user->job()->make();

					$job->name = $request->name;
					$job->mobile = $request->mobile;
					$job->client_id = $request->client_id;
					if($request->gate_no){
						$job->gate_no = $request->gate_no;
					}
					
					$job->job_type = $request->job_type;
					if($request->permit_no){
						$job->permit_no = $request->permit_no;
					}
					$job->address = $request->address;
					$job->city = $request->city;
					$job->state = $request->state;
					$job->pincode = $request->pincode;
					$job->contract_status = $request->contract_status;
					$job->status = '1';
					$job->save();
					
					$job_id = $job->id;
					if($request->inspection){
						$jobcontact = $user->jobinspection()->make();
						$jobcontact->job_id = $job_id;
						$jobcontact->contact_id = $request->inspection;
						$jobcontact->save();
					}
					
					$jobcontact = Jobcontacts::make();
					$jobcontact->user_id = $user->id;
					$jobcontact->job_id = $job_id;
					$jobcontact->contact_id = $request->client_id;
					$jobcontact->save();
					
					// add default value in shared contact table
					$contactshared = Contactshared::make();
                    $contactshared->user_id = $user->id;
					$contactshared->contact_id = $request->client_id;
                    $contactshared->job_id = $job_id;
					$contactshared->jobnotepad = 0;
                    $contactshared->punchlist = 0;
                    $contactshared->stage = 0;
                    $contactshared->contact = 0;
                    $contactshared->document = 0;
					$contactshared->calendar= 0;
                    $contactshared->pictures = 0; 
					$contactshared->general = 0;
                    $contactshared->todo = 0;
                    $contactshared->save();
				 // Send notification
				 
					$contacts = Contact::find($request->client_id);
					if($contacts){
						$contact_user_id = $contacts->contact_user_id;
						if($contact_user_id){
							//notification send	
							$msg["title"] = "New Job";
							$msg["body"] = "You are invited to join this job ".$jobname;
							$msg['type'] = "job";
							$msg['client_id'] = $contacts->contact_user_id;
							$msg['user_type'] = $this->user_type($contacts->type);
							$msg['move'] = 'Home';
							$this->sendNotification($user->id, $msg);
							
						}else{
								$from_email = Config::get('mail.from.address');
								$email = $contacts->email;
								$name = $contacts->name;
								$subject = "New job added";

								$body = @Template::where('type', 5)->orderBy('id', 'DESC')->first()->content;
								
								$content = array('name' => $name);
								foreach ($content as $key => $parameter) 
								{
									$body = str_replace('{{' . $key . '}}', $parameter, $body);
								}

                                if($from_email)
                                {
                                    try{
                                        Mail::send('emails.name', ['template' => $body, 'name' => $name], function ($m) use ($from_email, $email, $name, $subject) 
                                        {
                                            $m->from($from_email, 'See Job Run');

                                            $m->to($email, $name)->subject($subject);
                                        });
                                    } catch (\Exception $e) {
                                        // Log the error but do not show it to the user
                                        //Log::error('Error sending email: ' . $e->getMessage());
                                    }
                                }

							
						}
					}
				 return response()->json([
                    'success' => true,
					'job_id' =>$job_id,
                    'message' => 'Your  Job has been Saved Successfully.'
                ]);
				
				}else{
					return response()->json([
						'success' => false,
						'message' => 'Token is not valid. please contact to the admin.',
					]);
				}
			}catch (\Exception $e) 
			{
				return response()->json(
				[
					'success' => false,
					'message' =>  $e->getMessage(),
				]);
			}
	}
	public function archive_job(Request $request){
		 $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'job_id' => 'required',
		   'job_type' => 'required',
		   'status' => 'required'
           
        ]);
		
		 if ($validator->fails()) 
		{  
            return response()->json(['error'=>$validator->errors()]); 
        }   

        $user = JWTAuth::authenticate($request->token);
		try
        {
			if($user){
				$job = Job::find($request->job_id);
				$job->job_type = $request->job_type;
				$job->status = $request->status;
				$job->save();
				 return response()->json([
                    'success' => true,
                    'message' => 'You have successfully added this job in archive.'
                ]);
				
			}else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
		}catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
	}
	
	public function getArchiveJob(Request $request){
			 $validator = Validator::make($request->all(), 
				[ 
				   'token' => 'required',
				   'job_type' => 'required',
				   'status' => 'required'
				   
				]);
				 if ($validator->fails()) 
				{  
					return response()->json(['error'=>$validator->errors()]); 
				}   
				$user = JWTAuth::authenticate($request->token);
				 try
					{
						if($user)
						{
							$result = Job::where('job_type','=',$request->job_type)->where('status','=',$request->status)->get();
							
							 return response()->json([
								'success' => true,
								'Archive_jobs' => $result
							]); 
							
						}
						else
						{
							return response()->json([
								'success' => false,
								'message' => 'Token is not valid. please contact to the admin.',
							]);
						}
					}
					
				catch (\Exception $e) 
				{
					return response()->json(
					[
						'success' => false,
						'message' =>  $e->getMessage(),
					]);
				}
				
		
	}
	
    public function addTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'name' => 'required',
           'stage' => 'required',
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $stagetemplate = $user->stagetemplate()->make();

                $stagetemplate->name = $request->name;
                $stagetemplate->stage_id = serialize($request->stage);
                $stagetemplate->status = '1';
                $stagetemplate->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Your Stage Template has been Saved Successfully.'
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function getTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {       
            if($user)
            {
                if(empty($request->id))
                {
                    $stagetemplate = $user->stagetemplate()->where('status', '=', 1)->get();
                }
                else
                {
                    $stagetemplate = $user->stagetemplate()->where('status', '=', 1)->where('id','=',$request->id)->get();
                }
                
                //dd($stagetemplate);
                $data = array();
                $st = array();

                foreach($stagetemplate as $stagetempkey)
                {
                    $tmp = array();
                    $tmp['id'] = $stagetempkey->id;
                    $tmp['user_id'] = $stagetempkey->user_id;
                    $tmp['name'] = $stagetempkey->name; 

                    $stagedata = $user->stage()->whereIn('id', unserialize($stagetempkey->stage_id))->get();

                    foreach($stagedata as $stagedatakey)
                    {
                        $data1 = array("id" => $stagedatakey->id,"name" => $stagedatakey->name); 

                        array_push($st,$data1);
                    }

                    $tmp['stage'] = $st;

                    array_push($data,$tmp);

                    $st = array();
                }

                //dd($data);
                return response()->json([
                    'success' => true,
                    'stage_template' => $data
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function deleteTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'id' => 'required'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $delete_template = $user->stagetemplate()->where('id', '=', $request->id)->delete();

                if($delete_template)
                {   
                    return response()->json([
                        'success' => true, 
                        'message' => 'Template has been deleted successfully.',
                    ]);
                }
                else
                {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Unable to delete Template.Please try again or contact to admin.',
                    ]);
                }
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function getJob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

       $user_id = $user->id;
		
		//$contact_id = $user->contactuserid()->first()->id;
		
		$contact_id = $user->contactuserid()->where('contact_user_id','=',$user_id)->get();
		
		
        try
        {
            if($user)
            {
                if($request->job_id)
                {	$job_id = $request->job_id;
                    if($request->tab == "general")
                    {
                        $jobdata = job::with(['jobinspection','jobinspection.contact','contact','contact.contactshared'])->where('id','=',$request->job_id)->paginate(10);
						
						
						/* if(count($jobdatass)>0){
							$jobdata = job::with(['jobinspection','jobinspection.contact','contact'])->where('id','=',$request->job_id)->paginate(10);
						}else{
							$jobdata = job::with(['jobinspection','jobinspection.contact','contact'])->where('id','=',$request->job_id)->where('status','!=',2)->paginate(10);
						} */
					}
					
                    else if($request->tab == "stage")
                    {
                        $jobdata = job::with(['jobstage','jobstage.stage'])->where('id','=',$request->job_id)->paginate(10);
                    }
                    else if($request->tab == "document")
                    {
                        //$jobdata = job::with(['jobmedia','jobmedia.media'])->whereHas('jobmedia.media', function($q) {$q->where('type', '=', '1'); })->where('id','=',$request->job_id)->paginate(10);
                    
						$jobdata = job::with(['jobmedia', 'jobmedia.media'])
						->whereHas('jobmedia', function ($q) use ($request) {
							$q->where('job_id', '=', $request->job_id)
								->whereHas('media', function ($query) {
									$query->where('type', '=', 1);
								});
						})
						->where('id', '=', $request->job_id)
						->paginate(10);
					}
                    else if($request->tab == "picture")
                    {
                        $jobdata = job::with(['jobmedia','jobmedia.media'])->whereHas('jobmedia.media', function($q) {$q->where('type', '=', '2'); })->where('id','=',$request->job_id)->paginate(10);
                    }
                    else if($request->tab == "contact")
                    {
                        $jobdata = job::with(['jobcontact','jobcontact.contact','jobcontact.contact.contactshared'=> function ($query) use ($request) {
							$query->where('job_id', '=', $request->job_id);
						}])->where('id','=',$request->job_id)->paginate(10);
                    }
                    else if($request->tab == "punchlist")
                    {
						$jobcontacts = Jobcontacts::where('job_id','=',$request->job_id)->where('user_id','=',$user->id)->get();
						if(count(@$jobcontacts)>0){
							$jobdata = job::with(['jobinspection','jobinspection.contact','punchlist','punchlist.punchlistimg'])->where('id','=',$request->job_id)->paginate(10);
						}else{
								
								$contactss = Contact::where('contact_user_id','=',$user->id)->first();
								$ContactsharedPermission = Contactshared::where('contact_id','=',$contactss->id)->where('job_id','=',$request->job_id)->first();
								if($ContactsharedPermission->punchlist==1){
									
									$jobdata = job::with(['jobinspection','jobinspection.contact','punchlist.punchlistimg', 'punchlist' => function ($q) use ($user,$contactss,$job_id) {
											$q->where('user_id', "=", $user->id);
											$q->orwhere('contact_id', "=", $contactss->id);
											$q->orwhere('job_id', "=", $job_id);
										}])->where('id', '=', $request->job_id)->paginate(10);
									
								}else{
									
									$jobdata = job::with(['jobinspection','jobinspection.contact','punchlist.punchlistimg', 'punchlist' => function ($q) use ($user,$contactss,$job_id) {
											$q->where('user_id', "=", $user->id);
											$q->where('job_id', "=", $job_id);
											$q->orwhere('contact_id', "=", $contactss->id);
										}])->where('id', '=', $request->job_id)->paginate(10);
									
								}
								/* $jobcontacts = Jobcontacts::where('job_id','=',$request->job_id)->get();
								$data = array();	
								foreach($jobcontacts as $jobcontact){
									if($jobcontact->contact_id){
										$data['contactshared'] = Contact::with('contactshared1')->where('id','=',$jobcontact->contact_id)->where('contact_user_id','=', $user->id)->get();
									}
								}
								if(@$data['contactshared'][0]['contactshared1'][0]->punchlist==1){
								$jobdata = job::with(['jobinspection','jobinspection.contact','punchlist','punchlist.punchlistimg'])->where('id','=',$request->job_id)->paginate(10);
								}else{
								$jobdata = job::with(['jobinspection','jobinspection.contact','punchlist.punchlistimg', 'punchlist' => function ($q) use ($user) {$q->where('user_id', "=", $user->id);}])->where('id', '=', $request->job_id)->paginate(10);

								} */
							
						}
					
					}
					else if($request->tab == "task")
                    {	
				
							
							 $jobcontacts = Jobcontacts::where('job_id','=',$request->job_id)->where('user_id','=',$user->id)->get();
							if(count(@$jobcontacts)>0){
								
								$jobdata = job::with(['jobinspection','jobinspection.contact','taskassignment','taskassignment.taskassignmentimages'])->where('id','=',$request->job_id)->paginate(10);
							}else{
								$contactss = Contact::where('contact_user_id','=',$user->id)->first();
								$ContactsharedPermission = Contactshared::where('contact_id','=',$contactss->id)->where('job_id','=',$request->job_id)->first();
								if($ContactsharedPermission->jobnotepad==1){
										$jobdata = job::with(['jobinspection','jobinspection.contact','taskassignment.taskassignmentimages', 'taskassignment' => function ($q) use ($user,$contactss,$job_id) {
											$q->where('user_id', "=", $user->id);
											$q->orwhere('contact_id', "=", $contactss->id);
											$q->orwhere('job_id', "=", $job_id);
										}])->where('id', '=', $request->job_id)->paginate(10);
								}else{
									$jobdata = job::with(['jobinspection','jobinspection.contact','taskassignment.taskassignmentimages', 'taskassignment' => function ($q) use ($user,$contactss,$job_id) {
											$q->where('user_id', "=", $user->id);
											$q->where('job_id', "=", $job_id);
											$q->orwhere('contact_id', "=", $contactss->id);
										}])->where('id', '=', $request->job_id)->paginate(10);
								}
							} 
								
						
						/* 	$jobcontacts = Jobcontacts::where('job_id', '=', $request->job_id)
								->where('user_id', '=', $user->id)
								->get();

							if (count($jobcontacts) > 0) {
								$jobdata = job::with([
									'jobinspection',
									'jobinspection.contact',
									'taskassignment' => function ($q) {
										$q->orderBy('status', 'asc'); // Order by status (0 first, then 1)
									},
									'taskassignment.taskassignmentimages'
								])->where('id', '=', $request->job_id)->paginate(10);
							} else {
								$contactss = Contact::where('contact_user_id', '=', $user->id)->first();
								$ContactsharedPermission = Contactshared::where('contact_id', '=', $contactss->id)
									->where('job_id', '=', $request->job_id)
									->first();

								if ($ContactsharedPermission->jobnotepad == 1) {
									$jobdata = job::with([
										'jobinspection',
										'jobinspection.contact',
										'taskassignment' => function ($q) use ($user, $contactss, $job_id) {
											$q->where('user_id', "=", $user->id)
												->orWhere('contact_id', "=", $contactss->id)
												->orWhere('job_id', "=", $job_id)
												->orderBy('status', 'asc'); // Order by status (0 first, then 1)
										},
										'taskassignment.taskassignmentimages'
									])->where('id', '=', $request->job_id)->paginate(10);
								} else {
									$jobdata = job::with([
										'jobinspection',
										'jobinspection.contact',
										'taskassignment' => function ($q) use ($user, $contactss, $job_id) {
											$q->where('user_id', "=", $user->id)
												->where('job_id', "=", $job_id)
												->orWhere('contact_id', "=", $contactss->id)
												->orderBy('status', 'asc'); // Order by status (0 first, then 1)
										},
										'taskassignment.taskassignmentimages'
									])->where('id', '=', $request->job_id)->paginate(10);
								}
							} */

							// Ading task assignment order						
							
                    }
                    else
                    {
                        $jobdata = job::with(['user', 'jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->where('id','=',$request->job_id)->paginate(10);
                    }
                }
                else
                {
					
                  //$jobdata = $user->job()->with(['user', 'jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->paginate(10);
					if(count($contact_id) > 0)
					{ 
				
						$cont_id = $contact_id[0]->id;
						
						$invol_user = job::with(['user', 'jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->whereHas('jobcontact', function($q) use($cont_id) {$q->where('contact_id', '=', $cont_id); })->where('status','!=',2)->orderBy('contract_status', 'desc')->orderBy('id', 'desc')->get();
						
						//echo 'total->'.count($jobdata);
						//print_r($jobdata);
						
						//$jobdata = job::with(['user', 'jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->whereHas('jobcontact', function($q) use($cont_id) {$q->where('contact_id', '=', $cont_id); })->orwhere('user_id','=',$user_id)->get();
						
						
						$login_user = job::with(['user','jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->where('user_id','=',$user_id)->orderBy('contract_status', 'desc')->orderBy('id', 'desc')->get();
						
						$jobdata['data'] = $login_user->merge($invol_user);
						/* $merged_data = $invol_user->merge($login_user);
						
						usort($merged_data, function ($a, $b) {
							if ($a['id'] == $b['id']) {
								return 0;
							}
							$jobdata['data'] = ($a['id'] < $b['id']) ? -1 : 1;
						}); */

						
						
						
						
						/* echo 'total->'.count($jobdata);
						print_r($jobdata); */
					}
					else
					{
						
						$jobdata = job::with(['user','jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->where('user_id','=',$user_id)->orderBy('contract_status', 'desc')->orderBy('id', 'desc')->paginate(10);
						
					}
						
					
						//$jobdata = job::with(['jobinspection','jobinspection.contact','punchlist','punchlist.punchlistimg'])->where('user_id','=',$user_id)->paginate(10);
					
				}
				
				
				
             /*   $archive_jobs=0;
				$archive = job::with(['user','jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->where('user_id','=',$user_id)->where('status','=',2)->where('job_type','=','Archived')->paginate(10);
			   if($archive){
				   $archive_jobs = $archive;
			   } */
                return response()->json([
                    'success' => true,
                    'Job' => @$jobdata,
					
                ]);
                //dd($data);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }
  
    
// test purpose
public function testgetJob(Request $request)
{
    $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

       $user_id = $user->id;
		
		//$contact_id = $user->contactuserid()->first()->id;
		
		$contact_id = $user->contactuserid()->where('contact_user_id','=',$user_id)->get();
		
		
        try
        {
            if($user)
            {
                if($request->job_id)
                {	$job_id = $request->job_id;
                    if($request->tab == "general")
                    {
                        $jobdata = job::with(['jobinspection','jobinspection.contact','contact','contact.contactshared'])->where('id','=',$request->job_id)->where('job_type','!=','Lead')->paginate(10);
						
						
					}
					
                    else if($request->tab == "stage")
                    {
                        $jobdata = job::with(['jobstage','jobstage.stage'])->where('id','=',$request->job_id)->where('job_type','!=','Lead')->paginate(10);
                    }
                    else if($request->tab == "document")
                    {
                    
						$jobdata = job::with(['jobmedia', 'jobmedia.media'])
						->whereHas('jobmedia', function ($q) use ($request) {
							$q->where('job_id', '=', $request->job_id)
								->whereHas('media', function ($query) {
									$query->where('type', '=', 1);
								});
						})
						->where('id', '=', $request->job_id)->where('job_type','!=','Lead')
						->paginate(10);
					}
                    else if($request->tab == "picture")
                    {
                        $jobdata = job::with(['jobmedia','jobmedia.media'])->whereHas('jobmedia.media', function($q) {$q->where('type', '=', '2'); })->where('id','=',$request->job_id)->where('job_type','!=','Lead')->paginate(10);
                    }
                    else if($request->tab == "contact")
                    {
                        $jobdata = job::with(['jobcontact','jobcontact.contact','jobcontact.contact.contactshared'=> function ($query) use ($request) {
							$query->where('job_id', '=', $request->job_id);
						}])->where('id','=',$request->job_id)->where('job_type','!=','Lead')->paginate(10);
                    }
                    else if($request->tab == "punchlist")
                    {
						$jobcontacts = Jobcontacts::where('job_id','=',$request->job_id)->where('user_id','=',$user->id)->get();
						if(count(@$jobcontacts)>0){
							$jobdata = job::with(['jobinspection','jobinspection.contact','punchlist','punchlist.punchlistimg'])->where('id','=',$request->job_id)->where('job_type','!=','Lead')->paginate(10);
						}else{
								
								$contactss = Contact::where('contact_user_id','=',$user->id)->first();
								$ContactsharedPermission = Contactshared::where('contact_id','=',$contactss->id)->where('job_id','=',$request->job_id)->first();
								if($ContactsharedPermission->punchlist==1){
									
									$jobdata = job::with(['jobinspection','jobinspection.contact','punchlist.punchlistimg', 'punchlist' => function ($q) use ($user,$contactss,$job_id) {
											$q->where('user_id', "=", $user->id);
											$q->orwhere('contact_id', "=", $contactss->id);
											$q->orwhere('job_id', "=", $job_id);
										}])->where('id', '=', $request->job_id)->where('job_type','!=','Lead')->paginate(10);
									
								}else{
									
									$jobdata = job::with(['jobinspection','jobinspection.contact','punchlist.punchlistimg', 'punchlist' => function ($q) use ($user,$contactss,$job_id) {
											$q->where('user_id', "=", $user->id);
											$q->where('job_id', "=", $job_id);
											$q->orwhere('contact_id', "=", $contactss->id);
										}])->where('id', '=', $request->job_id)->where('job_type','!=','Lead')->paginate(10);
									
								}
								
						}
					
					}
					else if($request->tab == "task")
                    {	
				
							
                        $jobcontacts = Jobcontacts::where('job_id', '=', $request->job_id)
                        ->where('user_id', '=', $user->id)
                        ->get();
                    
                    if (count($jobcontacts) > 0) {
                        $jobdata = job::with([
                            'jobinspection',
                            'jobinspection.contact',
                            'taskassignment' => function ($q) {
                                $q->orderByDesc('id');  // Order by 'created_at' in descending order
                            },
                            'taskassignment.taskassignmentimages'
                        ])->where('id', '=', $request->job_id)
                        ->where('job_type', '!=', 'Lead')
                        ->paginate(10);
                    } else {
                        $contactss = Contact::where('contact_user_id', '=', $user->id)->first();
                        $ContactsharedPermission = Contactshared::where('contact_id', '=', $contactss->id)
                            ->where('job_id', '=', $request->job_id)
                            ->first();
                    
                        if ($ContactsharedPermission->jobnotepad == 1) {
                            $jobdata = job::with([
                                'jobinspection',
                                'jobinspection.contact',
                                'taskassignment' => function ($q) use ($user, $contactss, $job_id) {
                                    $q->where('user_id', '=', $user->id)
                                      ->orWhere('contact_id', '=', $contactss->id)
                                      ->orWhere('job_id', '=', $job_id)
                                      ->orderBy('startdate', 'desc');  // Order by 'created_at' in descending order
                                },
                                'taskassignment.taskassignmentimages'
                            ])->where('id', '=', $request->job_id)
                            ->where('job_type', '!=', 'Lead')
                            ->paginate(10);
                        } else {
                            $jobdata = job::with([
                                'jobinspection',
                                'jobinspection.contact',
                                'taskassignment' => function ($q) use ($user, $contactss, $job_id) {
                                    $q->where('user_id', '=', $user->id)
                                      ->where('job_id', '=', $job_id)
                                      ->orWhere('contact_id', '=', $contactss->id)
                                      ->orderByDesc('id');  // Order by 'created_at' in descending order
                                },
                                'taskassignment.taskassignmentimages'
                            ])->where('id', '=', $request->job_id)
                            ->where('job_type', '!=', 'Lead')
                            ->paginate(10);
                        }
                    }
                    
								
						
									
                    }
                    else
                    {
                        $jobdata = job::with(['user', 'jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->where('id','=',$request->job_id)->where('job_type','!=','Lead')->paginate(10);
                    }
                }
                else
                {
					
                
					if(count($contact_id) > 0)
					{ 
				
						$cont_id = $contact_id[0]->id;
						
						$invol_user = job::with(['user', 'jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->whereHas('jobcontact', function($q) use($cont_id) {$q->where('contact_id', '=', $cont_id); })->where('status','!=',2)->where('job_type','!=','Lead')->orderBy('contract_status', 'desc')->orderBy('id', 'desc')->get();
						
						
						$login_user = job::with(['user','jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->where('user_id','=',$user_id)->where('job_type','!=','Lead')->orderBy('contract_status', 'desc')->orderBy('id', 'desc')->get();
						
						$jobdata['data'] = $login_user->merge($invol_user);
					}
					else
					{
						
						$jobdata = job::with(['user','jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->where('user_id','=',$user_id)->where('job_type','!=','Lead')->orderBy('contract_status', 'desc')->orderBy('id', 'desc')->paginate(10);
						
					}
						
					
						//$jobdata = job::with(['jobinspection','jobinspection.contact','punchlist','punchlist.punchlistimg'])->where('user_id','=',$user_id)->paginate(10);
					
				}
				
				
				
             /*   $archive_jobs=0;
				$archive = job::with(['user','jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->where('user_id','=',$user_id)->where('status','=',2)->where('job_type','=','Archived')->paginate(10);
			   if($archive){
				   $archive_jobs = $archive;
			   } */
                return response()->json([
                    'success' => true,
                    'Job' => @$jobdata,
					
                ]);
                //dd($data);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

public function getContactSharedByJobId(Request $request){
	 $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'job_id' => 'required',
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   
			
		
        $user = JWTAuth::authenticate($request->token);
		
		if( $user){
			
			 //$data['contactshared'] = Contact::with('contactshared1')->where('contact_user_id','=',$user->id)->get();

			 $jobcontacts = Jobcontacts::where('job_id','=',$request->job_id)->get();
			
			$data = array();
			
			if($jobcontacts){
				foreach($jobcontacts as $jobcontact){
					//echo'dsfsfs'. $jobcontact->contact_id;
					
					if($jobcontact->contact_id){
						
						$data['contactshared'] = Contact::with(['contactshared1'=> function ($query) use ($request) {$query->where('job_id', '=', $request->job_id);}])->where('contact_user_id','=',$user->id)->get();
						
						
					}
				}
				if(count($data['contactshared'])==0){
					$data['contactshared'] = array(
							array(
								"contactshared1" => array()
							)
						);
				}
			} 
			 return response()->json([
                        'success' => true, 
                        'Jobs' => $data
                    ]);
		}else{
			return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
		}
}

public function getGeneralShared(Request $request){
	 $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'job_id' => 'required',
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   
			
		
        $user = JWTAuth::authenticate($request->token);
		
		if( $user){
			
			 //$data['contactshared'] = Contact::with('contactshared1')->where('contact_user_id','=',$user->id)->get();

			 $jobcontacts = Jobcontacts::where('job_id','=',$request->job_id)->get();
			
			$data = array();
			
			if($jobcontacts){
				foreach($jobcontacts as $jobcontact){
					//echo'dsfsfs'. $jobcontact->contact_id;
					
					if($jobcontact->contact_id){
						
						$data['contactshared'] = Contact::with(['contactshared1'=> function ($query) use ($request) {$query->where('job_id', '=', $request->job_id);}])->where('contact_user_id','=',$user->id)->get();
						
						
					}
				}
				if(count($data['contactshared'])==0){
					$data['contactshared'] = array(
							array(
								"contactshared1" => array()
							)
						);
				}
			} 
			 return response()->json([
                        'success' => true, 
                        'Jobs' => $data
                    ]);
		}else{
			return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
		}
}
// task assignment

	public function addtaskassignment(Request $request)
    {
		
        $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'job_id' => 'required',
           'title' => 'required',
           'assign_to' => 'required',
           'startdate' => 'required',
           'enddate' => 'required',
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   
			
		
        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $taskassignment = $user->taskassignment()->make();
                $taskassignment->job_id = $request->job_id;
                $taskassignment->title = $request->title;
                $taskassignment->room = $request->room;
                $taskassignment->priority = $request->priority;
                $taskassignment->contact_id = $request->assign_to;
                $taskassignment->startdate = $request->startdate;
                $taskassignment->enddate = $request->enddate;
				if(!empty($request->room) && $request->room !=null && $request->room !='null' ){
					$taskassignment->room = $request->room;
				}else{
					$taskassignment->room ='none' ;
				}
				if(!empty($request->description) && $request->description !=null && $request->description !='null' ){
					$taskassignment->description = $request->description;
				}else{
					$taskassignment->description='';
				}
                $taskassignment->save();

                $i=0;
			
                if(!empty($request->image))
                {
                    foreach($request->image as $taskassignkey) 
                    {
                        $i++;
                        $taskassignmentimg = $user->taskassignmentimages()->make();

                        $taskassignmentimg->taskassignment_id = $taskassignment->id;

                        if ($taskassignkey) 
                        {
                            $frontimage = str_replace("data:image/jpeg;base64,", '', $taskassignkey);
                            $frontimage1 = str_replace(" ","+",$frontimage);

                            $profile_pic = time().$i.'.jpeg';
                            file_put_contents($profile_pic, base64_decode($frontimage1));
                            $taskassignmentimg->image = $profile_pic;
                        }
                       
                        $taskassignmentimg->save();
                    }
                }
                if($request->assign_to){
					$job_id = $request->job_id;
					$addtaskassignmentcontact = Contact::where('id','=',$request->assign_to)->first();
					if(@$addtaskassignmentcontact->contact_user_id){
						$startd = Carbon::parse($request->startdate);
					   $formattedDate = $startd->format('l M j, Y');
					    $job_name = Job::where('id','=', $job_id)->first()->name;
						$msg["title"] = $request->title .' , '.$job_name .' starts '.$formattedDate;
						$msg["body"] = 'You have a new task assigned to you.';
						$msg['type'] = "Add TaskAssignment";
						$msg['client_id'] = $addtaskassignmentcontact->contact_user_id;
						$msg['user_type'] = '';
						$msg['move'] = 'Home';
						$description =$request->description;
						$this->sendNotification($addtaskassignmentcontact->contact_user_id , $msg); 
					}
				}
                return response()->json([
                    'success' => true,
                    'message' => 'Your Taskassignment has been assigned.'
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]); 
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }
	
public function addtaskassignmentAttachment(Request $request)
    {
		
        $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'taskassignment_id' => 'required',
           'image' => 'required'
           
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   
			
		
        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $i=0;
			
                if(!empty($request->image))
                {
                   /*  foreach($request->image as $taskassignkey) 
                    { 
                        $i++;*/
						
                        $taskassignmentimg = $user->taskassignmentimages()->make();

                        $taskassignmentimg->taskassignment_id = $request->taskassignment_id;

                  /*       if ($taskassignkey) 
                        {
                            $frontimage = str_replace("data:image/jpeg;base64,", '', $taskassignkey);
                            $frontimage1 = str_replace(" ","+",$frontimage);

                            $profile_pic = time().$i.'.jpeg';
                            file_put_contents($profile_pic, base64_decode($frontimage1));
                            $taskassignmentimg->image = $profile_pic;
                        } */
						
						if($request->image){
							$frontimage = str_replace("data:image/jpeg;base64,", '', $request->image);
							$frontimage1 = str_replace(" ","+",$frontimage);
							$task_pic = time() . '.jpeg';
							file_put_contents($task_pic, base64_decode($frontimage1));
							$taskassignmentimg->image = $task_pic;
				
						}
                        $taskassignmentimg->save();
						
                   /*  } */
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Your Taskassignment Attachment has been saved successfully.'
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]); 
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

   public function updateSingletaskAssignment(Request $request){
	   
	   $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
		   'id'    => 'required',
           'job_id' => 'required',
           'title' => 'required',
           'assign_to' => 'required',
           'startdate' => 'required',
           'enddate' => 'required',
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   
        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $taskassignment = Taskassignment::findorfail($request->id);
                $taskassignment->job_id = $request->job_id;
                $taskassignment->title = $request->title;
                $taskassignment->room = $request->room;
                $taskassignment->priority = $request->priority;
                $taskassignment->contact_id = $request->assign_to;
                $taskassignment->startdate = $request->startdate;
                $taskassignment->enddate = $request->enddate;
                $taskassignment->description = $request->description;

                $taskassignment->save();

                $i=0;
			
               /*  if(!empty($request->image))
                {
                    foreach($request->image as $taskassignkey) 
                    {
                        $i++;
                        $taskassignmentimg = $user->taskassignmentimages()->make();

                        $taskassignmentimg->taskassignment_id = $taskassignment->id;

                        if ($taskassignkey) 
                        {
                            $frontimage = str_replace("data:image/jpeg;base64,", '', $taskassignkey);
                            $frontimage1 = str_replace(" ","+",$frontimage);

                            $profile_pic = time().$i.'.jpeg';
                            file_put_contents($profile_pic, base64_decode($frontimage1));
                            $taskassignmentimg->image = $profile_pic;
                        }
                       
                        $taskassignmentimg->save();
                    }
                } */
                
                return response()->json([
                    'success' => true,
                    'message' => 'Your Taskassignment has been updated.',
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]); 
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
	   
   }
   public function updateTaskAssignment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'taskassignment_id' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }  

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $taskassignment = Taskassignment::findorfail($request->taskassignment_id);
                $taskassignment->status = $request->status;

                if ($taskassignment->save()) 
                {
                    return response()->json([
                        'success' => true, 
                        'message' => 'Your Task Assignment has been update successfully.',
                    ]);
                } 
                else 
                {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Unable to update Task Assignment.Please try again or contact to admin.',
                    ]);
                } 
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }
  public function getTaskAssignment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
			'job_id' => 'required'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                if($request->job_id)
                {
                    $gettaskassignment  = $user->taskassignment()->with('taskassignmentimages')->where('job_id','=',$request->job_id)->get();

                    //dd($getpunchlist);
                }
                else
                {
                    $gettaskassignment = $user->taskassignment()->with('taskassignmentimages')->get();

                    //dd($getpunchlist);
                }

                return response()->json([
                    'success' => true,
                    'TaskAssignment' => $gettaskassignment
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    } 
	public function getTaskassignmentAttachmentById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
			'taskassignment_id' => 'required'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                
                $gettaskassignment  = Taskassignmentimages::where('taskassignment_id','=',$request->taskassignment_id)->get();

                return response()->json([
                    'success' => true,
                    'TaskAssignment_attachments' => $gettaskassignment
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }
	
  public function deleteTaskassignment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'id' => 'required'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $delete_taskassignment = $user->taskassignment()->where('id', '=', $request->id)->delete();

                if($delete_taskassignment)
                {   
                    return response()->json([
                        'success' => true, 
                        'message' => 'Assigned Task has been deleted successfully.',
                    ]);
                }
                else
                {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Unable to delete taskassignment.Please try again or contact to admin.',
                    ]);
                }
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }


//task assignment end

  public function deleteTaskassignmentAttachment(Request $request)
  {
	$validator = Validator::make($request->all(), [
		'token' => 'required',
		'id' => 'required',
		'taskassignment_id'=>'required'
	]);

	if ($validator->fails()) 
	{
		return response()->json(['error' => $validator->messages()]);
	}

	$user = JWTAuth::authenticate($request->token);

	try
	{
		if($user)
		{
			$delete_taskassignment_attachment = Taskassignmentimages::where('id', '=', $request->id)->where('taskassignment_id', '=', $request->taskassignment_id)->delete();

			if($delete_taskassignment_attachment)
			{   
				return response()->json([
					'success' => true, 
					'message' => 'Assigned Task Attachment has been deleted successfully.',
				]);
			}
			else
			{
				return response()->json([
					'success' => false, 
					'message' => 'Unable to delete taskassignment.Please try again or contact to admin.',
				]);
			}
		}
		else
		{
			return response()->json([
				'success' => false,
				'message' => 'Token is not valid. please contact to the admin.',
			]);
		}
	}
	catch (\Exception $e) 
	{
		return response()->json(
		[
			'success' => false,
			'message' =>  $e->getMessage(),
		]);
	}
}


    public function addPunchlist(Request $request)
    {
		
        $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'job_id' => 'required',
           'title' => 'required',
           
           'assign_to' => 'required',
           'startdate' => 'required',
           'enddate' => 'required',
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $punch = $user->punchlist()->make();

                $punch->job_id = $request->job_id;
                $punch->title = $request->title;
                $punch->room = $request->room;
                $punch->priority = $request->priority;
                $punch->contact_id = $request->assign_to;
                $punch->startdate = $request->startdate;
                $punch->enddate = $request->enddate;
				if(!empty($request->room) && $request->room !=null && $request->room !='null' ){
					$punch->room = $request->room;
				}else{
					$punch->room = 'none';
				}
				if(!empty($request->description) && $request->description !=null && $request->description !='null'){
					 $punch->description = $request->description;
				}else{
                $punch->description = '';
				}
                $punch->save();

                $i=0;
			
                if(!empty($request->image))
                {
                    foreach($request->image as $punchimgkey) 
                    {
                        $i++;
                        $punchimg = $user->punchlistimg()->make();

                        $punchimg->punch_id = $punch->id;

                        if ($punchimgkey) 
                        {
                            $frontimage = str_replace("data:image/jpeg;base64,", '', $punchimgkey);
                            $frontimage1 = str_replace(" ","+",$frontimage);

                            $profile_pic = time().$i.'.jpeg';
                            file_put_contents($profile_pic, base64_decode($frontimage1));
                            $punchimg->image = $profile_pic;
                        }
                       
                        $punchimg->save();
                    }
                }
				
				if($request->assign_to){
					$punchlistcontact = Contact::where('id','=',$request->assign_to)->first();
					if(@$punchlistcontact->contact_user_id){
						$msg["title"] = $request->title;
						$msg["body"] = $request->description;
						$msg['type'] = "Punchlist";
						$msg['client_id'] = $punchlistcontact->contact_user_id;
						$msg['user_type'] = '';
						$msg['move'] = 'Home';
						$description =$request->description;
						$this->sendNotification($punchlistcontact->contact_user_id , $msg); 
					}
				}
                return response()->json([
                    'success' => true,
                    'message' => 'Your punchlist has been added.'
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]); 
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function updateAllPunchlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'job_id' => 'required',
        ]);

        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }  

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $punchlist_exits = punchlist::where('job_id', '=', $request->job_id)->first();

                if($punchlist_exits)
                {
                    $punchlist = punchlist::where('job_id','=',$request->job_id)->update(['status'=>'1']);
                   
                    if ($punchlist) 
                    {
                        $get_punchlist = punchlist::where('job_id','=',$request->job_id)->first();

                        return response()->json([
                            'success' => true, 
                            'message' => 'Approved By Client On '.Carbon::createFromFormat('Y-m-d H:i:s', $get_punchlist->updated_at)->format('d-m-Y')
                        ]);
                    } 
                    else 
                    {
                        return response()->json([
                            'success' => false, 
                            'message' => 'Unable to update Punchlist.Please try again or contact to admin.',
                        ]);
                    } 
                }
                else
                {
                    return response()->json([
                        'success' => false, 
                        'message' => 'You can not update this Punchlist.',
                    ]);
                }
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function updatePunchlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'job_id' => 'required',
            'punch_id' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }  

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $punchlist = punchlist::findorfail($request->punch_id);
                $punchlist->status = $request->status;

                if ($punchlist->save()) 
                {
                    return response()->json([
                        'success' => true, 
                        'message' => 'Your punchlist has been updated successfully.',
                    ]);
                } 
                else 
                {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Unable to update Punchlist.Please try again or contact to admin.',
                    ]);
                } 
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function getPunchlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                if($request->job_id)
                {
                    $getpunchlist  = $user->punchlist()->with('punchlistimg')->where('job_id','=',$request->job_id)->get();

                    //dd($getpunchlist);
                }
                else
                {
                    $getpunchlist = $user->punchlist()->with('punchlistimg')->get();

                    //dd($getpunchlist);
                }

                return response()->json([
                    'success' => true,
                    'Punchlist' => $getpunchlist
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }
	
	
  public function deletePunchlistAttachment(Request $request)
  {
	$validator = Validator::make($request->all(), [
		'token' => 'required',
		'id' => 'required',
		'punch_id'=>'required'
	]);

	if ($validator->fails()) 
	{
		return response()->json(['error' => $validator->messages()]);
	}

	$user = JWTAuth::authenticate($request->token);

	try
	{
		if($user)
		{
			$delete_punchlist_attachment = Punchlistimg::where('id', '=', $request->id)->where('punch_id', '=', $request->punch_id)->delete();

			if($delete_punchlist_attachment)
			{   
				return response()->json([
					'success' => true, 
					'message' => 'Punchlist Attachment has been deleted successfully.',
				]);
			}
			else
			{
				return response()->json([
					'success' => false, 
					'message' => 'Unable to delete Punchlist attachment.Please try again or contact to admin.',
				]);
			}
		}
		else
		{
			return response()->json([
				'success' => false,
				'message' => 'Token is not valid. please contact to the admin.',
			]);
		}
	}
	catch (\Exception $e) 
	{
		return response()->json(
		[
			'success' => false,
			'message' =>  $e->getMessage(),
		]);
	}
}

public function getPunchlistAttachmentById(Request $request)
{
	$validator = Validator::make($request->all(), [
		'token' => 'required',
		'punch_id' => 'required'
	]);

	if ($validator->fails()) 
	{
		return response()->json(['error' => $validator->messages()]);
	}

	$user = JWTAuth::authenticate($request->token);

	try
	{
		if($user)
		{
			
			$getpunchlistattachment  = Punchlistimg::where('punch_id','=',$request->punch_id)->get();

			return response()->json([
				'success' => true,
				'Punchlist_attachments' => $getpunchlistattachment
			]);
		}
		else
		{
			return response()->json([
				'success' => false,
				'message' => 'Token is not valid. please contact to the admin.',
			]);
		}
	}
	catch (\Exception $e) 
	{
		return response()->json(
		[
			'success' => false,
			'message' =>  $e->getMessage(),
		]);
	}
}

public function addPunchlistAttachment(Request $request)
    {
		
        $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'punch_id' => 'required',
           'image' => 'required'
           
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   
			
		
        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $i=0;
			
                if(!empty($request->image))
                {
                   /*  foreach($request->image as $taskassignkey) 
                    { 
                        $i++;*/
						
                        $punchlistimg = $user->punchlistimg()->make();

                        $punchlistimg->punch_id = $request->punch_id;

                  
						
						if($request->image){
							$frontimage = str_replace("data:image/jpeg;base64,", '', $request->image);
							$frontimage1 = str_replace(" ","+",$frontimage);
							$punch_pic = time() . '.jpeg';
							file_put_contents($punch_pic, base64_decode($frontimage1));
							$punchlistimg->image = $punch_pic;
				
						}
                        $punchlistimg->save();
						
                  
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Your Punchlistimg Attachment has been saved successfully.'
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]); 
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }
	
    public function getapprovePunchlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'job_id' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $getpunchlistdate  = Punchlist::where('job_id','=',$request->job_id)->orderBy('updated_at', 'desc')->first();
                
				
				$getpunchlist  = Punchlist::where('job_id','=',$request->job_id)->get();
                $arrpunch = array();

                foreach($getpunchlist as $getvalue)
                {

                   array_push($arrpunch, $getvalue->status);
                }
				$key = array_search("0",$arrpunch);
                if ($key !== false) 
                {
                    return response()->json([
                        'success' => true,
                        'message' => "Tap To Approve 100% Completion",
                        'status' => '0',
                        'submessage' => "To be approved by client",
                    ]);
                }
                else
                {
                    return response()->json([
                        'success' => true,
                        'message' => 'Approved By Client On '.Carbon::createFromFormat('Y-m-d H:i:s', $getpunchlistdate->updated_at)->format('F d, Y'),
                        'status' => '1',
                        'submessage' => "Client approved sign off",
                    ]);
                }
                //dd($arrpunch);
                // return response()->json([
                //     'success' => true,
                //     'message' => $getpunchlist->approve_text
                // ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function deletePunchlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'id' => 'required'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $delete_punchlist = $user->punchlist()->where('id', '=', $request->id)->delete();

                if($delete_punchlist)
                {   
                    return response()->json([
                        'success' => true, 
                        'message' => 'Assigned punchlist has been deleted successfully.',
                    ]);
                }
                else
                {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Unable to delete Punchlist.Please try again or contact to admin.',
                    ]);
                }
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        try
        {
            $user_details = User::where('email', $request->email)->first();

            if(!empty($user_details))
            {
                $otp = rand(1000,9999);

                $from_email = Config::get('mail.from.address');
                $email = $user_details->email;
                $name = $user_details->name;
                $subject = "Forgot Password Verify Code";

                $body = @Template::where('type', 3)->orderBy('id', 'DESC')->first()->content;
                $content = array('name' => $name, 'otp' => $otp);
                foreach ($content as $key => $parameter) 
                {
                    $body = str_replace('{{' . $key . '}}', $parameter, $body); 
                }
                try{
                    Mail::send('emails.name', ['template' => $body, 'name' => $name, 'otp' => $otp], function ($m) use ($from_email, $email, $name, $subject) 
                    {
                        $m->from($from_email, 'See Job Run');

                        $m->to($email, $name)->subject($subject);
                    });
                } catch (\Exception $e) {
                    // Log the error but do not show it to the user
                    //Log::error('Error sending email: ' . $e->getMessage());
                }
                $updateotp = User::where('email', $email)->update(['otp' => $otp]);

                return response()->json([
                    'success' => true,
                    'message' => 'Forgot Password Verify Code sent to your email. please check your email.',
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'No such user found. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        try
        {
            if($request->password)
            {
                $newPassword = bcrypt($request->password);
                
                $updatePassword = User::where('email', $request->email)->where('otp', $request->otp)->update(['password' => $newPassword]);
                if ($updatePassword)
                {
                    return response()->json([
                        'success' => true,
                        'message' => 'You have updated your password successfully!',
                    ]);
                } 
                else 
                {
                    return response()->json([
                        'success' => false,
                        'message' => 'Verfiy Code Not Match Please Try Again',
                    ]);
                }
            }
            else if($request->otp)
            {
                $checkotp = User::where('email', $request->email)->where('otp', $request->otp)->first();

                if($checkotp)
                {
                    return response()->json([
                        'success' => true,
                        'message' => 'Verfiy Code Match successfully!',
                    ]);
                }
                else
                {
                    return response()->json([
                        'success' => true,
                        'message' => 'Verfiy Code Not Match Please Try Again',
                    ]);
                }
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong. Please try again.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function dashboard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);
		
        try
        {
            if($user)
            {
                $user_id = $user->id;
                $data = array();
				
				$contact_id = $user->contactuserid()->where('contact_user_id','=',$user_id)->get();
				$get_changeorder=0;
				$time_card=0;
				$get_punchlist=0;
				$get_job='';
				$invol_user=0;
				$login_user = 0;
				$invol_user_lead = 0;
				$login_user_lead = 0;
				if(count($contact_id) > 0)
				{
						$cont_id = $contact_id[0]->id;
						//$get_job = job::with(['user', 'jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->whereHas('jobcontact', function($q) use($cont_id) {$q->where('contact_id', '=', $cont_id); })->orwhere('user_id','=',$user_id)->count();
						$invol_user = job::with(['user', 'jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->whereHas('jobcontact', function($q) use($cont_id) {$q->where('contact_id', '=', $cont_id); })->where('status','!=',2)->count();
						$login_user = job::with(['user','jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->where('user_id','=',$user_id)->count();
						//$jobdata = $invol_user->merge($login_user);
						$get_job=$invol_user + $login_user;
						
						$invol_user_lead = job::with(['user', 'jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->whereHas('jobcontact', function($q) use($cont_id) {$q->where('contact_id', '=', $cont_id); })->where('job_type','=','Lead')->count();
						$login_user_lead = job::with(['user','jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->where('user_id','=',$user_id)->where('job_type','=','Lead')->count();
						$total_leads = $invol_user_lead + $login_user_lead ;
						
				
				}else{
					$get_job = $user->job()->where('status','=',1)->count();
					$total_leads = $user->job()->where('job_type','=','Lead')->count();
					
				}
				
				$time_card = $user->contact()->where('type','=',3)->count(); 
                $get_punchlist = $user->punchlist()->whereDate('created_at','=',Carbon::now()->format('Y-m-d'))->count();

				/* $jobcontact = Job::where('user_id','=',$user->id)->first();
			
				if($jobcontact->id){
					$jobcon = Jobcontacts::where('contact_id','=',$jobcontact->id)->get();	
					if(count($jobcon)>0){
						$jobcontacts = Jobcontacts::where('contact_id','=',$jobcontact->id)->get();
					}else{
						$jobcontacts = Jobcontacts::where('user_id','=',$user->id)->get();
					}
				}else{
					$jobcontacts = Jobcontacts::where('user_id','=',$user->id)->get();
				}
				
				if(count($jobcontacts)>0){
					$get_changeorder=0;
					echo 'helloo'.count($jobcontacts);
					foreach($jobcontacts as $jobco){
						if($jobco->job_id){
						echo 'hello'.$get_changeorder = Changeorder::where('job_id','=',$jobco->job_id)->where('status','=','New')->count();
						}
					}
				} */
				
                //$get_changeorder = $user->changeorder()->whereDate('created_at','=',Carbon::now()->format('Y-m-d'))->count();
				 
				$changeorders = Changeorder::where('user_id','=',$user->id)->get();
				
				
				if(count($changeorders)>0){
					$get_changeorder = $user->changeorder()->where('status','=','New')->count();
				}else{
					
				   $jobcontact = Contact::where('contact_user_id','=',$user->id)->first();
					if($jobcontact){
					   $changeorder = Changeorder::where('client_id', 'LIKE', '%'.$jobcontact->id.'%')->where('status','=','New')->get();
					   $get_changeorder = count($changeorder);
					}
				} 
				
				
                $get_type = $user->contactuserid()->get();
				
				$Gtype ='';
				if(count($get_type)>0){
					foreach($get_type as $type){
						$Gtype = $type->type;
					}	
				}else{
					$Gtype ='';
				}
				
				$myContact = Contact::where('user_id','=',$user->id)->count();
				$total_job_count = Job::where('user_id','=',$user_id)->count();
                $data['username'] = $user->name;
				$data['total_leads']=$total_leads;
                $data['job'] = $get_job;
                $data['time_card'] = $time_card;
                $data['appointment'] = 0;
                $data['punchlist'] = $get_punchlist;
                $data['change_order'] = $get_changeorder;
                $data['type'] = $Gtype; 
				$data['user_id'] = $user_id;
				$data['contact'] = $myContact;
				$data['created_job_count'] = $total_job_count;
				$data['credit_contact'] = $user->credit_contact;

				
				
                return response()->json([
                    'success' => true, 
                    'details' => $data,
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }
    public function testdashboard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);
		$user_id = $user->id;
        try
        {
            if($user)
            {
                $data = array();
				
				$contact_id = $user->contactuserid()->where('contact_user_id','=',$user_id)->get();
				$get_changeorder=0;
				$time_card=0;
				$get_punchlist=0;
				$get_job='';
				$invol_user=0;
				$login_user = 0;
				$invol_user_lead = 0;
				$login_user_lead = 0;
				if(count($contact_id) > 0)
				{
						$cont_id = $contact_id[0]->id;
						//$get_job = job::with(['user', 'jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->whereHas('jobcontact', function($q) use($cont_id) {$q->where('contact_id', '=', $cont_id); })->orwhere('user_id','=',$user_id)->count();
						$invol_user = job::with(['user', 'jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->whereHas('jobcontact', function($q) use($cont_id) {$q->where('contact_id', '=', $cont_id); })->where('status','!=',2)->where('job_type','!=','Lead')->where('job_type','!=','Archived')->count();
						$login_user = job::with(['user','jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->where('user_id','=',$user_id)->where('job_type','!=','Lead')->where('job_type','!=','Archived')->count();
						//$jobdata = $invol_user->merge($login_user);
						$get_job=$invol_user + $login_user;
						
						// $invol_user_lead = job::with(['user', 'jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->whereHas('jobcontact', function($q) use($cont_id) {$q->where('contact_id', '=', $cont_id); })->where('job_type','=','Lead')->count();
						// $login_user_lead = job::with(['user','jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->where('user_id','=',$user_id)->where('job_type','=','Lead')->count();
						// $total_leads = $invol_user_lead + $login_user_lead ;
						
				
				}else{
					$get_job = $user->job()->where('status','=',1)->where('job_type','!=','Lead')->where('job_type','!=','Archived')->count();
                    //$total_leads = $user->job()->where('job_type','=','Lead')->count();
					
					
				}

                $total_leads = $user->lead()->where('status','=',1)->count();
				
				$time_card = $user->contact()->where('type','=',3)->count(); 
                $get_punchlist = $user->punchlist()->whereDate('created_at','=',Carbon::now()->format('Y-m-d'))->count();

				
				$changeorders = Changeorder::where('user_id','=',$user->id)->get();
				
				
				if(count($changeorders)>0){
					$get_changeorder = $user->changeorder()->where('status','=','New')->count();
				}else{
					
				   $jobcontact = Contact::where('contact_user_id','=',$user->id)->first();
					if($jobcontact){
					   $changeorder = Changeorder::where('client_id', 'LIKE', '%'.$jobcontact->id.'%')->where('status','=','New')->get();
					   $get_changeorder = count($changeorder);
					}
				} 
				
				
                $get_type = $user->contactuserid()->get();
				
				$Gtype ='';
				if(count($get_type)>0){
					foreach($get_type as $type){
						$Gtype = $type->type;
					}	
				}else{
					$Gtype ='';
				}
				
				$myContact = Contact::where('user_id','=',$user->id)->count();
				$total_job_count = Job::where('user_id','=',$user_id)->where('job_type','!=','Lead')->count();
                $data['username'] = $user->name;
				$data['total_leads']= $total_leads;
                $data['job'] = $get_job;
                $data['time_card'] = $time_card;
                $data['appointment'] = 0;
                $data['punchlist'] = $get_punchlist;
                $data['change_order'] = $get_changeorder;
                $data['type'] = $Gtype; 
				$data['user_id'] = $user_id;
				$data['contact'] = $myContact;
				$data['created_job_count'] = $total_job_count;
				$data['credit_contact'] = $user->credit_contact;

				
				
                return response()->json([
                    'success' => true, 
                    'details' => $data,
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function changeorder(Request $request)
    {
        $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'job_id' => 'required',
           'client_id' => 'required',
           'date' => 'required',
           'title' => 'required',
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   

	
        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $changeorder = $user->changeorder()->make();

                $changeorder->title = $request->title;
                $changeorder->job_id = $request->job_id;
                $changeorder->client_id = serialize($request->client_id);
                $changeorder->date = $request->date;
                $changeorder->status = 'New';

                if($request->image)
                {
                    $frontimage = str_replace("data:image/jpeg;base64,", '', $request->image);
                    $frontimage1 = str_replace(" ","+",$frontimage);

                    $profile_pic = time() . '.jpeg';
                    file_put_contents($profile_pic, base64_decode($frontimage1));
                    $changeorder->digital_sign = $profile_pic;
                }

                $changeorder->save();

                for ($x = 0; $x < count($request->item['code']); $x++) 
                {
                    $item = $user->item()->make();

                    $item->order_id = $changeorder->id;
                    $item->item_code = $request->item['code'][$x];
                    $item->title = $request->item['title'][$x];
                    $item->description = $request->item['description'][$x];
                    $item->cost = $request->item['cost'][$x];

                    $item->save();
                }
				
				if($request->client_id){
					foreach($request->client_id as $client_id){
						$msg["title"] = 'You have a new change order';
						$msg["body"] = '';
						$msg['type'] = '';
						$msg['client_id'] = $client_id ;
						$msg['user_type'] = '';
						$msg['move'] = 'Home';
						$this->sendNotification($client_id , $msg);
					}
				}
                return response()->json([
                    'success' => true,
                    'message' => 'Your Change Order has been Saved Successfully.'
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]); 
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function getChangeorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $changeorderdata = $user->changeorder()->with(['item'])->get();
               
                return response()->json([
                    'success' => true,
                    'changeOrder' => $changeorderdata
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function updateChangeorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'id' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $changeorder_exits = Changeorder::where('id', '=', $request->id)->first();

                if($changeorder_exits)
                {
                    $changeorder = Changeorder::findorfail($request->id);
                    $changeorder->status = $request->status;
					$changeorder->approved_date = date('Y-m-d H:i:s');
					
					if(!empty($request->digital_sign) && ($request->digital_sign !='null')){
						$digitalsign_image = str_replace("data:image/jpeg;base64,", '', $request->digital_sign);
						$digitalsign = str_replace(" ","+",$digitalsign_image);
						$digitalsign_img = time() . '.jpeg';
						file_put_contents($digitalsign_img, base64_decode($digitalsign));
						$changeorder->digital_sign = $digitalsign_img;
						
					}

                    if ($changeorder->save()) 
                    {
						
						// Create a PDF file
						
							//$changeorder_details = Changeorder::with(['job','user','item'])->where('id','=',$request->id)->get();
							$changeorder_details = Changeorder::with(['job','user','user.meta'=> function($q) {
								$q->where('key','=','Business_name');
							},'item'])->where('id','=',$request->id)->get();
							 $contractorname = $changeorder_details[0]->user->name;
								$contact = array();
								 if($changeorder_details){
									foreach($changeorder_details as $details){
										if($details['client_id']){
											foreach($details['client_id'] as $client_id){
												
												$contact[] = Contact::where('id','=',$client_id)->get();	
											}
										}
										if($details['job']['client_id']){
											$job_clientId = $details['job']['client_id'];
											$jobclient_details = Contact::where('id','=',$job_clientId)->get();
										}
									}	
									
								} 
							$total_jobclient = count($jobclient_details);
							$remain_client 	= $total_jobclient-1;
						
							$user_business='';
							if($changeorder_details[0]->user->meta->Business_name){
								$user_business = $changeorder_details[0]->user->meta->Business_name;
							}
							$date 		 	= 	$changeorder_details[0]->date;
							$order_date   	=   Carbon::parse($date)->format('F d, Y');
							$approve_date   =   Carbon::parse($date)->format('F d, Y');
							
						
							$project_name 	= 	$changeorder_details[0]->job->name;
							$license_no 	= 	$changeorder_details[0]->job->permit_no;
							$address 		= 	$changeorder_details[0]->job->address;
							$city 			= 	$changeorder_details[0]->job->city;
							$state 			= 	$changeorder_details[0]->job->state;
							$pincode 		= 	$changeorder_details[0]->job->pincode;
							$receiptNo 		= 	$changeorder_details[0]->receiptNo;
							
							$digital_sign 	= 	$changeorder_details[0]->digital_sign;
							
							
							$client_name 	= 	$jobclient_details[0]->name;
							$client_email 	= 	$jobclient_details[0]->email;
							$client_address = 	$jobclient_details[0]->address;
							$client_city 	= 	$jobclient_details[0]->city;
							$client_state 	= 	$jobclient_details[0]->state;
							$client_pincode = 	$jobclient_details[0]->pincode;
						
						//print_r($changeorder_details[0]->client_id);
						//print_r($changeorder_details);
					
							$dompdf = new Dompdf();
							$options = new Options();
						
							$options->set('defaultFont', 'Arial');
							$dompdf->setOptions($options);
							$dompdf->setBasePath($_SERVER['DOCUMENT_ROOT']);
							
							// PDF generate
							$html1 ='<!DOCTYPE html><style>.section{page-break-inside: avoid;}</style>';
							$html1 .='<html><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="description" content=""><meta name="author" content=""></head><body style="margin:0;">';
							$html1 .= '<div style=" background-color:#2c7898; font-family:Arial; margin:10px auto; overflow-x:auto; padding:20px 13px; width:500px;">';
							$html1 .='<h1 style="color:#fff; font-size:22px; margin:0 0 20px; text-align:center;">'.$project_name.' Change Order</h1>';
							$html1 .='<table style="background-color:#fff; border-radius:12px 12px 0 0;" cellspacing="0" width="100%">';
							
							$html1 .='<tr>';
							$html1 .='<th style="border-bottom:3px dotted #adb5bd; color:#757575; font-size:16px; font-weight:600; padding:15px 0 23px 15px; text-align:left;" width="50%">Change Order #'.$receiptNo.'</th>';
							$html1 .='<th style="border-bottom:3px dotted #adb5bd; color:#757575; font-size:16px; font-weight:600; padding:15px 0 23px 15px; text-align:left;" width="50%">'.$order_date.'</th>';
							$html1 .='</tr>';
							
							$html1 .='<tr>';
							$html1 .='<td style="color:#000; font-size:16px; font-weight:600; padding:0 10px 3px 22px;">Contractor:</td>';
							$html1 .='<td style="color:#000; font-size:16px; font-weight:600; padding:10px 0 10px 10px;">Client:</td>';
							$html1 .='</tr>';
							
							$html1 .='<tr>';
							$html1 .='<td style="color:#757575; font-size:16px; line-height:1.5; padding:0 0 3px 20px;">'.$user_business.'</td>';
							$html1 .='<td style="color:#757575; font-size:16px;  padding:0 20px 6px 10px;">'.$client_name.'</td>';
							$html1 .='</tr>';
							
							$html1 .='<tr>';
							$html1 .='<td style="color:#757575; font-size:16px; line-height:1.5; padding:0 0 3px 20px;">'.$contractorname.'</td>';
							$html1 .='<td style="color:#757575; font-size:16px;  padding:0 20px 6px 10px;">'.$client_address.'</td>';
							$html1 .='</tr>';
							
							$html1 .='<tr>';
							$html1 .='<td style="color:#000; font-size:16px; font-weight:600; padding:0 20px 3px 20px;">Project:</td>';
							$html1 .='<td style="color:#757575; font-size:16px;  padding:0 0 6px 10px;">'.$client_city.',&nbsp;'.$client_state.'<br>'.$client_pincode.'</td>';
							$html1 .='</tr>';
							
							$html1 .='<tr>';
							$html1 .='<td style="color:#008000; font-size:16px; line-height:1.5; padding:0 0 3px 20px;">'.$project_name.'</td>';
							$html1 .='<td style="color:#757575; font-size:16px;  padding:0 0 6px 10px;"></td>';
							$html1 .='</tr>';
							
							$html1 .='<tr>';
							$html1 .='<td style="color:#757575; font-size:16px;  padding:0 20px 3px 20px;">'.$address.'</td>';
							$html1 .='<td style="color:#757575; font-size:16px;  padding:0 20px 6px 10px;"></td>';
							$html1 .='</tr>';
							
							$html1 .='<tr>';
							$html1 .='<td style="color:#757575; font-size:16px; line-height:1.5; padding:0 0 3px 20px;">'.$city.',&nbsp;'.$state.'<br>'.$pincode.'</td>';
							$html1 .='<td style="color:#757575; font-size:16px; line-height:1.5; padding:0 20px 30px 0;"></td>';
							$html1 .='</tr>';
							
							$html1 .='<tr>';
							$html1 .='<td style="border-top:3px dotted #adb5bd; color:#000; font-size:16px; font-weight:600; padding:15px 0 0 22px;" colspan="2">Shared With</td>';
							$html1 .='</tr>';
							$html1 .='<tr>';
							$html1 .='<td style="border-bottom:3px dotted #adb5bd; color:#000; font-size:16px; padding:22px 0 38px 20px;" colspan="2"><span style="color:#1282a2;">'.$client_name.'</span> and '.$remain_client.' more</td>';
							$html1 .='</tr>';
							$html1 .='</table>';
							
							$html1 .='<table style="background-color:#fff; border-radius:0 0 12px 12px;" cellspacing="0" width="100%">';
							$html1 .='<tr>';
							$html1 .='<td style="color:#000; font-size:16px; font-weight:600; padding:12px 0 10px 22px;">Id</td>';
							$html1 .='<td style="color:#000; font-size:16px; font-weight:600; padding:12px 0 10px 22px">Item Name</td>';
							$html1 .='<td style="color:#000; font-size:16px; font-weight:600; padding:12px 0 10px 22px;">Item Amount</td>';
							$html1 .='</tr>';
							$totalitemcost = 0;
							 foreach($changeorder_details[0]->item as $items){
								$html1 .='<tr>';
								$html1 .='<td style="color:#757575; font-size:15px; line-height:1.5; padding:12px 0 10px 22px">'.$items->id.'</td>';
								$html1 .='<td style="color:#757575; font-size:15px; line-height:1.5; padding:12px 0 10px 22px">'.$items->title.'</td>';
								$html1 .='<td style="color:#757575; font-size:15px; line-height:1.5; padding:12px 0 10px 22px;">$'.number_format($items->cost, 2, '.', ',').'</td>';
								$html1 .='</tr>';
								$html1 .='<tr colspan="4"><td colspan="4" style="padding: 2px 5px 11px 20px; width:100%;color: #49af31;font-size: 14px;margin-top: -7px;">'.$items->description.'</td></tr>';
								$totalitemcost += $items->cost;
							}
					
							$html1 .='<tr>';
							$html1 .='<td style="border-bottom:3px dotted #adb5bd; border-top:3px dotted #adb5bd; color:#757575; font-size:18px; line-height:1.5; padding:12px 0 12px 20px;" colspan="2">Total</td>';
							$html1 .='<td style="border-bottom:3px dotted #adb5bd; border-top:3px dotted #adb5bd; color:#757575; font-size:18px; line-height:1.5; padding:12px 20px 12px 0; text-align:right;" colspan="2">$'.number_format($totalitemcost, 2, '.', ',').'</td>';
							$html1 .='</tr>';
							
							$html1 .='<tr>';
							
							$html1 .='<td style="padding:10px 0 10px; text-align:center;" colspan="4">';
							if($digital_sign){
								$imagePath = public_path($digital_sign);
								$imageContents = file_get_contents($imagePath);
								$imageBase64 = base64_encode($imageContents);
								$html1 .='<img src="data:image/jpeg;base64,'.$imageBase64.'" style="display:block; margin:0 auto 20px; width:200px;"/><br/>';
								
							}
							$html1 .='<button style="background-color:#49af31; border:none; border-radius:8px; color:#fff; font-size:14px; padding:5px 10px;" type="button">Approved On '.$approve_date.'</button></td>';
							$html1 .='</tr>';
						
							$html1 .='</table>';
							$html1 .='</div>';
							$html1 .='</body></html>';
						
							$dompdf->loadHtml($html1);
							$dompdf->set_option('isHtml5ParserEnabled', true);
							$dompdf->set_option('isRemoteEnabled', true);
							$font = 'Arial';
							/* $dompdf->set_paper('letter', 'landscape'); */
							//$dompdf->set_option('isFontSubsettingEnabled', true);

							$dompdf->get_canvas()->page_text(500, 750, 'Page {PAGE_NUM} of {PAGE_COUNT}', $font, 10, array(0, 0, 0));

							$dompdf->render();
							$pdfContent = $dompdf->output();
							$from_email = Config::get('mail.from.address');
							$subject = 'Change order '. $project_name;
							$body ='Please find attached the change order PDF for '.$project_name .'.';
							if($user->email){
								$to = $user->email;
                                try{
                                    Mail::send([], [], function ($message) use ($from_email,$to,$subject,$pdfContent,$body) {
                                        $message->from($from_email, 'See Job Run');
                                        $message->to($to)
                                                ->subject($subject)
                                                ->attachData($pdfContent, 'ChangeOrderDetails.pdf')
                                                ->setBody($body);
                                    });
                                } catch (\Exception $e) {
                                    // Log the error but do not show it to the user
                                    //Log::error('Error sending email: ' . $e->getMessage());
                                }
							}
							
	 					  if($client_email){
								$to = $client_email;
                                try{
                                    Mail::send([], [], function ($message) use ($from_email,$to,$subject,$pdfContent,$body) {
                                        $message->from($from_email, 'See Job Run');
                                        $message->to($to)
                                                ->subject($subject)
                                                ->attachData($pdfContent, 'ChangeOrderDetails.pdf')
                                                ->setBody($body);
                                    });
                                } catch (\Exception $e) {
                                    // Log the error but do not show it to the user
                                    //Log::error('Error sending email: ' . $e->getMessage());
                                }
							}
							
							if($changeorder_details[0]->client_id){
								foreach($changeorder_details[0]->client_id as $client_id){
									$receipts = Contact::where('id','=',$client_id)->first(); 
									$to = $receipts->email;
									try{
                                        Mail::send([], [], function ($message) use ($from_email,$to,$subject,$pdfContent,$body) {
                                        $message->from($from_email, 'See Job Run');
                                        $message->to($to)
                                                ->subject($subject)
                                                ->attachData($pdfContent, 'ChangeOrderDetails.pdf')
                                                ->setBody($body);
                                        });
                                    } catch (\Exception $e) {
                                        // Log the error but do not show it to the user
                                        //Log::error('Error sending email: ' . $e->getMessage());
                                    }
								}
							}    
							
						//End of pdf
                        return response()->json([
                            'success' => true, 
                            'message' => 'Change Order Status has been updated successfully.',
                        ]);
                    } 
                    else 
                    {
                        return response()->json([
                            'success' => false, 
                            'message' => 'Unable To Update Change Order Status. Please Try Again or Contact To Admin.',
                        ]);
                    } 
                }
                else
                {
                    return response()->json([
                        'success' => false, 
                        'message' => 'You can not update this Change Order.',
                    ]);
                }
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function getPlan(Request $request)
    {
        $plans = plan::where('status', '=', 1)->get();

        return response()->json(['plans' => $plans]);
    }

    public function updateClocktime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'jobid' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $checktime = $user->clocktime()->where('tdate','=',Carbon::now()->format('Y-m-d'))->where('clockout','=',null)->orderBy('id', 'DESC')->first();

                //dd($checktime);
                if($checktime)
                {
                    $clocktime = Clocktime::findorfail($checktime->id);
                    $clocktime->clockout = Carbon::now()->format('H:i:s');
                    $clocktime->job_id = $request->jobid;
                    $clocktime->clockout_latitude = $request->latitude;
                    $clocktime->clockout_longitude = $request->longitude;
                    $clocktime->injoyed = $request->injoyed;
                    $clocktime->description = $request->description;
                    $clocktime->save();
                }
                else
                {
                    $clocktime = $user->clocktime()->make();
                    $clocktime->tdate = Carbon::now()->format('Y-m-d');
                    $clocktime->clockin = Carbon::now()->format('H:i:s');
                    $clocktime->job_id = $request->jobid;
                    $clocktime->clockin_latitude = $request->latitude;
                    $clocktime->clockin_longitude = $request->longitude;
                    $clocktime->save();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Clockin-Clockout Time Save successfully!',
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }
	
	public function edit_ClockinClockout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
			'id' => 'required',
			'clockin' => 'required',
			'clockout' => 'required'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
					//dd($checktime);
                    $clocktime = Clocktime::findorfail($request->id);
                    $clocktime->clockin = $request->clockin;                    
					$clocktime->clockout = $request->clockout;
                    $clocktime->save();
					
                return response()->json([
                    'success' => true,
                    'message' => 'Clockin-Clockout updated Successfully!',
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }
	
    public function get_employeejob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $getcontact = $user->contactuserid()->with(['jobcontact'])->where('type','=',$request->type)->get();

                return response()->json([
                    'success' => true,
                    'message' => $getcontact,
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function getusertimesheet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $checktime = $user->clocktime()->orderBy('tdate','DESC')->groupBy('tdate')->paginate(10);

                $samedate = "";
                $getarray = array();

                foreach($checktime as $valueclock)
                {
                    $getarray[$valueclock['tdate']] = $user->clocktime()->where('tdate','=',$valueclock['tdate'])->get();
                }

                $lastrecord = $user->clocktime()->orderby('id','DESC')->first();

                return response()->json([
                    'success' => true,
                    'data'=>$getarray,
                    'lastrecord'=>$lastrecord
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function getEmployee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $getemployee = $user->contact()->where('type','=','3')->get();

                return response()->json([
                    'success' => true,
                    'details' => $getemployee,
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function timecard_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'jobid' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $checktime = $user->clocktime()->where('job_id','=',$request->jobid)->orderBy('tdate','DESC')->groupBy('tdate')->paginate(10);

                $getarray = array();

                foreach($checktime as $valueclock)
                {
                    $getarray[$valueclock['tdate']]['clockin'] = $user->clocktime()->where('job_id','=',$request->jobid)->where('tdate','=',$valueclock['tdate'])->orderBy('id','ASC')->first();

                    $getarray[$valueclock['tdate']]['clockout'] = $user->clocktime()->where('job_id','=',$request->jobid)->where('tdate','=',$valueclock['tdate'])->orderBy('id','DESC')->first();

                    $fdate = Carbon::parse($valueclock['tdate'].' '.$getarray[$valueclock['tdate']]['clockin']->clockin);

                    $edate = Carbon::parse($valueclock['tdate'].' '.$getarray[$valueclock['tdate']]['clockout']->clockout);

                    if($getarray[$valueclock['tdate']]['clockout']->clockout != "")
                    {
                        $diffhour = $fdate->diffInHours($edate);
                        $diffminutes = $fdate->diffInMinutes($edate);

                        $housminutes = $diffhour*60;
                        $diffminutest = $diffminutes-$housminutes;

                        $hours = $diffhour.' h '. $diffminutest.' m';
                    }
                    else
                    {
                        $hours = "0 h 0 m";
                    }
                    
                    $getarray[$valueclock['tdate']]['hour'] = $hours;
                }

                return response()->json([
                    'success' => true,
                    'details' => $getarray,
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function getjobtimecard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'employee_id' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            $getjob = $user->jobcontact()->with(['job'])->where('contact_id','=',$request->employee_id)->get();

            return response()->json(
            [
                'success' => true,
                'message' => $getjob,
            ]);
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function clockdetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'jobid' => 'required',
            'tdate' => 'required',
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            $clockdetails = $user->clocktime()->where('job_id','=',$request->jobid)->where('tdate','=',$request->tdate)->get();

            $clockin = $user->clocktime()->where('job_id','=',$request->jobid)->where('tdate','=',$request->tdate)->orderBy('id','ASC')->first()->clockin;

            $clockout = $user->clocktime()->where('job_id','=',$request->jobid)->where('tdate','=',$request->tdate)->orderBy('id','DESC')->first()->clockout;

            $fdate = Carbon::parse($request->tdate.' '.$clockin);

            $edate = Carbon::parse($request->tdate.' '.$clockout);

            if($clockout != "")
            {
                $diffhour = $fdate->diffInHours($edate);
                $diffminutes = $fdate->diffInMinutes($edate);

                $housminutes = $diffhour*60;
                $diffminutest = $diffminutes-$housminutes;

                $hours = $diffhour.' h '. $diffminutest.' m';
            }
            else
            {
                $hours = "0 h 0 m";
            }

            return response()->json(
            [
                'success' => true,
                'message' => $clockdetails,
                'daytotal' => $hours,
            ]);
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }
    
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);
        
        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $user = JWTAuth::authenticate($request->token);
        
        if ($user) 
        {
            if($request->user_name)
            {
                $user->name = $request->user_name;  
            }
			if($request->timezone)
            {
                $user->timezone = $request->timezone;  
            }
            if($request->profile_pic)
            {
                $frontimage = str_replace("data:image/jpeg;base64,", '', $request->profile_pic);
                $frontimage1 = str_replace(" ","+",$frontimage);

                $profile_pic = time() . '.jpeg';
                file_put_contents($profile_pic, base64_decode($frontimage1));
                $user->profile_pic = $profile_pic;
            }
            if($request->phone)
            {
                $user->updateMeta('Mobile' , $request->phone);
            }
            
            $user->save();
            return response()->json(['success' => true, 'message' => 'Profile updated successfully.']);
        } 
        else 
        {
            return response()->json([
                'success' => false,
                'message' => 'Token is not valid. please contact to the admin.',
            ]);
        }
    }
	Public function jobcalender(Request $request){
		 $validator = Validator::make($request->all(), [
            'token' => 'required',
			'job_id'=>'required'
        ]);
        
        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $user = JWTAuth::authenticate($request->token);
	
		if($user){
			/* $jobcontacts = Jobcontacts::where('job_id','=',$request->job_id)->where('user_id','=',$user->id)->get();
			
			
				if(count(@$jobcontacts)>0){
					
					$gettaskassignment = Taskassignment::where('job_id','=',$request->job_id)->get();
					 $getpunchlist  = Punchlist::where('job_id','=',$request->job_id)->get();
				}else{
						$jobcontacts = Jobcontacts::where('job_id','=',$request->job_id)->get();
						
						$data = array();
							
						foreach($jobcontacts as $jobcontact){
							
							if($jobcontact->contact_id){
								$data['contactshared'] = Contact::with('contactshared1')->where('id','=',$jobcontact->contact_id)->where('contact_user_id','=', $user->id)->get();
								foreach($data['contactshared'] as $ctshrd){
									if($ctshrd['contactshared1'][0]->jobnotepad==1){
										$gettaskassignment = Taskassignment::where('job_id','=',$request->job_id)->get();
									}else{
										$gettaskassignment = Taskassignment::where('job_id','=',$request->job_id)->where('user_id','=',$user->id)->get();
									}
									if($ctshrd['contactshared1'][0]->punchlist==1){
										$getpunchlist  = Punchlist::where('job_id','=',$request->job_id)->get();
									}else{
										$getpunchlist  = Punchlist::where('job_id','=',$request->job_id)->where('user_id','=',$user->id)->get();
									}
								}
									
							}
						}

						
					
				} */		 
			 
				 $jobcontacts = Jobcontacts::where('job_id','=',$request->job_id)->where('user_id','=',$user->id)->get();
				 
									if(count(@$jobcontacts)>0){
											
										$gettaskassignment = Taskassignment::where('job_id','=',$request->job_id)->get();
										$getpunchlist  = Punchlist::where('job_id','=',$request->job_id)->get();
										}else{
											
											$contactss = Contact::where('contact_user_id','=',$user->id)->first();
											
											$ContactsharedPermission = Contactshared::where('contact_id','=',$contactss->id)->where('job_id','=',$request->job_id)->first();
											if($ContactsharedPermission->calendar==1){
													$gettaskassignment = Taskassignment::where('job_id','=',$request->job_id)->get();
													$getpunchlist  =  Punchlist::where('job_id','=',$request->job_id)->get();
													/* if($ContactsharedPermission->jobnotepad==1){
														$gettaskassignment = Taskassignment::where('job_id','=',$request->job_id)->get();
													}else{
														$gettaskassignment = Taskassignment::where('job_id','=',$request->job_id)->where('user_id','=',$user->id)->orwhere('contact_id','=',$contactss->id)->get();
													}
													if($ContactsharedPermission->punchlist==1){
														$getpunchlist  = Punchlist::where('job_id','=',$request->job_id)->get();
													}else{
														$getpunchlist  = Punchlist::where('job_id','=',$request->job_id)->where('user_id','=',$user->id)->orwhere('contact_id','=',$contactss->id)->get();
													} */
											}
								}
		/* $job = Job::where('id','=',$request->job_id)->where('user_id','=',$user->id)->get();
		   
		   if(count(@$job)>0){
			  $gettaskassignment = Taskassignment::where('job_id','=',$request->job_id)->get();
			  $getpunchlist  = Punchlist::where('job_id','=',$request->job_id)->get();
			  
		   }else{
			  $gettaskassignment = Taskassignment::where('job_id','=',$request->job_id)->where('user_id','=',$user->id)->get();
			  $getpunchlist  = Punchlist::where('job_id','=',$request->job_id)->where('user_id','=',$user->id)->get();
		   }  */
			 
		
			 
			 return response()->json(
            [
                'success' => true,
                'gettaskassignment' => $gettaskassignment,
				'punchlist' => $getpunchlist
				
            ]); 
		}else{
			 return response()->json([
                'success' => false,
                'message' => 'Token is not valid. please contact to the admin.',
            ]);
		}
	}	
	
	Public function jobcalendernew(Request $request){
		 $validator = Validator::make($request->all(), [
            'token' => 'required',
			'job_id'=>'required'
        ]);
        
        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $user = JWTAuth::authenticate($request->token);
	
		if($user){
				 $jobcontacts = Jobcontacts::where('job_id','=',$request->job_id)->where('user_id','=',$user->id)->get();
			
									if(count(@$jobcontacts)>0){
											
										$gettaskassignment = Taskassignment::where('job_id','=',$request->job_id)->get();
										$getpunchlist  = Punchlist::where('job_id','=',$request->job_id)->get();
										}else{
											
											$contactss = Contact::where('contact_user_id','=',$user->id)->first();
											$ContactsharedPermission = Contactshared::where('contact_id','=',$contactss->id)->where('job_id','=',$request->job_id)->first();
											if($ContactsharedPermission->calendar==1){
													if($ContactsharedPermission->jobnotepad==1){
														$gettaskassignment = Taskassignment::where('job_id','=',$request->job_id)->get();
													}else{
														$gettaskassignment = Taskassignment::where('job_id','=',$request->job_id)->where('user_id','=',$user->id)->orwhere('contact_id','=',$contactss->id)->get();
													}
													if($ContactsharedPermission->punchlist==1){
														$getpunchlist  = Punchlist::where('job_id','=',$request->job_id)->get();
													}else{
														$getpunchlist  = Punchlist::where('job_id','=',$request->job_id)->where('user_id','=',$user->id)->orwhere('contact_id','=',$contactss->id)->get();
													}
											}
								}
			 return response()->json(
            [
                'success' => true,
                'gettaskassignment' => $gettaskassignment,
				'punchlist'=>$getpunchlist
				
            ]); 
		}else{
			 return response()->json([
                'success' => false,
                'message' => 'Token is not valid. please contact to the admin.',
            ]);
		}
	}
	public function addEvent(Request $request){
		
		 $validator = Validator::make($request->all(), [
            'token' => 'required',
			'title'=>'required',
			'startdate'=>'required',
			'enddate'=>'required',
			'notification_alert'=>'required'
        ]);
        
        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()], 200);
        }
		
        $user = JWTAuth::authenticate($request->token);
			
			
		if($user){
			/* $startdate = Carbon::createFromFormat('Y-m-d H:i:s', $request->startdate);
			$enddate = Carbon::createFromFormat('Y-m-d H:i:s', $request->enddate); */
			
			$events = Events::make();
			$events->user_id = $user->id;
			$events->title =$request->title;
			$events->description =$request->description;
			$events->startdate = $request->startdate;
			$events->enddate = $request->enddate;
			$events->notification_alert = $request->notification_alert;
			$events->status = 1;
			$events->save();
			
			
			return response()->json(['success' => true, 'message' => 'You have successfully added Event.']);
			
		}else{
			return response()->json([
                'success' => false,
                'message' => 'Token is not valid. please contact to the admin.',
            ]);
		}
		
	}	
	
	public function EditEvent(Request $request){
		
		 $validator = Validator::make($request->all(), [
            'token' 		=> 	'required',
			'id'			=> 	'required',
			'title'			=>  'required',
			'startdate'		=> 	'required',
			'enddate'		=> 	'required'
			
        ]);
        
        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()], 200);
        }
	
        $user = JWTAuth::authenticate($request->token);
		
		if($user){
			/* $startdate = Carbon::createFromFormat('Y-m-d H:i:s', $request->startdate);
			$enddate = Carbon::createFromFormat('Y-m-d H:i:s', $request->enddate); */
			
			$events = Events::findorfail($request->id);
			$events->title =$request->title;
			$events->description =$request->description;
			$events->startdate = $request->startdate;
			$events->enddate = $request->enddate;
			if($request->notification_alert){
				$events->notification_alert = $request->notification_alert;
			}
			$events->save();
			return response()->json(['success' => true, 'message' => 'You have successfully updated Event.']);
			
		}else{
			return response()->json([
                'success' => false,
                'message' => 'Token is not valid. please contact to the admin.',
            ]);
		}
		
	}
	
	public function getEvents(Request $request){
		
		 $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);
        
        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $user = JWTAuth::authenticate($request->token);
		if($user){
			$events = Events::where('user_id',$user->id)->where('status',1)->get();
			return response()->json([
                    'success' => true,
                    'Events' => $events
                ]);
		}else{
			 return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
			
		}
		
	}
	
public function DeleteEvent(Request $request){
	 $validator = Validator::make($request->all(), [
            'token' => 'required',
			'id'	=> 'required'
        ]);
        
        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $user = JWTAuth::authenticate($request->token);
		if($user){
			
			$events = Events::findorfail($request->id);
			$events->delete();
			return response()->json(['success' => true, 'message' => 'You have successfully Deleted Event.']);
		}else{
			return response()->json([
					'success' => false,
					'message' => 'Token is not valid. please contact to the admin.',
				]);
		}
	
}	
	
Public function getTimeCard(Request $request){
	
	$validator = Validator::make($request->all(), [
            'token' => 'required',
			'from_date'=>'required',
			'to_date' =>'required'
        ]);
        
	if ($validator->fails()) 
	{
		return response()->json(['error' => $validator->messages()], 200);
	}
	
	$user = JWTAuth::authenticate($request->token);
	
	 try
        {
			
		$contact = Contact::where('id','=',$request->jobcontact_id)->first();
		$contact_user = $contact->contact_user_id;
		 if($request->jobcontact_id && $request->jobid==0){
			$data=array();	
			$timesheet = array();
			$hours=0;
			$totalminutes=0;
			$diffsecond = 0;
			$totalSeconds =0;
			$totalhours =0;
			
			$clockdetails = Clocktime::where('user_id','=',$contact_user)
					->whereBetween('tdate', [$request->from_date, $request->to_date])
					->orderBy('id', 'asc')
					->get();
			/*  $getjobs = Jobcontacts::with(['job'])->where('contact_id','=',$request->jobcontact_id)->get();
			$clockdetails = [];

			foreach ($getjobs as $job) {
				$job_idd = $job->job->id;
				$clockdetails += array_merge($clockdetails, Clocktime::where('job_id', '=', $job_idd)
					->where('user_id','=',$contact_user)
					->whereBetween('tdate', [$request->from_date, $request->to_date])
					->orderBy('id', 'asc')
					->get()
					->toArray());
				
			} */
			
			
			
			
			foreach($clockdetails as $clockdetail){
				
				/***********total day time *************/
					if($clockdetail->clockout=== null){
						$clockoutt =  date('H:i:s', time());
						$sTime = Carbon::parse($clockdetail->tdate.' '.$clockdetail->clockin);
						$eTime = Carbon::parse($clockdetail->tdate.' '.$clockoutt);
						$dsecond = $sTime->diffInSeconds($eTime);
						$clockouttime = $clockdetail->clockout;
						
					}else{
						$sTime = Carbon::parse($clockdetail['tdate'].' '.$clockdetail['clockin']);
						$eTime = Carbon::parse($clockdetail['tdate'].' '.$clockdetail['clockout']);
						$dsecond = $eTime->diffInSeconds($sTime);
						$eetm = Carbon::createFromFormat('Y-m-d H:i:s', $eTime);
						$clockouttime = $eetm->format('h:i A');
					}
						$days = floor($dsecond / 86400);
						$hourss = floor(($dsecond -($days*86400)) / 3600);
						$minutess = floor(($dsecond / 60) % 60);
						$secondss = $dsecond % 60;
						$hoursss = $hourss.' hrs '.$minutess .' mins';
						
						$sstm = Carbon::createFromFormat('Y-m-d H:i:s', $sTime);
						$clockintime = $sstm->format('h:i A');
						
						
						
						$clockdate =$clockdetail['tdate'];
						$timesheet[$clockdate][] = 
						array(
							'job_id'=>$clockdetail['job_id'],
							'tdate'=>$clockdetail['tdate'],
							'clockin'=>$clockintime,
							'clockout'=>$clockouttime,
							'dseconds'=>$dsecond,
							'daytotal'=>$hoursss
							);
							
					/***********total days time *************/
					if($clockdetail->clockout === null) {				
							$clockoutt =  date('H:i:s', time());
							$startTime = Carbon::parse($clockdetail->tdate.' '.$clockdetail->clockin);
							$endTime = Carbon::parse($clockdetail->tdate.' '.$clockoutt);
							$diffsecond = $endTime->diffInSeconds($startTime);
							$totalSeconds += $diffsecond; 
						}else{
							$startTime = Carbon::parse($clockdetail['tdate'].' '.$clockdetail['clockin']);
							$endTime = Carbon::parse($clockdetail['tdate'].' '.$clockdetail['clockout']);
							$diffsecond = $endTime->diffInSeconds($startTime);
							$totalSeconds += $diffsecond;
						}
					
				  
				  
			}
		
		 }
		 else{	
				$data=array();	
				$timesheet = array();
				$hours=0;
				$totalminutes=0;
				$diffsecond = 0;
				$totalSeconds =0;
				$totalhours =0;
				
			  $clockdet = $user->clocktime()->where('job_id','=',$request->jobid)->whereBetween('tdate',[$request->from_date,$request->to_date])->get();
			 
			 
			  if(count($clockdet)>0){
				$clockdetails = $user->clocktime()->where('job_id','=',$request->jobid)->where('user_id','=',$contact_user)->whereBetween('tdate',[$request->from_date,$request->to_date])->orderBy('id', 'asc')->get();  
			  }else{
				$clockdetails = Clocktime::where('job_id','=',$request->jobid)->where('user_id','=',$contact_user)->whereBetween('tdate',[$request->from_date,$request->to_date])->orderBy('id', 'asc')->get();
				
				
				
			  }
			 
			
			 foreach($clockdetails as $clockdetail){
				
				/***********total day time *************/
				
				
					 if($clockdetail->clockout=== null){
						$clockoutt =  date('H:i:s', time());
						$sTime = Carbon::parse($clockdetail->tdate.' '.$clockdetail->clockin);
						$eTime = Carbon::parse($clockdetail->tdate.' '.$clockoutt);
						$dsecond = $sTime->diffInSeconds($eTime);
						$clockouttime = $clockdetail->clockout;
					}else{
						$sTime = Carbon::parse($clockdetail->tdate.' '.$clockdetail->clockin);
						$eTime = Carbon::parse($clockdetail->tdate.' '.$clockdetail->clockout);
						$dsecond = $eTime->diffInSeconds($sTime);
						$eetm = Carbon::createFromFormat('Y-m-d H:i:s', $eTime);
						$clockouttime = $eetm->format('h:i A');
					} 	
					
						
						
						$days = floor($dsecond / 86400);
						$hourss = floor(($dsecond -($days*86400)) / 3600);
						$minutess = floor(($dsecond / 60) % 60);
						$secondss = $dsecond % 60;
						$hoursss = $hourss.' hrs '.$minutess .' mins';
						
						$sstm = Carbon::createFromFormat('Y-m-d H:i:s', $sTime);
						$clockintime = $sstm->format('h:i A');
						
						
						
						
						$timesheet[$clockdetail->tdate][] = 
						array(
							'job_id'=>$clockdetail->job_id,
							'tdate'=>$clockdetail->tdate,
							'clockin'=>$clockintime,
							'clockout'=>$clockouttime,
							'dseconds'=>$dsecond,
							'daytotal'=>$hoursss
							
							);
					
					/***********total days time *************/
					
						if($clockdetail->clockout === null) {				
							$clockoutt =  date('H:i:s', time());
							$startTime = Carbon::parse($clockdetail->tdate.' '.$clockdetail->clockin);
							$endTime = Carbon::parse($clockdetail->tdate.' '.$clockoutt);
							$diffsecond = $endTime->diffInSeconds($startTime);
							$totalSeconds += $diffsecond; 
						}else{
							$startTime = Carbon::parse($clockdetail->tdate.' '.$clockdetail->clockin);
							$endTime = Carbon::parse($clockdetail->tdate.' '.$clockdetail->clockout);
							$diffsecond = $endTime->diffInSeconds($startTime);
							$totalSeconds += $diffsecond;
						}
					
				  
				} 
			  
			  
			}
		

				
				
		
				
			if($clockdetails){	
				
				/* $init = $totalSeconds;
				$day = floor($init / 86400);
				$hours = floor(($init -($day*86400)) / 3600);
				$minutes = floor(($init / 60) % 60);
				$seconds = $init % 60;
				$hours = $hours.' hrs '.$minutes .' mins'; */
				$init = $totalSeconds;
				$minutes = floor($init / 60);
				$hourss = floor($minutes / 60);
				$days = floor($hourss / 24);
				// Calculate the remaining hours and minutes
				
				$remainingMinutes = $minutes % 60;
				$remainingHours = $hourss % 24;
				$hours = $hourss.' hrs '.$remainingMinutes .' mins';
				
				$data=array();
				$i=0;
				
			
				foreach ($timesheet as $ntimes) {
					$tdayseconds = 0;
					$sttime = null;
					$entime = null;
					$date = null;

					foreach ($ntimes as $ntime) {
						if (!$date) {
							$date = $ntime['tdate'];
						}
						if (!$sttime) {
							$sttime = $ntime['clockin'];
						}
						$entime = $ntime['clockout'];
						$tdayseconds += $ntime['dseconds'];
					}

					$dayTotalHours = floor($tdayseconds / 3600);
					$dayTotalMinutes = floor(($tdayseconds % 3600) / 60);
					
					
					/******* start array*********/
					/* foreach ($ntimes as $ntime) {
					  $tdate = $ntime['tdate'];
						$clockin = $ntime['clockin'];
						$clockout = $ntime['clockout'];
						if (!isset($data[$tdate])) {
							$data[$tdate] = [
								'tdate' => $tdate,
								'first_clockin' => $clockin,
								'last_clockout' => $clockout,
							];
						} else {
							$data[$tdate]['last_clockout'] = $clockout;
						}
					} */	
					/******* start array*********/
					
					 $data[] = array(
						'job_id' => $ntime['job_id'],
						'tdate' => $date,
						'clockin' => $sttime,
						'clockout' => $entime,
						'daytotal' => $dayTotalHours . ' hrs ' . $dayTotalMinutes . ' mins'
					); 
				}
			} 
          
			
            return response()->json(
            [
                'success' => true,
                'timesheet' => $data,
                'totalhours' => $hours,
            ]);
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
	
	
}

public function getTimecardDetails(Request $request){
    $validator = Validator::make($request->all(), [
            'token' => 'required',
			'tdate' =>'required',
			
        ]);
        
	if ($validator->fails()) 
	{
		return response()->json(['error' => $validator->messages()], 200);
	}
	$user = JWTAuth::authenticate($request->token);
	
	 try
        {
		$totalSeconds =0;
		$contact = Contact::where('id','=',$request->jobcontact_id)->first();
		$contact_user = $contact->contact_user_id;
		
		$clockdet = $user->clocktime()->where('tdate','=',$request->tdate)->where('user_id','=',$contact_user)->get();
		
		if(count($clockdet)>0){
			
			if($request->job_id==0){
				$clockdetails = $user->clocktime()->where('tdate','=',$request->tdate)->where('user_id','=',$contact_user)->get();
			}else{
				$clockdetails = $user->clocktime()->where('tdate','=',$request->tdate)->where('user_id','=',$contact_user)->where('job_id','=',$request->job_id)->get();
			}
		}else{
			if($request->job_id==0){
				$clockdetails = Clocktime::where('tdate','=',$request->tdate)->where('user_id','=',$contact_user)->get();
			}else{
				$clockdetails = Clocktime::where('tdate','=',$request->tdate)->where('user_id','=',$contact_user)->where('job_id','=',$request->job_id)->get();	
			}
		}
		
		
		foreach($clockdetails as $clockdetail){
			if($clockdetail->clockout === null) {				
				$clockoutt =  date('H:i:s', time());
				$startTime = Carbon::parse($clockdetail->tdate.' '.$clockdetail->clockin);
				$endTime = Carbon::parse($clockdetail->tdate.' '.$clockoutt);
				$diffsecond = $endTime->diffInSeconds($startTime);
				$totalSeconds += $diffsecond; 
			}else{
			$startTime = Carbon::parse($clockdetail->tdate.' '.$clockdetail->clockin);
			$endTime = Carbon::parse($clockdetail->tdate.' '.$clockdetail->clockout);
			$diffsecond = $endTime->diffInSeconds($startTime);
			$totalSeconds += $diffsecond;
			}
		}
		/*  $day = floor($totalSeconds / 86400);
		 $htotal = floor(($totalSeconds -($day*86400)) / 3600).' hrs '.floor(($totalSeconds / 60) % 60).' mins';
	 */	 $init = $totalSeconds;
				$minutes = floor($init / 60);
				$hourss = floor($minutes / 60);
				$days = floor($hourss / 24);
				// Calculate the remaining hours and minutes
				
				$remainingMinutes = $minutes % 60;
				$remainingHours = $hourss % 24;
				$htotal = $hourss.' hrs '.$remainingMinutes .' mins';
		 return response()->json(
            [
                'success' => true,
                'timesheet' => $clockdetails,
                'totalhours' => $htotal,
            ]);
		 
		} catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }

}
public function getTimesheetClockinClockout(Request $request){
	$validator = Validator::make($request->all(), [
				'token' => 'required',
				'tdate' =>'required',
				'job_id' =>'required'
			]);
			
		if ($validator->fails()) 
		{
			return response()->json(['error' => $validator->messages()], 200);
		}
		//date_default_timezone_set("Asia/Calcutta");
		
		$user = JWTAuth::authenticate($request->token);	
		if($user){
		 $data =array();
		$results = $user->clocktime()->where('tdate','=',$request->tdate)->where('job_id','=',$request->job_id)->orderBy('id', 'desc')->first();
		
		 if($results){
			 $timeclocks = $user->clocktime()->where('tdate','=',$request->tdate)->where('job_id','=',$request->job_id)->get();
			 $totalSeconds=0;
			 foreach($timeclocks as $timeclock){
				if($timeclock->clockout === null) {
					
					$clockoutt =  date('H:i:s', time());
					$startTime = Carbon::parse($timeclock->tdate.' '.$timeclock->clockin);
					$endTime = Carbon::parse($timeclock->tdate.' '.$clockoutt);
					$diffsecond = $endTime->diffInSeconds($startTime);
					$totalSeconds += $diffsecond; 
				}else{
					$startTime = Carbon::parse($timeclock->tdate.' '.$timeclock->clockin);
					$endTime = Carbon::parse($timeclock->tdate.' '.$timeclock->clockout);
					$diffsecond = $endTime->diffInSeconds($startTime);
					$totalSeconds += $diffsecond;
				}
				$lastclockin = Carbon::parse($timeclock->tdate.' '.$timeclock->clockin);
			  }
				 $day = floor($totalSeconds / 86400);
				 $htotal = floor(($totalSeconds -($day*86400)) / 3600).' hrs '.floor(($totalSeconds / 60) % 60).' mins';
			  $data['totalhours']=$htotal;
			  $data['current_clockin']= $lastclockin;
			  if($results->id){
				 $data['id']= $results->id;
			  }else{
				  $data['id']=0;
			  }
			  
			  if($results->clockstatus==0){
				$data['status'] ='Clock Out'; 
			  }	
			 else{
				$data['status'] ='Clock In';  
			  }
				return response()->json(
				[
					'success' => true,
					'clock_status' => $data
					
					
				]);
			}else{
				$data['totalhours']='0 hrs 0 min';
				$data['status'] ='Clock In';  
				$data['id']=0;
				return response()->json(
				[
					'success' => true,
					'clock_status' =>$data
				]);
			}
		}else{
			 return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
		}
	
}
Public function addTimesheetClockin(Request $request){
	$validator = Validator::make($request->all(), [
				'token' => 'required',
				'tdate' =>'required',
				'job_id' =>'required'
				
			]);
			
		if ($validator->fails()) 
		{
			return response()->json(['error' => $validator->messages()], 200);
		}
		
		
		$user = JWTAuth::authenticate($request->token);	
		//echo $user->id;
		if($user){  
					$tday_date = Carbon::now()->format('Y-m-d');
					$clockinexist = $user->clocktime()->where('clockout', '=', NULL)->where('tdate','=',$tday_date)->orderBy('id', 'desc')->count();
					
					if($clockinexist<1){
						$clocktime = $user->clocktime()->make();
						$clocktime->tdate = Carbon::now()->format('Y-m-d');
						$clocktime->clockin = Carbon::now()->format('H:i:s');
						/* $clocktime->clockout = Carbon::now()->addHour(12)->format('H:i:s'); */
						$clocktime->job_id = $request->job_id;                    
						$clocktime->clockin_latitude = $request->clockin_latitude;
						$clocktime->clockin_longitude = $request->clockin_longitude;
						$clocktime->clockstatus = 0;
						$clocktime->save();
						 return response()->json([
							'success' => true,
							'clockin_id'=>$clocktime->id,
							'message' => 'You have successfully clockedin.',
						]);
						
					}else{
						return response()->json([
							'success' => false,
							'message' => 'You have already clocked in for the job.',
						]);	
						
					}
				   
		}else{
				return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
		}
}
Public function updateTimesheetClockout(Request $request){
	$validator = Validator::make($request->all(), [
				'token' => 'required',
				'id' =>'required',
				'tdate' =>'required',
				'job_id' =>'required',
				'injoyed'=>'required',
				'clockout_latitude'=>'required',
				'clockout_longitude'=>'required'
				
			]);
			
		if ($validator->fails()) 
		{
			return response()->json(['error' => $validator->messages()], 200);
		}
		
		
		$user = JWTAuth::authenticate($request->token);	
		//echo $user->id;
		$jobclient_name = $user->name;
		if($user){
					
					$clocktime =Clocktime::findorfail($request->id);
					$clocktime->clockout = Carbon::now()->format('H:i:s');
                    $clocktime->job_id = $request->job_id;                    
                    $clocktime->clockout_latitude = $request->clockout_latitude;
                    $clocktime->clockout_longitude = $request->clockout_longitude;
					if($request->injoyed>0){
						$clocktime->description = $request->description;
						
						$job = Job::where('id','=',$request->job_id)->first();
						$jobuser = User::where('id','=',$job->user_id)->first();
						$client_id = $jobuser->id;
						$msg["title"] = 'Job' .$job->name. 'is clockout due to injured.';
						$msg["body"] = $request->description;
						$msg['type'] = "Injured";
						$msg['client_id'] = $client_id ;
						$msg['user_type'] = '';
						$msg['move'] = 'Home';
						$description =$request->description;
						$this->sendNotification($client_id , $msg); 
						//$getjob = $user->jobcontact()->with(['job'])->where('user_id','=',$request->employee_id)->get();
						$from_email = Config::get('mail.from.address');
							$email = $jobuser->email;
							$name = $jobuser->name;
							$subject = 'Job ' .$job->name. ' is clockout due to injured.';
							$body = @Template::where('type', 6)->orderBy('id', 'DESC')->first()->content;
							
							$content = array('name' => $name,'job_name'=>$job->name,'jobclient_name'=>$jobclient_name,'description'=>$description);
							foreach ($content as $key => $parameter) 
							{
								$body = str_replace('{{' . $key . '}}', $parameter, $body);
							}
                            try{
                                Mail::send('emails.name', ['template' => $body, 'name' => $name,'job_name'=>$job->name,'jobclient_name'=>$jobclient_name,'description'=>$description], function ($m) use ($from_email, $email, $name, $subject) 
                                {
                                    $m->from($from_email, 'See Job Run');

                                    $m->to($email, $name)->subject($subject);
                                });
                            } catch (\Exception $e) {
                                // Log the error but do not show it to the user
                                //Log::error('Error sending email: ' . $e->getMessage());
                            }

					}
					$clocktime->injoyed = $request->injoyed;
					$clocktime->clockstatus = 1;
                    $clocktime->save();
				   return response()->json([
						'success' => true,
						'message' => 'You have successfully clockout.',
					]); 
		}else{
				return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
		}
}
public function get_allClocks(Request $request){
	$validator = Validator::make($request->all(), [
            'token' => 'required',
			'job_id' => 'required',
			'from_date'=>'required',
			'to_date' =>'required'
        ]);
        
	if ($validator->fails()) 
	{
		return response()->json(['error' => $validator->messages()], 200);
	}
	$user = JWTAuth::authenticate($request->token);
	 try
        {
		  $clockdetails = $user->clocktime()->where('job_id','=',$request->job_id)->whereBetween('tdate',[$request->from_date,$request->to_date])->groupBy('tdate')->orderBy('id','DESC')->get();
		   return response()->json(
            [
                'success' => true,
                'tsheetclockins' => $clockdetails
                
            ]);
		} catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
}
public function getTsheetDetails(Request $request){
	$validator = Validator::make($request->all(), [
            'token' => 'required',
			'job_id' => 'required',
			'tdate'  => 'required'
        ]);
        
	if ($validator->fails()) 
	{
		return response()->json(['error' => $validator->messages()], 200);
	}
	$user = JWTAuth::authenticate($request->token);
	
	try{
		$totalSeconds =0;
		
		$clockdetails = Clocktime::where('tdate','=',$request->tdate)->where('job_id','=',$request->job_id)->get();
		
		foreach($clockdetails as $clockdetail){
			
			if($clockdetail->clockout=== null){
				$clockoutt =  date('H:i:s', time());
				$startTime = Carbon::parse($clockdetail->tdate.' '.$clockdetail->clockin);
				$endTime = Carbon::parse($clockdetail->tdate.' '.$clockoutt);
				$dsecond = $endTime->diffInSeconds($startTime);
				$totalSeconds += $diffsecond;
						
			}
			else{
				$startTime = Carbon::parse($clockdetail->tdate.' '.$clockdetail->clockin);
				$endTime = Carbon::parse($clockdetail->tdate.' '.$clockdetail->clockout);
				$diffsecond = $endTime->diffInSeconds($startTime);
				$totalSeconds += $diffsecond;
			}
		}
		 $day = floor($totalSeconds / 86400);
		 $htotal = floor(($totalSeconds -($day*86400)) / 3600).' hrs '.floor(($totalSeconds / 60) % 60).' min';
		 
		 return response()->json(
            [
                'success' => true,
                'tsheetdetail' => $clockdetails,
                'totalhours' => $htotal,
            ]);
		 
		
	}catch (\Exception $e) 
	{
		return response()->json([
			'success' => false,
			'message' => 'Token is not valid. please contact to the admin.',
		]);	
	}
}

public function addJobStage(Request $request){
	$validator = Validator::make($request->all(), [
            'token' => 'required',
			'name'  => 'required',
			'job_id' => 'required',
            
        ]);
		
	if ($validator->fails()) 
	{
		return response()->json(['error' => $validator->messages()], 200);
	}	
	$user = JWTAuth::authenticate($request->token);
	try
        {
		if($user){
			$stage = $user->stage()->make();
			$stage->name = $request->name;
			$stage->status = '1';
			$stage->save();
			$jobstage = $user->jobstage()->make();
			$jobstage->user_id 	= $user->id;
			$jobstage->job_id  	= $request->job_id;
			$jobstage->stage_id	= $stage->id;
			if($request->template_id){
				$jobstage->template_id = $request->template_id;
			}else{
				$jobstage->template_id = 0;
			}
			$jobstage->save();
			
			 return response()->json([
						'success' => true,
						'message' => 'Your job Stage has been Saved Successfully.'
					]);
					
			/*  $stage = $user->stage()->where('name', '=', $request->name)->first();
			  if($stage){
				return response()->json([
					'success' => false,
					'message' => 'This Stage name is Already Exits.'
				]);
			  }else{
					$stage = $user->stage()->make();
					$stage->name = $request->name;
					$stage->status = '1';
					$stage->save();
					
					if($stage->id){
                        $template_exits = $user->stagetemplate()->where('id', '=', $request->template_id)->first();
                        $getStage = $template_exits->stage_id;
                        $arrayData = unserialize($getStage);
                        $arrayData[] = $stage->id;

                        $serializedData = serialize($arrayData);
                        $stagetemplate = stagetemplate::findorfail($request->template_id);
                        $stagetemplate->stage_id = $serializedData;
                        $stagetemplate->save(); 

							$jobstage = $user->jobstage()->make();
							$jobstage->user_id 	= $user->id;
							$jobstage->job_id  	= $request->job_id;
							$jobstage->stage_id	= $stage->id;
							if($request->template_id){
								$jobstage->template_id = $request->template_id;
							}else{
								$jobstage->template_id = 0;
							}
							$jobstage->save();
					} 
				 return response()->json([
						'success' => true,
						'message' => 'Your job Stage has been Saved Successfully.'
					]);
			  } */
		}else{
			return response()->json([
				'success' => false,
				'message' => 'Token is not valid. please contact to the admin.',
			]);
		}
	}catch (\Exception $e) 
	{
		return response()->json(
		[
			'success' => false,
			'message' =>  $e->getMessage(),
		]);
	}
}

public function deleteJobStage(Request $request){
	$validator = Validator::make($request->all(), [
			'token' => 'required',
			'id'  => 'required',
            
		]);
		
	if ($validator->fails()) 
	{
		return response()->json(['error' => $validator->messages()], 200);
	}
	
	$user = JWTAuth::authenticate($request->token);
	try
        { 
		if($user){
			$stage = $user->stage()->where('id', '=', $request->id)->delete();
			$jobstage = $user->jobstage()->where('stage_id', '=', $request->id)->delete();

           /*  $template_exits = $user->stagetemplate()->where('id', '=', $request->template_id)->first();
            $getStage = $template_exits->stage_id;
            $arrayData = unserialize($getStage);
            $key = array_search($request->id, $arrayData);
            unset($arrayData[$key]);
            $serializedData = serialize($arrayData);

            $stagetemplate = stagetemplate::findorfail($request->template_id);
            $stagetemplate->stage_id = $serializedData;
            $stagetemplate->save(); */

			return response()->json(['success' => true, 'message' => 'You have successfully Deleted job stage.']);
			
		}else{
			return response()->json([
					'success' => false,
					'message' => 'Token is not valid. please contact to the admin.',
				]);
		}
	}catch (\Exception $e) 
		{
			return response()->json(
			[
				'success' => false,
				'message' =>  $e->getMessage(),
			]);
		}
	
}

public function addJobAttachment(Request $request){
	$validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
		   'job_id'=> 'required',
           'name' => 'required',
           'file_name' => 'required',
           'type' => 'required'
        ]);
	
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   
		
        $user = JWTAuth::authenticate($request->token);
		
		 try
        {  
		if($user){
				$media = $user->media()->make();
				 $media->name = $request->name;
                
				if ($request->type == 1) {
					$file_name1 = mediaDocument($request->file_name, $request->extension);
					$media->image = $file_name1;
				} else {
					$file_name2 = mediaImage($request->file_name, $request->type);
					$media->image = $file_name2;
				}
                $media->status =1;
                $media->type = $request->type;
                $media->save();
				$media_id = $media->id;
				if($media_id){
					$jobmedia = $user->jobmedia()->make();
					$jobmedia->job_id = $request->job_id;
					$jobmedia->media_id = $media_id;
					$jobmedia->save();
					if($request->type==1){
						 return response()->json([
							'success' => true,
							'message' => 'Your documents have been saved.'
						]);	
					}else{
						return response()->json([
							'success' => true,
							'message' => 'Your photo has been saved.'
						]);	
					}
				} 
			}else{
				return response()->json([
					'success' => false,
					'message' => 'Token is not valid. please contact to the admin.',
				]);
			}
		 }catch (\Exception $e) 
		{
			return response()->json(
			[
				'success' => false,
				'message' =>  $e->getMessage(),
			]);
		} 
}
public function deleteJobAttachment(Request $request){
	$validator = Validator::make($request->all(), 
        [ 
			'token' => 'required',
			'media_id'=>'required'
        ]);
	
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   
		
        $user = JWTAuth::authenticate($request->token);
		try
        {
			if($user){
				
				$jobmedia = $user->jobmedia()->where('media_id', '=', $request->media_id)->delete();
				$media 	  = $user->media()->where('id', '=', $request->media_id)->delete();
				
				
				 return response()->json([
					'success' => true,
					'message' => 'Your JobMedia has been deleted Successfully.'
				]);
				
			}else{
				return response()->json([
					'success' => false,
					'message' => 'Token is not valid. please contact to the admin.',
				]);
			}
		}catch (\Exception $e) 
		{
			return response()->json(
			[
				'success' => false,
				'message' =>  $e->getMessage(),
			]);
		}
}
public function updateSingleJobNotePad(Request $request){
	$validator = Validator::make($request->all(), 
        [ 
			'token' => 'required',
			'id' => 'required',
			'job_id'=>'required',
			'title' => 'required',
			'room' => 'required',
			'priority' => 'required',
			'assign_to' => 'required',
			'startdate' => 'required',
			'enddate' => 'required'
			
        ]);
	
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   
		
        $user = JWTAuth::authenticate($request->token);
		try
        {
			if($user){
				
				$user_id = $user->id;
				$punchlist = punchlist::where('id','=',$request->id)->where('user_id','=',$user_id)->where('job_id','=',$request->job_id)
				->update(['title'=>$request->title,'description'=>$request->description,'room'=>$request->room,
				'priority'=>$request->priority,'contact_id'=>$request->assign_to,'startdate'=>$request->startdate,
				'enddate'=>$request->enddate]);
				
				$i=0;
                if(!empty($request->image))
                {
                    foreach($request->image as $punchimgkey) 
                    {
                        $i++;
                        $punchimg = $user->punchlistimg()->make();

                        $punchimg->punch_id = $request->id;

                        if ($punchimgkey) 
                        {
                            $frontimage = str_replace("data:image/jpeg;base64,", '', $punchimgkey);
                            $frontimage1 = str_replace(" ","+",$frontimage);

                            $profile_pic = time().$i.'.jpeg';
                            file_put_contents($profile_pic, base64_decode($frontimage1));
                            $punchimg->image = $profile_pic;
                        }
                       
                        $punchimg->save();
                    }
                }
				
					 return response()->json([
						'success' => true,
						'message' => 'Your punchlist has been update successfully.'
					]);
				
			}else{
				return response()->json([
					'success' => false,
					'message' => 'Token is not valid. please contact to the admin.',
				]);
			}
		}catch (\Exception $e) 
		{
			return response()->json(
			[
				'success' => false,
				'message' =>  $e->getMessage(),
			]);
		}
	
	
}
public function changeorderList(Request $request){
	
	   $validator = Validator::make($request->all(), [
            'token' => 'required',
			'job_id' => 'required'
        ]);

        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()]);
        }

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
				
				$jobcontact = Contact::where('contact_user_id','=',$user->id)->first();
				
				if(@$jobcontact){
					//$changeorderdata = Changeorder::with(['job','item'])->where('job_id','=',$request->job_id)->where('client_id', 'LIKE', '%'.$jobcontact->id.'%')->orWhere('user_id','=',$user->id)->get();
					$changeorderdata = Changeorder::with(['job','item'])->where('job_id','=',$request->job_id)->get();
				}else{
				
					$changeorderdata = $user->changeorder()->with(['job','item'])->where('job_id','=',$request->job_id)->get();
					
				}
                return response()->json([
                    'success' => true,
                    'changeOrder' => $changeorderdata
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
}


public function addSingleContact(Request $request){

	$validator = Validator::make($request->all(), [
		'token' => 'required',
		'job_id'=>'required',
		'contact_id'=>'required'
	]);

	if ($validator->fails()) 
	{
		return response()->json(['error' => $validator->messages()]);
	}
	$user = JWTAuth::authenticate($request->token);
	
	try
        {
            if($user)
            {
                $jobcontact = Jobcontacts::make();
				$jobcontact->user_id = $user->id;
				$jobcontact->job_id = $request->job_id;
				$jobcontact->contact_id = $request->contact_id;
				$jobcontact->save();
				 if($request->contact_id){
					 $conatct_id= $request->contact_id;
					 $conatct_type = Contact::where('id','=',$conatct_id)->first();
					 $cnttype = $conatct_type->type;
					 if($cnttype == 7){
						$job_id = $request->job_id;
					
						$jobins = $user->jobinspection()->make();
						$jobins->job_id = $job_id;
						$jobins->contact_id = $request->contact_id;
						$jobins->save();
					 }
				 }
				// add default value in shared contact table
					$contactshared = Contactshared::make();
                    $contactshared->user_id = $user->id;
					$contactshared->contact_id = $request->contact_id;
                    $contactshared->job_id = $request->job_id;
					$contactshared->jobnotepad = 0;
                    $contactshared->punchlist = 0;
                    $contactshared->stage = 0;
                    $contactshared->contact = 0;
                    $contactshared->document = 0;
					$contactshared->calendar= 0;
                    $contactshared->pictures = 0; 
					$contactshared->general = 0;
                    $contactshared->todo = 0;
                    $contactshared->save();
				
				 $job = Job::where('id','=',$request->job_id)->first();
				 $jobname = $job->name;
					$contacts = Contact::find($request->contact_id);
					$contact_user_id = $contacts->contact_user_id;
					if($contact_user_id){
						//notification send	
						$msg["title"] = "New Job";
						$msg["body"] = "You have received an invitation to join ".'"'.$jobname.'"';
						$msg['type'] = "job";
						$msg['client_id'] = $contacts->contact_user_id;
						$msg['user_type'] = $this->user_type($contacts->type);
						$msg['move'] = 'Home';
						
					
							$this->sendNotification($user->id, $msg);
						
						
					}else{
							$from_email = Config::get('mail.from.address');
							$email = $contacts->email;
							$name = ucwords($contacts->name);
							$job_owner_email = ucwords($user->name);
							$subject = "You have received an invitation to join ". '"'.$jobname.'"';

							$body = @Template::where('type', 8)->orderBy('id', 'DESC')->first()->content;
							
							$content = array('name' => $name,'job_owner_email'=>$job_owner_email);
							foreach ($content as $key => $parameter) 
							{
								$body = str_replace('{{' . $key . '}}', $parameter, $body);
							}

                            if($from_email)
                            {
                                try{
                                    Mail::send('emails.name', ['template' => $body, 'name' => $name,'job_owner_email'=>$job_owner_email], function ($m) use ($from_email, $email, $name, $subject,$job_owner_email) 
                                    {
                                        $m->from($from_email, 'See Job Run');

                                        $m->to($email, $name)->subject($subject);
                                    });
                                } catch (\Exception $e) {
                                    // Log the error but do not show it to the user
                                    //Log::error('Error sending email: ' . $e->getMessage());
                                }
                            }

						
					} 
					
						
				
				 return response()->json([
					'success' => true,
					'message' => 'Your jobcontact has been added Successfully.'
				]);
				
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }

}

public function deleteSingleContact(Request $request){

	$validator = Validator::make($request->all(), [
		'token' => 'required',
		'job_id' => 'required',
		'contact_id'=>'required'
		
		
	]);

	if ($validator->fails()) 
	{
		return response()->json(['error' => $validator->messages()]);
	}
	$user = JWTAuth::authenticate($request->token);
	
	try
        {
            if($user)
            {
				$jobcontact = Jobcontacts::where('job_id','=',$request->job_id)->where('contact_id','=',$request->contact_id)->delete();
				
				 return response()->json([
					'success' => true,
					'message' => 'Your jobcontact has been deleted Successfully.'
				]);
				
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }

}
public function updateSingleContact(Request $request){
	$validator = Validator::make($request->all(), [
		'token' => 'required',
		'id'=>'required',
		'job_id'=>'required',
		'contact_id'=>'required'
		
	]);

	if ($validator->fails()) 
	{
		return response()->json(['error' => $validator->messages()]);
	}
	$user = JWTAuth::authenticate($request->token);
	try
	   {
		if($user){
			
			$jobcontact = Jobcontacts::findorfail($request->id);
			$jobcontact->job_id = $request->job_id;
			$jobcontact->contact_id = $request->contact_id;
			$jobcontact->save();
			
			 return response()->json([
				'success' => true,
				'message' => 'Your jobcontact has been updated Successfully.'
			]);
			
		}else{
			return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
		}
	   }catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
}

public function changeorderDetails(Request $request){
	$validator = Validator::make($request->all(), [
		'token' => 'required',
		'id'=>'required',
	]);

	if ($validator->fails()) 
	{
		return response()->json(['error' => $validator->messages()]);
	}
	$user = JWTAuth::authenticate($request->token);
	
	try{
		if($user){
			
			//$changeorder_details = $user->changeorder()->with(['job','item'])->where('id','=',$request->id)->get();
			$changeorder_details = Changeorder::with(['job','user','user.meta'=> function($q) {
                        $q->where('key','=','Business_name');
                    },'item'])->where('id','=',$request->id)->get();
				$contact = array();
					 if($changeorder_details){
						foreach($changeorder_details as $details){
							
							if(@$details['client_id']){
								foreach($details['client_id'] as $client_id){
									
									$contact[] = Contact::where('id','=',$client_id)->get();
									
								}
							}
							$job_clientId = $details['job']['client_id'];
							$jobclient_details = Contact::where('id','=',$job_clientId)->get();
							
						}
											
						//$userMeta = Meta::whereMeta('App\Models\User', $userID, $metaKey, $metaValue)->get();
					}  
		
			
			
			  return response()->json([
					'success' => true,
					'changeOrder' => $changeorder_details,
					'contact'=> $contact,
					'jobClientDetails'=> $jobclient_details
					
				]);
			
		}
		else{
			
			return response()->json([
				'success' => false,
				'message' => 'Token is not valid. please contact to the admin.',
			]); 
        }
		
		
	}catch (\Exception $e){
		 return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
	} 
}

public function addNewChangeOrder(Request $request){
	
	
	$validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'job_id' => 'required',
           'clientId' => 'required',
           'date' => 'required',
           'title' => 'required'
		  
        ]);
		
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   

        $user = JWTAuth::authenticate($request->token);
		/* echo 'Clientid-> ' .$request->clientId;
		echo 'ItemAll-> '. $request->ItemAll; */
		// return response()->json([
					// 'message'=>array(
                   
                    // 'Clientid' => $request->clientId,
					// 'ItemAll' => $request->ItemAll)
                // ]);
		// die;
        try
        {
            if($user)
            {
				
                $changeorder = $user->changeorder()->make();
                $changeorder->title = $request->title;
                $changeorder->job_id = $request->job_id;
				
				$cldid = $request->clientId;
				$clientIDS = json_decode($cldid,true);
				foreach(@$clientIDS as $clientidsss){
						$client_ids[] = $clientidsss["id"];
						if(@$clientidsss["id"]){
							$contacts = Contact::where('id', '=', $clientidsss["id"])->whereNotNull('contact_user_id')->first();
							 if($contacts){
									/*for send and save notification  */
									
											$msg["title"] =  $request->title;
											$msg["body"] = 'Your order has been changed !';
											$msg['type'] = 'Client';
											$msg['user_id'] = $user->id;
											$msg['client_id'] = $contacts->contact_user_id;
											$msg['move'] = 'Home';
											$this->sendNotification($user->id, $msg);
											
									/*for send and save notification  */
							 }
						}
				}
				
				$changeorder->client_id = serialize(@$client_ids);
                $changeorder->date = $request->date;
				$changeorder->receiptNo = $request->receiptNo;
                $changeorder->status = 'New';
                $changeorder->save();
			
			   
				if($request->ItemAll){
					$items  = json_decode($request->ItemAll,true);
					foreach(@$items as $itemss){	
					$item = $user->item()->make();
                    $item->order_id = $changeorder->id;
					
                    $item->item_code = $itemss['code'];
                    $item->title = $itemss['name'];
                    $item->description = $itemss['desc'];
                    $item->cost = $itemss['amount'] ;
                    $item->save();
						
					}
				}
			
			$job = Job::where('id','=',$request->job_id)->first();
			if(@$job){
				if($job->client_id){
					 $contact = Contact::where('id', '=', $job->client_id)->whereNotNull('contact_user_id')->first();
					 if($contact){
						 	/*for send and save notification  */
							
									$msg["title"] =  $request->title;
									$msg["body"] = 'Your order has been changed !';
									$msg['type'] = 'Client';
									$msg['user_id'] = $user->id;
									$msg['client_id'] = $contact->contact_user_id;
									$msg['move'] = 'Home';
									$this->sendNotification($user->id, $msg);
									
							/*for send and save notification  */
					 }
				}
			}
			
               /*  for ($x = 0; $x < count($request->item['code']); $x++) 
                {
                    $item = $user->item()->make();

                    $item->order_id = $changeorder->id;
                    $item->item_code = $request->item['code'][$x];
                    $item->title = $request->item['title'][$x];
                    $item->description = $request->item['description'][$x];
                    $item->cost = $request->item['cost'][$x];

                    $item->save();
                } */
				
                return response()->json([
                    'success' => true,
                    'message' => 'Your New ChangeOrder has been Saved Successfully.'
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]); 
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
}


public function GetEventNotification(Request $request){
	
	$validator = Validator::make($request->all(), 
        [ 
           'token' => 'required'
        ]);
		
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   

        $user = JWTAuth::authenticate($request->token);
		$user_id = $user->id;
		   try
			{  
				if($user)
				{
					$notification = Notification::where('client_id','=',$user_id)->orderBy('id', 'desc')->get();
					$unread_notification = Notification::where('client_id','=',$user_id)->where('status','=',1)->count();
				    $today_date = Carbon::today();
					$events = Events::where('startdate', '<=', $today_date)->where('enddate', '>=', $today_date)->where('user_id',$user->id)->where('status',1)->get();
					if($events){
						$event_array = array();
						foreach($events as $event){
							$alldates = $this->getDatesFromRange($event->startdate,$event->enddate);
							
							$event_array[] = array(
												 'title'=>$event->title,
												 'notification_alert'=>$event->notification_alert,
												 'description'=>$event->description,
												 'startdate' => $event->startdate,
												 'enddate' => $event->enddate,
												 'alldates' => $alldates
												);
							
						}
					}
					
					return response()->json([
						'success' => true,
						'events' =>  $event_array,
						'notification' => $notification,
						'unreadnotification'=>$unread_notification
					]);
				}else{
					 return response()->json([
						'success' => false,
						'message' => 'Token is not valid. please contact to the admin.',
					]); 
					
				}
			}catch (\Exception $e) 
			{
				return response()->json(
				[
					'success' => false,
					'message' =>  $e->getMessage(),
				]);
			}
	
}

private function getDatesFromRange($date_time_from, $date_time_to)
{

	// cut hours, because not getting last day when hours of time to is less than hours of time_from
	// see while loop
	$start = Carbon::createFromFormat('Y-m-d', substr($date_time_from, 0, 10));
	$end = Carbon::createFromFormat('Y-m-d', substr($date_time_to, 0, 10));

	$dates = [];

	while ($start->lte($end)) {

		$dates[] = $start->copy()->format('Y-m-d');

		$start->addDay();
	}

	return $dates;
}

public function sendNotification_old($user_id, $msgdata = array())
{		
		$client_id = $msgdata['client_id'];
		$SERVER_API_KEY = 'AAAApzVhLyU:APA91bHHGMW5eOGHIOSPekapwfsew2XhbRjTb_oD2KsQpUvuEbRe1bYA6itGLjFimsP532Z58zKJYEyqTQnEfKCcj8AorrOzbfwQ2qRMSkoIL57e-UYI_WwkROriNf4AWMDQvJME6yV5';
		
		$regfcm = FcmToken::whereUserId($client_id)->first();
		if(@$regfcm){
			$msgdata['sound'] = 'default';
			$data = [
				"registration_ids" => array($regfcm->fcmtoken),
				"data" => $msgdata,
				"notification" => $msgdata
			]; 
		
			$saveNotification = Notification::make();
			$saveNotification->user_id = $user_id;
			$saveNotification->title = $msgdata['title'];
			$saveNotification->body = $msgdata['body'];
			$saveNotification->type = $msgdata['type'];
			$saveNotification->client_id = @$msgdata['client_id'];
			$saveNotification->save();
			$dataString = json_encode($data);

			$headers = [
				'Authorization: key=' . $SERVER_API_KEY,
				'Content-Type: application/json',
			];

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

			$response = curl_exec($ch);
		}
}

	public function savefcmToken(Request $request)
	{
		
		$validator = Validator::make($request->all(), [
			'token' => 'required',
			'fcmtoken' => 'required',

		]);
		if ($validator->fails()) {
			return response()->json(['error' => $validator->messages()], 200);
		}

		$user = JWTAuth::authenticate($request->token);
		
		if ($user) {
			$fcm = $user->fcmToken()->first();
			
			if ($fcm) {
				$fcmtoken = FcmToken::findorfail($fcm->id);
				$fcmtoken->user_id = $user->id;
				$fcmtoken->fcmtoken = $request->fcmtoken;
				$fcmtoken->save();
			} else {
				$fcmtoken = FcmToken::make();
				$fcmtoken->user_id = $user->id;
				$fcmtoken->fcmtoken = $request->fcmtoken;
				$fcmtoken->save();
			}

			return response()->json(['success' => true,  'message' => 'Token saved successfully.']);
		} else {
			return response()->json(['success' => false, 'message' => 'Unauthorized user.Please try again or contact to admin.']);
		}
	}


	public function saveNotifications(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'token' => 'required',
			'title' => 'required',
			'body' => 'required',
			'user_id' => 'required',
			'type' => 'required'
			

		]);
		if ($validator->fails()) {
			return response()->json(['error' => $validator->messages()], 200);
		}
		
		
		
		$user = JWTAuth::authenticate($request->token);
		
		if ($user) {
			
			$notification = Notification::make();
			$notification->user_id = $user->id;
			$notification->title = $request->title;
			$notification->body = $request->body;
			$notification->type = $request->type;
			$notification->client_id = $request->user_id;
			
			// $notification->save();

			/*for send and save notification  */
			$msg["title"] =  $request->title;
			$msg["body"] = $request->body;
			$msg['type'] = $request->type;
			$msg['user_id'] = $user->id;
			$msg['client_id'] = $request->user_id;
			$msg['move'] = 'Home';
			
			$this->sendNotification($user->id, $msg);
			/*for send and save notification  */
			return response()->json(['success' => true,  'message' => 'saved successfully.']);
		} else {
			return response()->json(['success' => false, 'message' => 'Unauthorized user.Please try again or contact to admin.']);
		}
	}
	public function readNotificationStatus(Request $request)
	{
			$validator = Validator::make($request->all(), [
				'token' => 'required',
				'id' => 'required',
				'status' => 'required'

			]);
			if ($validator->fails()) {
				return response()->json(['error' => $validator->messages()], 200);
			}

			$user = JWTAuth::authenticate($request->token);
			if ($user) {
				$notification = Notification::find($request->id);
				$notification->status = $request->status;
				$notification->save();
				return response()->json(['success' => true,  'message' => 'read successfully.']);
			} else {
				return response()->json(['success' => false, 'message' => 'Unauthorized user.Please try again or contact to admin.']);
			}
	}
	public function getUnreadNotifications(Request $request)
	{
			$validator = Validator::make($request->all(), [
				'token' => 'required'

			]);
			if ($validator->fails()) {
				return response()->json(['error' => $validator->messages()], 200);
			}

			$user = JWTAuth::authenticate($request->token);
			if ($user) {
				$notification = $user->notifications()->whereStatus(1)->get();
				return response()->json(['success' => true,  'notifications' => $notification]);
			} else {
				return response()->json(['success' => false, 'message' => 'Unauthorized user.Please try again or contact to admin.']);
			}
	}
	public function getAllNotifications(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'token' => 'required'

		]);
		if ($validator->fails()) {
			return response()->json(['error' => $validator->messages()], 200);
		}

		$user = JWTAuth::authenticate($request->token);
		if ($user) {
			$notification = $user->notifications()->orderBy('id', 'desc')->paginate(20);
			return response()->json(['success' => true,  'notifications' => $notification]);
		} else {
			return response()->json(['success' => false, 'message' => 'Unauthorized user.Please try again or contact to admin.']);
		}
	}
 public function user_type($type){
	 $utype='';
	 if($type==1){
		 $utype = 'Client';
	 }
	 if($type==2){
		 $utype = 'Sub Contractor';
	 }
	 if($type==3){
		 $utype = 'Employee';
	 }
	 if($type==4){
		 $utype = 'General Contractor';
	 }if($type==5){
		 $utype = 'Architect/Engineer';
	 }
	 if($type==6){
		 $utype = 'Interior Designer';
	 }
	 if($type==7){
		 $utype = 'Inspector';
	 } 
	 if($type==8){
		 $utype = 'Bookkeeper';
	 }
	 return $utype;
 }
 
public function deleteJob(Request $request){
	$validator = Validator::make($request->all(), [
		'token' => 'required',
		'job_id' => 'required'
	]);
	if ($validator->fails()) {
		return response()->json(['error' => $validator->messages()], 200);
	}
	
	$user = JWTAuth::authenticate($request->token);
	if($user){
		$job = Job::find($request->job_id);
		$job->delete();
		 return response()->json([
			'success' => true,
			'message' => 'You have successfully deleted your job.',
		]);
	}else{
		 return response()->json([
			'success' => false,
			'message' => 'Token is not valid. please contact to the admin.',
		]); 
	}
	
}

public function getAllJobsForClock(Request $request){
	
		$validator = Validator::make($request->all(), [
			'token' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error' => $validator->messages()], 200);
		}
		
		$user = JWTAuth::authenticate($request->token);
		
		if($user){
			$user_id = $user->id;
			$contact_id = $user->contactuserid()->where('contact_user_id','=',$user_id)->get();
			
			if(count($contact_id)>0){
				$cont_id = $contact_id[0]->id;
				$jobdata = job::with(['user', 'jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->whereHas('jobcontact', function($q) use($cont_id) {$q->where('contact_id', '=', $cont_id); })->where('status','!=',2)->paginate(10);
				return response()->json([
                    'success' => true,
                    'Job' => $jobdata
                ]);
			}else{
				return response()->json([
                    'success' => true,
                    'Job' => array('data'=>[])
                ]);
				
			}	
		}else{
			
			return response()->json([
				'success' => false,
				'message' => 'Token is not valid. please contact to the admin.',
			]); 
			
		}
}

 public function checkJobpermission(Request $request){
	$validator = Validator::make($request->all(), [
			'token'  => 'required',
			'job_id' => 'required'
		]);
		if ($validator->fails()) {
			return response()->json(['error' => $validator->messages()], 200);
		}
		
		$user = JWTAuth::authenticate($request->token);
		
		if($user){
			$jobs = job::with(['user','contact','contact.Contactshared'])->where('id','=',$request->job_id)->get();
			return response()->json([
                    'success' => true,
                    'Jobs' => $jobs
                ]);
		}else{
			return response()->json([
				'success' => false,
				'message' => 'Token is not valid. please contact to the admin.',
			]);
		}
	
} 

public function blanktemplate(Request $request)
    {
        $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'name' => 'required',
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $arrayData = array();
                array_push($arrayData, 0);

                $stagetemplate = $user->stagetemplate()->make();

                $stagetemplate->name = $request->name;
                $stagetemplate->status = '1';
                $stagetemplate->stage_id = serialize($arrayData);
                $stagetemplate->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Your blank template has been successfully created.'
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function stagetemplate(Request $request)
    {
        $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'id' => 'required',
           'stage_name' => 'required',
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
                $stage = $user->stage()->make();
                $stage->name = $request->stage_name;
                $stage->status = '1';
                $stage->save();

                $lastInsertedID = $stage->id;

                $template_exits = $user->stagetemplate()->where('id', '=', $request->id)->first();
               
                if($template_exits)
                {
                    $getStage = $template_exits->stage_id;
                    $arrayData = unserialize($getStage);
                    $arrayData[] = $lastInsertedID;
                    
                    $serializedData = serialize($arrayData);
                    $stagetemplate = stagetemplate::findorfail($request->id);
                    $stagetemplate->stage_id = $serializedData;
                    if ($stagetemplate->save()) 
                    {
                        return response()->json([
                            'success' => true, 
                            'message' => 'Stage has been saved successfully.',
                        ]);
                    } 
                    else 
                    {
                        return response()->json([
                            'success' => false, 
                            'message' => 'Unable to update Stage.Please try again or contact to admin.',
                        ]);
                    } 
                }
                else
                {
                    return response()->json([
                        'success' => false, 
                        'message' => 'You can not update this Template.',
                    ]);
                }
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function deletestagetemplate(Request $request)
    {
        $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'stage_id' => 'required',
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
				
				$delete_stage = $user->stage()->where('id', '=', $request->stage_id)->delete();
				if ($delete_stage) 
				{
					return response()->json([
						'success' => true, 
						'message' => 'Stage has been delete successfully.',
					]);
				} 
				else 
				{
					return response()->json([
						'success' => false, 
						'message' => 'Unable to update Stage.Please try again or contact to admin.',
					]);
				}
				
				
              /*   $template_exits = $user->stagetemplate()->where('id', '=', $request->id)->first();
               
                if($template_exits)
                {
                    $getStage = $template_exits->stage_id;
                    $arrayData = unserialize($getStage);
                    $key = array_search($request->stage_id, $arrayData);
                    unset($arrayData[$key]);
                    $serializedData = serialize($arrayData);

                    $stagetemplate = stagetemplate::findorfail($request->id);
                    $stagetemplate->stage_id = $serializedData;
                    $stagetemplate->save();

                    $delete_stage = $user->stage()->where('id', '=', $request->stage_id)->delete();

                    if ($delete_stage) 
                    {
                        return response()->json([
                            'success' => true, 
                            'message' => 'Stage has been delete successfully.',
                        ]);
                    } 
                    else 
                    {
                        return response()->json([
                            'success' => false, 
                            'message' => 'Unable to update Stage.Please try again or contact to admin.',
                        ]);
                    } 
                }
                else
                {
                    return response()->json([
                        'success' => false, 
                        'message' => 'You can not update this Template.',
                    ]);
                } */
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function addJob2(Request $request)
    {
        $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'name' => 'required',
           'mobile' => 'required',
           'client_id' => 'required',
           'job_type' => 'required',
           'address' => 'required',
           'city' => 'required',
           'state' => 'required',
           'pincode' => 'required',
           'contract_status' => 'required',
           'inspection' => 'required',
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   

            $user = JWTAuth::authenticate($request->token);
        try
        {
            if($user)
            {
				$job_name = $request->name;
                $job = $user->job()->make();

                $job->name = $request->name;
                $job->mobile = $request->mobile;
                $job->client_id = $request->client_id;
				if($request->gate_no)
                {
					$job->gate_no = $request->gate_no;
				}
                $job->job_type = $request->job_type;
				if($request->permit_no)
                {
					$job->permit_no = $request->permit_no;
				}
                $job->address = $request->address;
                $job->city = $request->city;
                $job->state = $request->state;
                $job->pincode = $request->pincode;
                $job->contract_status = $request->contract_status;
                $job->status = '1';
                $job->save();
				$jobname = $request->name;
                $job_id = $job->id;
				
                if($request->templateid != 0 && $request->templateid != null)
                {
                    $template_exits = $user->stagetemplate()->where('id', '=', $request->templateid)->first();
                    $getStage = $template_exits->stage_id;
                    $arrayData = unserialize($getStage);

                    foreach($arrayData as $stagekey) 
                    {
                        if($stagekey != 0)
                        {
                            $jobstage = $user->jobstage()->make();

                            $jobstage->job_id = $job_id;
                            $jobstage->stage_id = $stagekey;
                            $jobstage->template_id = $request->templateid;
                            $jobstage->save();
                        }
                    }
                }

                if($request->document != null)
                {
                    foreach($request->document as $documentkey) 
                    {
                        $jobmedia = $user->jobmedia()->make();

                        $jobmedia->job_id = $job_id;
                        $jobmedia->media_id = $documentkey;
                        $jobmedia->save();
                    } 
                }

                if($request->picture != null)
                {
                    foreach($request->picture as $picturekey) 
                    {
                        $jobmedia = $user->jobmedia()->make();

                        $jobmedia->job_id = $job_id;
                        $jobmedia->media_id = $picturekey;
                        $jobmedia->save();
                    }
                }

                if($request->contact != null)
                {
                    foreach($request->contact as $contactkey) 
                    {
                        $jobcontact = $user->jobcontact()->make();
                        $jobcontact->job_id = $job_id;
                        $jobcontact->contact_id = $contactkey;
                        $jobcontact->save();
                        
                        $contacts = Contact::find($contactkey);
                        $contact_user_id = $contacts->contact_user_id;
                        if($contact_user_id)
                        {
                            //notification send	
                            $msg["title"] = "New Job";
                            $msg["body"] = "You are invited to join this job ".$jobname;
                            $msg['type'] = "job";
                            $msg['client_id'] = $contacts->contact_user_id;
                            $msg['user_type'] = $this->user_type($contacts->type);
                            $msg['move'] = 'Home';
                            $this->sendNotification($user->id, $msg);
                        }
                        else
                        {
                            $from_email = Config::get('mail.from.address');
                            $email = $contacts->email;
                            $name = $contacts->name;
                            $subject = "New job added";

                            $body = @Template::where('type', 5)->orderBy('id', 'DESC')->first()->content;
                            
                            $content = array('name' => $name);
                            foreach ($content as $key => $parameter) 
                            {
                                $body = str_replace('{{' . $key . '}}', $parameter, $body);
                            }

                            if($from_email)
                            {
                                try{
                                    Mail::send('emails.name', ['template' => $body, 'name' => $name], function ($m) use ($from_email, $email, $name, $subject) 
                                    {
                                        $m->from($from_email, 'See Job Run');

                                        $m->to($email, $name)->subject($subject);
                                    });
                                } catch (\Exception $e) {
                                    // Log the error but do not show it to the user
                                // Log::error('Error sending email: ' . $e->getMessage());
                                }
                            }
                        }
                    }
                }
				
               /* foreach($request->inspection as $inspectionkey) 
                {*/
				
					if($request->inspection){
						
						$jobinspection1 = $user->jobinspection()->make();
						$jobinspection1->job_id = $job_id;
						$jobinspection1->contact_id = $request->inspection;
						$jobinspection1->save();
					}
                /*}*/

				
                return response()->json([
                    'success' => true,
                    'message' => 'Your Job has been Saved Successfully.'
                ]);
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function templateaddjob(Request $request)
    {
        $validator = Validator::make($request->all(), 
        [ 
           'token' => 'required',
           'template_id' => 'required',
           'job_id' => 'required',
        ]);
 
        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }   

        $user = JWTAuth::authenticate($request->token);

        try
        {
            if($user)
            {
			
				$stagetemplate1 = stagetemplate::findorfail($request->template_id);
				
				if($stagetemplate1){
					
						$id = $stagetemplate1->getAttribute('id');
						$stage_ids = unserialize($stagetemplate1->getAttribute('stage_id'));
						
						if($stage_ids[0]>0){
							foreach($stage_ids as $stg_id){
								
								$job_st = Jobstage::where('stage_id','=',$stg_id)->where('template_id','=',$request->template_id)->first();
								//print_r($job_st);
								
								 
								 if($job_st->stage_id){
									 
									$exist_stage = Stage::where('id','=',$job_st->stage_id)->first();
									
									
									$stage = $user->stage()->make();
									$stage->name = $exist_stage->name;
									$stage->progress_status = $exist_stage->progress_status;
									$stage->user_id = $user->id;
									$stage->status = '1';
									$stage->save();
									
									$jobstage = $user->jobstage()->make();
									$jobstage->job_id = $request->job_id;
									$jobstage->stage_id = $stage->id;
									$jobstage->template_id = $request->template_id;
									$jobstage->save();
									
										
								 }
								/* if(!empty($job_st)){
									
									$exist_stage = Jobstage::with(['Stage'])->where('stage_id','=',$stg_id)->where('template_id','=',$request->template_id)->first();
									$stage = $user->stage()->make();
									    $stage->name = $exist_stage['Stage']->name;
										$stage->progress_status = $exist_stage['Stage']->progress_status;
										$stage->status = '1';
										$stage->save();
										
										 $lastInsertedID = $stage->id;
										/*$stagetemplate = stagetemplate::findorfail($request->template_id);
										$stagetemplate->stage_id = serialize(array($lastInsertedID));
										$stagetemplate->save();
										
										$jobstage = $user->jobstage()->make();
										$jobstage->job_id = $request->job_id;
										$jobstage->stage_id = $lastInsertedID;
										$jobstage->template_id = $request->template_id; 
										  if ($jobstage->save()) 
											{
												return response()->json([
													'success' => true, 
													'message' => 'Template added to job successfully.',
												]);
											} 
											else 
											{
												return response()->json([
													'success' => false, 
													'message' => 'Unable to update Stage.Please try again or contact to admin.',
												]);
											}
									
								} */
								
							}
							
							return response()->json([
								'success' => true, 
								'message' => 'Template added to job successfully.',
							]);
							
						}else{
							
						   $stage = $user->stage()->make();
							if($request->name){
								$stage->name = $request->name;
							}else{
								$stage->name = 'Blank Template';
							}
							$stage->status = '1';
							$stage->save();

							$lastInsertedID = $stage->id;

							$stagetemplate = stagetemplate::findorfail($request->template_id);
							$stagetemplate->stage_id = serialize(array($lastInsertedID));
							$stagetemplate->save();

							$jobstage = $user->jobstage()->make();
							$jobstage->job_id = $request->job_id;
							$jobstage->stage_id = $lastInsertedID;
							$jobstage->template_id = $request->template_id;

							if ($jobstage->save()) 
							{
								return response()->json([
									'success' => true, 
									'message' => 'Template added to job successfully.',
								]);
							} 
							else 
							{
								return response()->json([
									'success' => false, 
									'message' => 'Unable to update Stage.Please try again or contact to admin.',
								]);
							} 	

						}

				}
             
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function stageorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
          /*   'from_stage_id' => 'required',
            'from_order' => 'required',
            'to_stage_id' => 'required',
            'to_order' => 'required', */
            'job_id' => 'required',
			'rearrangeorder'=>'required'
        ]);

        if ($validator->fails()) 
        {  
            return response()->json(['error'=>$validator->errors()]); 
        }  

        $user = JWTAuth::authenticate($request->token);
		
        try
        {
            if($user)
            {
				if($request->rearrangeorder){
					$reorder_array = $request->rearrangeorder;
					$reorders = json_decode($reorder_array,true);
						foreach($reorders as $reorder){
							$stage = Jobstage::WHERE('job_id', '=', $request->job_id)->WHERE('stage_id','=',$reorder['stage_id'])->first();
							$stage->stage_order = $reorder['changeOrder'];
							$stage->save();
						} 
						
						return response()->json([
							'success' => true, 
							'message' => 'Stage has been updated successfully.',
						]);	
				}else{
					 return response()->json([
                        'success' => false, 
                        'message' => 'Unable to update Stage.Please try again or contact to admin.',
                    ]);
				}
				
              /* $stage = Jobstage::WHERE('job_id', '=', $request->job_id)->WHERE('stage_id','=',$request->from_stage_id)->first();
                $stage->stage_order = $request->from_order;
                $stage->save();

               
                $stage1 = Jobstage::WHERE('job_id', '=', $request->job_id)->WHERE('stage_id','=',$request->to_stage_id)->first();
                $stage1->stage_order = $request->to_order; */
               
              /*   if ($stage1->save()) 
                {
                    return response()->json([
                        'success' => true, 
                        'message' => 'Stage has been updated successfully.',
                    ]);
                } 
                else 
                {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Unable to update Stage.Please try again or contact to admin.',
                    ]);
                } */ 
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);
            }
        }
        catch (\Exception $e) 
        {
            return response()->json(
            [
                'success' => false,
                'message' =>  $e->getMessage(),
            ]);
        }
    }
	
	
	
	/* for Ios payment */
	
public function makePaymentForIos(Request $request){
		$validator = Validator::make($request->all(), [
				'token' => 'required',
				'plan_id' => 'required',
				'receipt'=>'required'
				
			]);
			
			if ($validator->fails()) {
			return response()->json(['error' => $validator->messages()], 200);
		}
		
		$user = JWTAuth::authenticate($request->token);
		$user_id = JWTAuth::authenticate($request->token)->id;
		
		if($user_id){
			if($request->receipt){
			    $curl = curl_init();
				curl_setopt_array($curl, array(
				  //CURLOPT_URL => 'https://sandbox.itunes.apple.com/verifyReceipt',
				  CURLOPT_URL => 'https://buy.itunes.apple.com/verifyReceipt', 
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'POST',
				  CURLOPT_POSTFIELDS =>'{
				"receipt-data":"'.$request->receipt.'",
				"password":"d664e45baee94e9c8fbbbae38205129e",
				"exclude-old-transactions":true
				}',
				  CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json'
				  ),
				));
				$response = curl_exec($curl);
				curl_close($curl);						
			}
				$result = json_decode($response);
				
				if($result->status==0){
					$plan_id = $request->plan_id;
					
					$plan = Plan::where('id','=',$plan_id)->first();
					$price = str_replace('$', '', $plan->price);
					
				/* record save in payment table */
							
						 $customerpay = Payment::make();
							$customerpay->user_id = $user_id;
							$customerpay->amount = $price;
							$customerpay->status = 1;
						
							/* $customerpay->transaction_id = $result->latest_receipt_info[0]->transaction_id; */
							$customerpay->transaction_id = $result->latest_receipt_info[0]->original_transaction_id;
							/* $customerpay->ios_original_transaction_id = $result->latest_receipt_info[0]->original_transaction_id; */
							
							$customerpay->subscription_id = $result->latest_receipt_info[0]->transaction_id;
							$customerpay->payment_date = date('Y-m-d H:i:s', strtotime($result->latest_receipt_info[0]->purchase_date));
							$customerpay->save();
							/* record save in payment table */
							/* for selected plan */
							
							$selectedplans = $user->selectedPlan()->orderBy('id', 'desc')->first();
							if($selectedplans){
								 $selectedpln = SelectedPlan::where('user_id','=',$user_id)->orderBy('id', 'desc')->first();
								 
								/*  if($order_id==$selectedpln->subscription_id){ */
								
									 $selectedpln->start_date = date('Y-m-d H:i:s', strtotime($result->latest_receipt_info[0]->purchase_date));
									 $selectedpln->end_date = date('Y-m-d H:i:s', strtotime($result->latest_receipt_info[0]->expires_date));
									 $selectedpln->subscription_status = 0; 
									 $selectedpln->subscription_id = $result->latest_receipt_info[0]->original_transaction_id;
									 $selectedpln->plan_id = $plan_id; 
									 $selectedpln->purchase_token = '';
									 $selectedpln->status = 1;									 
									 $selectedpln->receipt = $request->receipt;
									 $selectedpln->save();	
								
							}else{
								
								$selectedPlan = SelectedPlan::make();
								$selectedPlan->user_id = $user_id;
								$selectedPlan->plan_id = $plan_id;
								$selectedPlan->start_date = date('Y-m-d H:i:s', strtotime($result->latest_receipt_info[0]->purchase_date));
								$selectedPlan->end_date = date('Y-m-d H:i:s', strtotime($result->latest_receipt_info[0]->expires_date));	
								$selectedPlan->status = 1;
								$selectedPlan->subscription_id = $result->latest_receipt_info[0]->original_transaction_id;
								$selectedPlan->purchase_token = '';
								$selectedPlan->subscription_status = 0;
								$selectedPlan->receipt = $request->receipt;
								$selectedPlan->save();
								
							}
							
							
							
							
							/* if ($plan_id == 1) {
								$selectedPlan->end_date = date('Y-m-d H:i:s', strtotime('+1 month', strtotime($result->latest_receipt_info[0]->purchase_date)));
							} else {
								$selectedPlan->end_date = date('Y-m-d H:i:s', strtotime('+1 year', strtotime($result->latest_receipt_info[0]->purchase_date)));
							} */

							
							/* for selected plan */
							return response()->json([
								'success' => true,
								'message' => 'Payment has been completed.',
							]);
		}else{
			return response()->json([
				'success' => false,
				'message' => 'Unable to payment. please try again or contact to the admin.',
			]);
		}
		}else {
			return response()->json([
				'success' => false,
				'message' => 'Token is not valid. please contact to the admin.',
			]);
		}
	}
	
/* for Ios payment */


public function makePaymentForAndroid(Request $request){
	
		$validator = Validator::make($request->all(), [
				'token' => 'required',
				'plan_id' => 'required',
				'receipt'=>'required'
			]);
		
		if ($validator->fails()) {
			return response()->json(['error' => $validator->messages()], 200);
		}
		
		
		
		$user = JWTAuth::authenticate($request->token);
		
		$user_id = JWTAuth::authenticate($request->token)->id;
	
		if($user){
			$amount = 0;
			/****Android Receipt data *****/
			
			//$receipt =  stripslashes($request->receipt);
			$clean_receipt = str_replace('“', '"', $request->receipt);
			$clean_receipt2 = str_replace('”', '"', $clean_receipt);
			$receipt_data = json_decode($clean_receipt2,true);
			if ($receipt_data !== null) {
					$purchaseToken = $receipt_data['purchaseToken'];
					$productId = $receipt_data['productId'];
					$packageName = $receipt_data['packageName'];
					$order_id = $receipt_data['orderId'];
					$purchaseState = $receipt_data['purchaseState'];
					//$Amount = $receipt_data['Amount'];
					
				} else {
					return response()->json([
					'success' => false,
					'message' => 'Receipt format is not valid.',
					]);
			}
			/****Android Receipt data End *****/	

				$client = new Google_Client();
				$client->setApplicationName('seejobrun');
				$client->setAuthConfig(storage_path('app/google_play_credentials.json'));
				$client->setScopes([Google_Service_AndroidPublisher::ANDROIDPUBLISHER]);
				
				$androidPublisher = new Google_Service_AndroidPublisher($client);
				 try {
					$subscription = $androidPublisher->purchases_subscriptions->get($packageName, $productId, $purchaseToken);
					
					$expiryTimeMillis = $subscription->expiryTimeMillis / 1000; // Convert 
					$startTimeMillis = $subscription->startTimeMillis / 1000; // Convert from 
					$expiryDateTime = Carbon::createFromTimestamp($expiryTimeMillis);
					$startTimeDateTime = Carbon::createFromTimestamp($startTimeMillis);
					$subs_Enddate = $expiryDateTime->format('Y-m-d H:i:s');
					$subs_startDate = $startTimeDateTime->format('Y-m-d H:i:s');
					$amount_bal = $subscription->priceAmountMicros;
					$amount = $amount_bal/1000000;
					
					if($subscription){
						
						
					/* record save in payment table */
							$customerpay = Payment::make();
							$customerpay->user_id = $user_id;
							$customerpay->amount = $amount;
							$customerpay->status = $purchaseState;
							$customerpay->transaction_id = $order_id;
							$customerpay->subscription_status = $purchaseState;
							$customerpay->subscription_id = $order_id;
							$customerpay->payment_date = $subs_startDate;
							
							$customerpay->save();
							
							/* record save in payment table */
							/* for selected plan */
							
							$plan_id = $request->plan_id;
							$selectedplans = $user->selectedPlan()->orderBy('id', 'desc')->first();
							if($selectedplans){
								 $selectedpln = SelectedPlan::where('user_id','=',$user_id)->orderBy('id', 'desc')->first();
								 
								/*  if($order_id==$selectedpln->subscription_id){ */
								
									 $selectedpln->start_date = $subs_startDate;
									 $selectedpln->end_date = $subs_Enddate;
									 $selectedpln->subscription_status = $purchaseState; 
									 $selectedpln->subscription_id = $order_id;
									 $selectedpln->plan_id = $request->plan_id;
									 $selectedpln->status = $purchaseState;		
									 $selectedpln->receipt	='';								 
									 $selectedpln->purchase_token = $purchaseToken;
									 $selectedpln->save();
								/*  }else{
									$selectedPlan = SelectedPlan::make();
									$selectedPlan->user_id = $user_id;
									$selectedPlan->plan_id = $plan_id;
									$selectedPlan->start_date = $subs_startDate;
									$selectedPlan->end_date = $subs_Enddate; 
									$selectedPlan->status = $purchaseState;
									$selectedPlan->purchase_token = $purchaseToken;
									$selectedPlan->subscription_id = $order_id;
									$selectedPlan->subscription_status = $purchaseState;
									$selectedPlan->save();
									
								 } */
									
								
							}else{
								
									$selectedPlan = SelectedPlan::make();
									$selectedPlan->user_id = $user_id;
									$selectedPlan->plan_id = $plan_id;
									$selectedPlan->start_date = $subs_startDate;
									$selectedPlan->end_date = $subs_Enddate;
									$selectedPlan->status = $purchaseState;
									$selectedPlan->purchase_token = $purchaseToken;
									$selectedPlan->subscription_id = $order_id;
									$selectedPlan->receipt='';
									$selectedPlan->subscription_status = $purchaseState;
									$selectedPlan->save();	
								
								
							}

						return response()->json([
							'success' => true,
							'message' => 'Payment has been completed.',
						]);
						
					}else{
						return response()->json([
						'success' => false,
						'message' => 'Payment is not valid',]);
					}
					
				} catch (\Google_Service_Exception $e) {
					return response()->json([
							'success' => false,
							'message' =>  $e->getMessage(),
						]);
				}
			
		}else{
			return response()->json([
					'success' => false,
					'message' => 'Token is not valid. please contact to the admin.',
				]);
		}
	
}


public function IosSubscriptionRestore(Request $request){
	
		$validator = Validator::make($request->all(), [
				'token' => 'required',
			]);
		
		if ($validator->fails()) {
			return response()->json(['error' => $validator->messages()], 200);
		}
			
		$user = JWTAuth::authenticate($request->token);
		
		$user_id = JWTAuth::authenticate($request->token)->id;
		
		if($user){
				
					$slectedPlans = SelectedPlan::where('user_id','=',$user->id)->orderBy('id', 'desc')->first();
						$purchaseToken = $slectedPlans->purchase_token;
						$subscriptionId= $slectedPlans->subscription_id;
						$productId='';
						if($slectedPlans->plan_id==1){
							$productId = 'plan_monthly';
						}elseif($slectedPlans->plan_id==2){
							$productId = 'plan_yearly';
						}
						
						//$slectedPlans->subscription_id;
						
						/* $client = new Google_Client();
						$client->setApplicationName('seejobrun');
						$client->setAuthConfig(storage_path('app/google_play_credentials.json'));
						$client->setScopes([Google_Service_AndroidPublisher::ANDROIDPUBLISHER]);
						
						$androidPublisher = new Google_Service_AndroidPublisher($client);
						$packageName = 'com.clockk';
						
						try {
							$androidPublisher->purchases_subscriptions->revoke($packageName, $subscriptionId, $purchaseToken);
							$slPlans = SelectedPlan::where('user_id','=',$user->id)->orderBy('id', 'desc')->first();
							$slPlans->status =0;
							$slPlans->subscription_status =0;
							$slPlans->save();
							return response()->json([
									'success' => true,
									'message' => 'You have successfully Restored Your subscription.',
								]);
							
						}catch (Google_Service_Exception $e) {
							$errorData = json_decode($e->getMessage(), true);
							if (isset($errorData['error']['errors'][0]['reason']) && $errorData['error']['errors'][0]['reason'] === 'subscriptionExpired') {
									
								return response()->json([
									'success' => false,
									'message' => 'Cannot restore the subscription because it has already expired.',
									]);
						    } else {
								echo "Error restoring subscription: " . $e->getMessage();
							}
							
									
						} */
						
					$receipt = $request->receipt;
					$slectPlans = SelectedPlan::where('user_id','=',$user->id)->orderBy('id', 'desc')->first();
					$curl = curl_init();
							curl_setopt_array($curl, array(
							/* CURLOPT_URL => 'https://sandbox.itunes.apple.com/verifyReceipt',*/
							CURLOPT_URL => 'https://buy.itunes.apple.com/verifyReceipt',
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_ENCODING => '',
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 0,
							CURLOPT_FOLLOWLOCATION => true,
							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							CURLOPT_CUSTOMREQUEST => 'POST',
							CURLOPT_POSTFIELDS =>'{
							"receipt-data":"'.$receipt.'",
							"password":"d664e45baee94e9c8fbbbae38205129e",
							"exclude-old-transactions":true
							}',
							CURLOPT_HTTPHEADER => array(
							'Content-Type: application/json'
							),
							));
							$response = curl_exec($curl);
							curl_close($curl);						
							$responseData = json_decode($response);	
							
							if ($responseData->status === 0) {
								$latestReceiptInfo = $responseData->latest_receipt_info;
								
								$numericTimestamp = strtotime($latestReceiptInfo[0]->expires_date);
								$currentTimestamp = time();
								if($numericTimestamp > $currentTimestamp){
									
									$slectPlans->subscription_status = 0;
									$slectPlans->start_date = date('Y-m-d H:i:s', strtotime($result->latest_receipt_info[0]->purchase_date));
									 $slectPlans->end_date = date('Y-m-d H:i:s', strtotime($result->latest_receipt_info[0]->expires_date));
									 $slectPlans->subscription_status = 0;
									$slectPlans->save();
									return response()->json(
									[
										'success' => true,
										'message' => 'You have successfully Restored Your subscription.',
									]);
									
								}else{
									
									$slectedPlans = SelectedPlan::where('user_id','=',$user->id)->orderBy('id', 'desc')->first();
									 $slectedPlans->status=1;
									 $slectedPlans->subscription_status=1;
									 $slectedPlans->save();
									return response()->json(
									[
										'success' => false,
										'message' => 'Cannot restore the subscription because it has already expired.',
									]);
								}
								
						
			}else{
				
			return response()->json([
					'success' => false,
					'message' => 'Token is not valid. please contact to the admin.',
				]);
		}

}
}

public function AddContactShared(Request $request){

	$validator = Validator::make($request->all(), [
				'token' => 'required',
				'job_id' => 'required',
				'contact_id'=>'required'
				
			]);
		
		if ($validator->fails()) {
			return response()->json(['error' => $validator->messages()], 200);
		}
	
		$user = JWTAuth::authenticate($request->token);
	
		if($user){
			
			 $contactshared = Contactshared::make();
			
		
                    $contactshared->user_id = $user->id;
					$contactshared->contact_id = $request->contact_id;
                    $contactshared->job_id = $request->job_id;
					$contactshared->jobnotepad = 0;
                    $contactshared->punchlist = 0;
                    $contactshared->stage = 0;
                    $contactshared->contact = 0;
                    $contactshared->document = 0;
					$contactshared->calendar= 0;
                    $contactshared->pictures = 0;
                    $contactshared->save();
				
					 return response()->json([
                        'success' => true,
                        'message' => 'Your Contact has been Saved Successfully.'
                   ]);
		}else{
			return response()->json([
					'success' => false,
					'message' => 'Token is not valid. please contact to the admin.',
				]);
		}
}

public function archive_Or_unarcive_contacts(Request $request){
	$validator = Validator::make($request->all(), [
				'token' => 'required',
				'contact_id'=>'required',
				'status'=>'required'
				
			]);
			
			if ($validator->fails()) {
				return response()->json(['error' => $validator->messages()], 200);
			}
			$user = JWTAuth::authenticate($request->token);		
		
		if($user){
			$archivecontact = Contact::where('id','=',$request->contact_id)->first();
			$old_suscription_id = $archivecontact->subscription_id;
			if($request->status==2){
				if($archivecontact->subscription_id){
					Stripe\Stripe::setApiKey(config('app.stripe_secret'));
					$current_subscription = \Stripe\Subscription::retrieve($old_suscription_id);
					if($current_subscription->status=='active'){
						$current_subscription->cancel();
							$endDate = $current_subscription->current_period_end;
							$subscriptionEndDate = date('Y-m-d H:i:s', $endDate);
							$archivecontact->status=2;
							$archivecontact->subscription_status=2;
							$archivecontact->subscription_end_reason='Contact Archived';
							$archivecontact->subscription_end=$subscriptionEndDate;
							$archivecontact->save();
							
							$subsstatus = Addcontactsubscription::where('subscription_id','=',$old_suscription_id)->first();
							$subsstatus->subscription_end_date = $subscriptionEndDate;
							$subsstatus->subscription_status =2; 
							$subsstatus->save();
						
					}
				}else{
					
					$archivecontact->status=2;
					$archivecontact->save();
					
				}
				return response()->json([
					'success' => true,
					'message' => 'You have successfully archived the contact.'
			   ]);
				
			}
			if($request->status==1){
				
				if(@$old_suscription_id){
					Stripe\Stripe::setApiKey(config('app.stripe_secret'));
					$current_subscription = \Stripe\Subscription::retrieve($old_suscription_id);
					
					if($current_subscription->status=='canceled'){
						$exist_plan = Addcontactsubscription::where('subscription_id','=',$old_suscription_id)->first();
					
						try {
							$subscription = \Stripe\Subscription::create(array(
									"customer" => $current_subscription->customer,
									"items" => array(
										array(
											"plan" =>$exist_plan->stripe_plan_id,
										),
									),
								));
							} catch (Exception $e) {
								$api_error = $e->getMessage();
								return response()->json([
									'success' => false,
									'message' => $api_error 
							   ]);
							}
						
						if(empty($api_error)&& $subscription){
							//echo 'new_subscription_id->'.$subscription->id;
							$subsstatus = Addcontactsubscription::where('subscription_id','=',$old_suscription_id)->first();
							
							$startDate = $subscription->current_period_start;
							$endDate = $subscription->current_period_end;
							$subscriptionstartDate = date('Y-m-d H:i:s', $startDate);
							$subscriptionendDate = date('Y-m-d H:i:s', $endDate);
							$subsstatus->subscription_start_date = $subscriptionstartDate;
							$subsstatus->subscription_end_date = $subscriptionendDate;
							$subsstatus->subscription_id = $subscription->id; 
							$subsstatus->subscription_status =1; 
							$subsstatus->save();
							
							$contact_update = Contact::where('subscription_id','=',$old_suscription_id)->first();
							$contact_update->subscription_id= $subscription->id;
							$contact_update->subscription_status = 1;
							$contact_update->status = 1;
							$contact_update->subscription_start= $subscriptionstartDate;
							$contact_update->subscription_end= $subscriptionstartDate;
							$contact_update->subscription_end_reason= 'unarchived contact';
							$contact_update->save();
							return response()->json([
								'success' => true,
								'message' => 'You have successfully unarchived the contact.'
						   ]);
							
						}
					} 
				}else{
					$archivecontact->status=1;
					$archivecontact->save();
				}	
				return response()->json([
					'success' => true,
					'message' => 'You have successfully unarchived the contact.'
			   ]);
			}
			
			//$archivecontact = Contact::findorfail($request->contact_id);
			//$archivecontact->status=$request->status;
			//$archivecontact->save();
			
			
		}else{
			return response()->json([
					'success' => false,
					'message' => 'Token is not valid. please contact to the admin.',
				]);
		}	
	
}

public function all_my_daily_task(Request $request){
	
	$validator = Validator::make($request->all(), [
		'token' => 'required'	
	]);

	if ($validator->fails()) {
		return response()->json(['error' => $validator->messages()], 200);
	}
	
	$user = JWTAuth::authenticate($request->token);
	
		if($user){
		/* $user_id = $user->id;
		 $contact_id = $user->contactuserid()->where('contact_user_id','=',$user_id)->get();
	
			$contactss = Contact::where('contact_user_id', '=', $user->id)->first(); */
				$user_id = $user->id;
				$contact = contact::where('contact_user_id','=',$user_id)->first();		
				$contact_id= $contact->id;

		/*  if(count($contact_id) > 0)
					{ 
				
						$cont_id = $contact_id[0]->id;
						
						$invol_user = job::with('taskassignment','taskassignment.taskassignmentimages')->whereHas('jobcontact', function($q) use($cont_id) {$q->where('contact_id', '=', $cont_id); })->where('status','!=',2)->orderBy('contract_status', 'desc')->orderBy('id', 'desc')->get();
						$login_user = job::with('taskassignment','taskassignment.taskassignmentimages')->where('user_id','=',$user_id)->orderBy('contract_status', 'desc')->orderBy('id', 'desc')->get();
						
						$jobdata['data'] = $login_user->merge($invol_user);
						
					}
					 else
					{
						
						$jobdata = job::with('taskassignment','taskassignment.taskassignmentimages')->where('user_id','=',$user_id)->orderBy('contract_status', 'desc')->orderBy('id', 'desc')->paginate(10);
						
					} */   
					
					
				$tasks1 = Job::with([
									'taskassignment' => function ($query) use ($user_id,$contact_id) {
										$query->where('user_id', '=', $user_id)->orwhere('contact_id', '=', $contact_id);
									},
									'taskassignment.taskassignmentimages'
								])->get();
				
				

		  return response()->json([
                        'success' => true,
                        'MyTasks' => array('data'=>@$tasks1)
                   ]);  
				   
		}else{
			return response()->json([
					'success' => false,
					'message' => 'Token is not valid. please contact to the admin.',
				]);
		}
	
}
public function all_my_daily_task_new(Request $request){
	
	$validator = Validator::make($request->all(), [
		'token' => 'required'	
	]);

	if ($validator->fails()) {
		return response()->json(['error' => $validator->messages()], 200);
	}
	
	$user = JWTAuth::authenticate($request->token);
	
		if($user){
			$jobdata='';
				$user_id = $user->id;
				$contact = contact::where('contact_user_id','=',$user_id)->first();		
				$contact_id= $contact->id;
				
					//$tasks1 = Taskassignment::where('user_id', '=', $user_id)->get();
					$tasks2 = Taskassignment::where('contact_id', '=', $contact_id)->get();

					// Merge both collections
					//$mergedTasks = $tasks1->merge($tasks2);
					//$uniqueTasks = $mergedTasks->unique('id');
					$tasks1 = Job::with([
									'taskassignment' => function ($query) use ($user_id,$contact_id) {
										$query->where('user_id', '=', $user_id)->orwhere('contact_id', '=', $contact_id);
									},
									'taskassignment.taskassignmentimages'
								])->get();
					return response()->json([
                        'success' => true,
                        'MyTasks' => @$tasks1
                   ]);
				   
		}else{
			return response()->json([
					'success' => false,
					'message' => 'Token is not valid. please contact to the admin.',
				]);
		}
	
}

public function Addbuycontactcredits(Request $request){
	
	$validator = Validator::make($request->all(), [
		'token' => 'required',
		'amount' => 'required',
		'stripeToken'=>'required'
	]);

	if ($validator->fails()) {
		return response()->json(['error' => $validator->messages()], 200);
	}
	
	$user = JWTAuth::authenticate($request->token);
	
		if($user){
			$user_id = $user->id;
			
			$stripe_customer_id =  $user->stripe_customer_id;
			$user_credits = $user->credit_contact;
			Stripe\Stripe::setApiKey(config('app.stripe_secret'));
			$payment_amount=$request->amount;
			$total_credits = $payment_amount/2;
			  if(empty($stripe_customer_id)){
				   try {
						$customer = Stripe\Customer::create(array(
								'email' => $user->email,
								'source'  => $request->stripeToken
							));
							$stripe_customer_id = $customer->id;
							$user = User::findorfail($user_id);
							$user->stripe_customer_id=$stripe_customer_id;
							$user->save();
							
						}catch (Exception $e) {
							return response()->json([
										'success' => false,
										'message' => $e->getMessage(),
									]);
						}
			  }	
			 
				try {
					$pay = Stripe\Charge::create([
						"amount" => $payment_amount * 100,
						"currency" => "USD",
						"customer" => $stripe_customer_id,
						"description" => $user->email."create a payment",
					]);
					$AddContactPayment = Addcontactpayment::make();
					$AddContactPayment->user_id = $user_id;
					$AddContactPayment->amount = $payment_amount;
					$AddContactPayment->transaction_id = $pay->balance_transaction;
					$AddContactPayment->credits = $total_credits;
					$AddContactPayment->status = $pay->paid;
					$AddContactPayment->payment_date = date('Y-m-d H:i:s', $pay->created);
					$AddContactPayment->save();
					if($user_credits>0){
						$to_credits = $user_credits+$total_credits;
					}else{
						$to_credits = $total_credits;
					}
					$usr = User::findorfail($user_id);
					$usr->credit_contact=$to_credits;
					$usr->save();
					
					if($pay->balance_transaction){	
						if (empty($api_error) && $stripe_customer_id) {
							try {
								$planName ='BU-'.$user_id;
								$planInterval = 'month';
								$priceCents = 2*100;

								for($i=0;$i<$total_credits; $i++){
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
									$Addcontactsubscription->user_id=$user_id;
									$Addcontactsubscription->credits=1;
									$Addcontactsubscription->stripe_plan_id=$plan->id;
									$Addcontactsubscription->user_plan_id=$planName;
									$Addcontactsubscription->stripe_customer_id=$stripe_customer_id;
									$Addcontactsubscription->amount=2;
									$Addcontactsubscription->transaction_id=$pay->balance_transaction;
									$Addcontactsubscription->save();

								}
							} catch (Exception $e) {
								return response()->json([
									'success' => false,
									'message' => $e->getMessage(),
								]);
							}	

						}
					}
				}catch (Exception $e) {
					return response()->json([
									'success' => false,
									'message' => $e->getMessage(),
								]);
				}
			return response()->json([
				'success' => true,
				'message' => 'You have successfully buy '.$total_credits,
			]);
				   
		}else{
			return response()->json([
					'success' => false,
					'message' => 'Token is not valid. please contact to the admin.',
				]);
		}


	
}

public function AcceptContactInvitations(Request $request){
	
	
    $userId = $request->input('user_id');
	$contactId = $request->input('contact_id');
	$errormsg='';
	$msg='';
	$checkregister_user_or_not = Contact::where('id','=',$contactId)->where('user_id','=',$userId)->first();
	if ($checkregister_user_or_not) {
		if (is_null($checkregister_user_or_not->contact_user_id)) {
				$errormsg=2;
		}else {
			$subs_status=$checkregister_user_or_not->subscription_status;
			$contact_status=$checkregister_user_or_not->status;
			if($subs_status==1 && $contact_status==1){
					$errormsg=4;
					}else{
						$subscriptionplan = Addcontactsubscription::where('user_id', $userId)->where('plan_status', 0)->first();
						
						$stripe_planid = $subscriptionplan->stripe_plan_id;
						$stripe_customerid = $subscriptionplan->stripe_customer_id;
						
						Stripe\Stripe::setApiKey(config('app.stripe_secret'));
						if($stripe_planid && $stripe_customerid){
							try {
								$subscription = \Stripe\Subscription::create(array(
										"customer" => $stripe_customerid ,
										"items" => array(
											array(
												"plan" =>$stripe_planid,
											),
										),
									));
								} catch (Exception $e) {
									$api_error = $e->getMessage();
								}
								if(empty($api_error)){
									
									$contact_detail = Contact::findorfail($contactId);
									$contact_detail->subscription_id= $subscription->id;
									if($subscription->status=='active'){
										$contact_detail->subscription_status = 1;
									}else{
										$contact_detail->subscription_status = 2;
									}
									$startDate = $subscription->current_period_start;
									$endDate = $subscription->current_period_end;
									$contact_detail->status= '1';
									// Convert Unix timestamps to readable dates
									$subscriptionstartDate = date('Y-m-d H:i:s', $startDate);
									$subscriptionendDate = date('Y-m-d H:i:s', $endDate);
									$contact_detail->subscription_start = $subscriptionstartDate;
									$contact_detail->subscription_end = $subscriptionendDate;
									$contact_detail->save();
									
									$final_susbscription = Addcontactsubscription::where('user_id', $userId)->where('stripe_plan_id','=',$stripe_planid)->first();
									$final_susbscription->subscription_start_date= $subscriptionstartDate;
									$final_susbscription->subscription_end_date= $subscriptionendDate;
									$final_susbscription->subscription_id= $subscription->id;
									$final_susbscription->plan_status= 1;
									if($subscription->status=='active'){
										$final_susbscription->subscription_status = 1;
									}else{
										$final_susbscription->subscription_status = 2;
									}
									$final_susbscription->contact_id= $contactId;
									$final_susbscription->save();
								}
								
						}
						$msg=1;
				
				}
			}
		} else {
			// No record found
			$msg = "No contact found for the given contactId and userId.";
		}
			
	

	
	
	return view('acceptcontactinvitations', compact('msg','errormsg'));
	
	
	
}
public function ShowAndHidetask(Request $request)
{
	$validator = Validator::make($request->all(), [
		'token' => 'required',
		'taskassignment_id' => 'required',
		'show_and_hide' => 'required',
	]);

	if ($validator->fails()) 
	{  
		return response()->json(['error'=>$validator->errors()]); 
	}  

	$user = JWTAuth::authenticate($request->token);

	try
	{
		if($user)
		{
			$taskassignment = Taskassignment::findorfail($request->taskassignment_id);
			$taskassignment->show_and_hide = $request->show_and_hide;

			if ($taskassignment->save()) 
			{
				return response()->json([
					'success' => true, 
					'message' => 'Your Task Assignment status has been update successfully.',
				]);
			} 
			else 
			{
				return response()->json([
					'success' => false, 
					'message' => 'Unable to update Task Assignment.Please try again or contact to admin.',
				]);
			} 
		}
		else
		{
			return response()->json([
				'success' => false,
				'message' => 'Token is not valid. please contact to the admin.',
			]);
		}
	}
	catch (\Exception $e) 
	{
		return response()->json(
		[
			'success' => false,
			'message' =>  $e->getMessage(),
		]);
	}
}

public function PendingContact(Request $request)
{
	$validator = Validator::make($request->all(), [
		'token' => 'required',
		
	]);

	if ($validator->fails()) 
	{  
		return response()->json(['error'=>$validator->errors()]); 
	}  

	$user = JWTAuth::authenticate($request->token);
	try
	{
		if($user)
		{
			
			//$pending_clients = $user->contact()->where('status', '=', 0)->get();
			//$pending_clients = Contact::with('user')->where('user_id',$user->id)->where('status', 0)->get();
			
			
			$pending_clients = Contact::with(['addedByUser'])->where('email', $user->email)->where('status', 0)->get();
			
			return response()->json([
					'success' => true, 
					'pending_client' =>$pending_clients,
				]);
		}
		else
		{
			return response()->json([
				'success' => false,
				'message' => 'Token is not valid. please contact to the admin.',
			]);
		}
	}
	catch (\Exception $e) 
	{
		return response()->json(
		[
			'success' => false,
			'message' =>  $e->getMessage(),
		]);
	}
}

public function AcceptContactInvitationsnew(Request $request){
	
	$validator = Validator::make($request->all(), [
		'token' => 'required',
		'user_id'=>'required',
		'contact_id'=>'required',
		
	]);

	if ($validator->fails()) 
	{  
		return response()->json(['error'=>$validator->errors()]); 
	}  

	$user = JWTAuth::authenticate($request->token);
	$userId = $request->user_id;
	$contactId = $request->contact_id;
    //$userId = $request->input('user_id');
	//$contactId = $request->input('contact_id');
	$errormsg='';
	$msg='';
	if($user){
		
			$checkregister_user_or_not = Contact::where('id','=',$contactId)->where('user_id','=',$userId)->first();
			
			$subs_status=$checkregister_user_or_not->subscription_status;
			$contact_status=$checkregister_user_or_not->status;
				if($subs_status==1 && $contact_status==1){
					return response()->json([
										'success' => false,
										'message' => 'Sorry !. You have already accepted the invitation.',
									]);
					}else{
						$totalcontacts = Contact::where('user_id', '=', $userId)->count();
						if($totalcontacts>2){
							$subscriptionplan = Addcontactsubscription::where('user_id', $userId)->where('plan_status', 0)->first();
						
							$stripe_planid = $subscriptionplan->stripe_plan_id;
							$stripe_customerid = $subscriptionplan->stripe_customer_id;
						
							Stripe\Stripe::setApiKey(config('app.stripe_secret'));
							if($stripe_planid && $stripe_customerid){
								try {
									$subscription = \Stripe\Subscription::create(array(
										"customer" => $stripe_customerid ,
										"items" => array(
											array(
												"plan" =>$stripe_planid,
											),
										),
									));
								} catch (Exception $e) {
									
									return response()->json([
										'success' => false,
										'message' => $e->getMessage(),
									]);
								}
								if(empty($api_error)){
									
									$contact_detail = Contact::findorfail($contactId);
									$contact_detail->subscription_id= $subscription->id;
									if($subscription->status=='active'){
										$contact_detail->subscription_status = 1;
									}else{
										$contact_detail->subscription_status = 2;
									}
									$startDate = $subscription->current_period_start;
									$endDate = $subscription->current_period_end;
									$contact_detail->status= '1';
									// Convert Unix timestamps to readable dates
									$subscriptionstartDate = date('Y-m-d H:i:s', $startDate);
									$subscriptionendDate = date('Y-m-d H:i:s', $endDate);
									$contact_detail->subscription_start = $subscriptionstartDate;
									$contact_detail->subscription_end = $subscriptionendDate;
									$contact_detail->save();
									
									$final_susbscription = Addcontactsubscription::where('user_id', $userId)->where('stripe_plan_id','=',$stripe_planid)->first();
									$final_susbscription->subscription_start_date= $subscriptionstartDate;
									$final_susbscription->subscription_end_date= $subscriptionendDate;
									$final_susbscription->subscription_id= $subscription->id;
									$final_susbscription->plan_status= 1;
									if($subscription->status=='active'){
										$final_susbscription->subscription_status = 1;
									}else{
										$final_susbscription->subscription_status = 2;
									}
									$final_susbscription->contact_id= $contactId;
									$final_susbscription->save();
								}
								
							}
						//$msg=1;
							return response()->json([
										'success' => true,
										'message' => 'Thank you for accepting the invitation to connect with See Job Run. We’re excited to have you as part of our network!',
									]);
				
						}else{
							$contact_detail = Contact::findorfail($contactId);
							$contact_detail->status= '1';
							$contact_detail->subscription_end_reason= 'free';
							$contact_detail->save();
							return response()->json([
											'success' => true,
											'message' => 'Thank you for accepting the invitation to connect with See Job Run. We’re excited to have you as part of our network!',
										]);
						}
				}
			
		
		//return view('acceptcontactinvitations', compact('msg','errormsg'));
		
	}else
	{
		return response()->json([
			'success' => false,
			'message' => 'Token is not valid. please contact to the admin.',
		]);
	}	
}

//Lead Module Start

Public function AddLead(Request $request)
{
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required',
       'name' => 'required',
       
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    }  
  
    $user = JWTAuth::authenticate($request->token);
    try
    {
        if($user)
        {
            $leadname = $request->name;
            $lead = $user->lead()->make();

            $lead->name = $request->name;
            if($request->mobile){
                $lead->mobile = $request->mobile;
            }
            $lead->lead_type = 'Lead';
            if($request->address){
                $lead->address = $request->address;
            }
            if($request->city){
                $lead->city = $request->city;
            }
            if($request->state){
                $lead->state = $request->state;
            }
            if($request->pincode){
                $lead->pincode = $request->pincode;
            }
            if($request->deal_name){
                $lead->deal_name = $request->deal_name;
            }
            if($request->lead_email){
                $lead->lead_email = $request->lead_email;
            }
            if($request->description){
             $lead->description = $request->description;
            }
            $lead->contract_status = 0;
            $lead->status = '1';
            $lead->save();
            
            $lead_id = $lead->id;
            
         // Send notification

            // $from_email = Config::get('mail.from.address');
            // $email = $request->lead_email;
            // $name =  $leadname;
            // $subject = "New lead added";

            // $body = @Template::where('type', 5)->orderBy('id', 'DESC')->first()->content;
            
            // $content = array('name' => $name);
            // foreach ($content as $key => $parameter) 
            // {
            //     $body = str_replace('{{' . $key . '}}', $parameter, $body);
            // }

            // if($from_email)
            // {
            //     Mail::send('emails.name', ['template' => $body, 'name' => $name], function ($m) use ($from_email, $email, $name, $subject) 
            //     {
            //         $m->from($from_email, 'See Job Run');

            //         $m->to($email, $name)->subject($subject);
            //     });
            // }

         return response()->json([
            'success' => true,
            'lead_id' =>$lead_id,
            'message' => 'Your Lead has been Saved Successfully.'
        ]);
        
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Token is not valid. please contact to the admin.',
            ]);
        }
    }catch (\Exception $e) 
    {
        return response()->json(
        [
            'success' => false,
            'message' =>  $e->getMessage(),
        ]);
    }
}

public function getLeads(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required',
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    }  
  
    $user = JWTAuth::authenticate($request->token);
    if($user){

        $leads = Lead::where('user_id','=',$user->id)->where('status','=',1)->get();
        return response()->json([
            'success' => true,
            'Leads' => @$leads,
            
        ]);
    }else{
        return response()->json([
                'success' => false,
                'message' => 'Token is not valid. please contact to the admin.',
            ]);
    }
}
public function getleadbyId(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required',
       'id' => 'required'
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    }  
  
    $user = JWTAuth::authenticate($request->token);
    if($user){

        $leads = Lead::where('id','=',$request->id)->where('user_id','=',$user->id)->get();
        return response()->json([
            'success' => true,
            'Leads' => @$leads,
            
        ]);
    }else{
        return response()->json([
                'success' => false,
                'message' => 'Token is not valid. please contact to the admin.',
            ]);
    }
}
public function updateLead(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required',
       'lead_id' => 'required',
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    }  
  
    $user = JWTAuth::authenticate($request->token);
    if($user){
        $lead = Lead::findorfail($request->lead_id);
        if($request->name){
            $lead->name = $request->name;
        }
        
        if($request->mobile){
            $lead->mobile = $request->mobile;
        }
        if($request->lead_type){
            $lead->lead_type = $request->lead_type;
        }
        if($request->address){
            $lead->address = $request->address;
        }
        if($request->city){
            $lead->city = $request->city;
        }
        if($request->state){
            $lead->state = $request->state;
        }
        if($request->pincode){
            $lead->pincode = $request->pincode;
        }
        if($request->deal_name){
            $lead->deal_name = $request->deal_name;
        }
        if($request->lead_email){
            $lead->lead_email = $request->lead_email;
        }
        if($request->description){
            $lead->description = $request->description;
        }
        if($request->contract_status){
            $lead->contract_status = $request->contract_status;
        }
        $lead->save();
        return response()->json([
            'success' => true,
            'message' => 'You have successfully Updated the lead.'
        ]);

    }else{
        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);

    }
    
   
}
public function deleteLead(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required',
       'lead_id' => 'required',
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token);
    if($user){
        $lead = Lead::where('user_id','=',$user->id)->where('id','=',$request->lead_id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'You have successfully deleted the lead.'
        ]);

    }else{
        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);

    }
}

// TODO Section Module start

public function Addtodosection(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required',
       'sec_name' => 'required',
       'lead_id' => 'required',
       
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token);
    if($user){
        
            $todosection = $user->todosection()->make();
            $todosection->sec_name = $request->sec_name;
            $todosection->lead_id = $request->lead_id;
            $todosection->status = '1';
            $todosection->save();
            return response()->json([
                'success' => true,
                'message' => 'You have successfully added todo section.'
            ]);
    }else{

        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);

    }
    
}
public function Gettodosection(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required', 
       'lead_id' =>'required'
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token);
    if($user){

        $todosection = $user->todosection()->where('lead_id', '=', $request->lead_id)->get();
        return response()->json([
            'success' => true,
            'Todosections' => @$todosection,
            
        ]);
    }else{
        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);
    }

}
public function updatetodosection(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required', 
       'id' =>'required'
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token);
        if($user){
            $todosection = Todosection::findorfail($request->id);
            if($todosection){
                $todosection->sec_name = $request->sec_name;
                if($request->status){
                     $todosection->status = $request->status;
                }
                $todosection->save();
                return response()->json([
                    'success' => true,
                    'Todosections' =>'You have successfully updated todosection.',
                    
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);

             }
    }else{
        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);
    } 

}

public function deletetodosection(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required', 
       'id' =>'required'
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token);
    if($user){
        $Todosection = Todosection::where('id','=',$request->id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'You have successfully deleted the to do section task.'
        ]);

    }else{

        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);

    }
}

public function AddtodoSectionTask(Request $request){

    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required', 
       'task_name' =>'required',
       'todosec_id' =>'required',
       'enddate' =>'required',
    ]);
    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token);

    if($user){
       $todosect =  Todosectiontask::make();
       $todosect->task_name = $request->task_name;
       $todosect->todosec_id = $request->todosec_id;
       $todosect->description = $request->description;
       $todosect->enddate = $request->enddate;
       $todosect->status = 0;
       $todosect->save();
       return response()->json([
        'success' => true,
        'message' => 'You have successfully added todosection task.'
    ]);

    }else{

        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);
    }

}
public function deletetodoSectionTask(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required', 
       'todosectask_id' =>'required',
       
    ]);
    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token); 
    if($user){
        $Todosectiontask = Todosectiontask::where('id','=',$request->todosectask_id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'You have successfully deleted the to do section task.'
        ]);

    }else{

        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);

    }

}
public function updatetodoSectionTask(Request $request){
  
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required', 
       'todosectask_id' =>'required',
    ]);
    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token);

    if($user){
       $todosect =  Todosectiontask::findorfail($request->todosectask_id);
       if($todosect){
            if($request->task_name){
                 $todosect->task_name = $request->task_name;
            }
            $todosect->todosec_id = $request->todosec_id;
            if($request->taskorder){
                 $todosect->taskorder = $request->taskorder;
            }
            if($request->description){
                $todosect->description = $request->description;
            }
            if($request->enddate){
             $todosect->enddate = $request->enddate;
            }
            if($request->status){
                $todosect->status = $request->status;
            }
            $todosect->save();
            return response()->json([
                'success' => true,
                'message' => 'You have successfully updated todosection task.'
            ]);
       }else{
        return response()->json([
            'success' => false,
            'message' => 'Todo section task not found',
        ]);
     }
    }else{

        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);
    }


}


public function update_todosection_task_status(Request $request){
  
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required', 
       'todosectask_id' =>'required',
       'status' =>'required'
    ]);
    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token);

    if($user){
       $todosect =  Todosectiontask::findorfail($request->todosectask_id);
       if($todosect){
            $todosect->status = $request->status;
            $todosect->save();
            return response()->json([
                'success' => true,
                'message' => 'You have successfully updated task Status.'
            ]);
       }else{
        return response()->json([
            'success' => false,
            'message' => 'Todo section task not found',
        ]);
     }
    }else{

        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);
    }


}


public function GettodoSectionWithTask(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required', 
      
    ]);
    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token);

    if($user){
        if($request->lead_id){
            $data = Lead::with(['todosection','todosection.todosectiontask'])->where('user_id','=',$user->id)->where('id','=',$request->lead_id)->get();
        }else{
            $data = Todosection::with('todosectiontask')->where('user_id','=',$user->id)->where('job_id','=',$request->job_id)->get(); 
        }
        return response()->json([
            'success' => true,
            'Lead' => @$data,
            
        ]);

    }else{
        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);
    }


}
// End TO DO SECTION

// Job TODO SECTIONS
public function AddtodosectionJob(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required',
       'sec_name' => 'required',
       'job_id' => 'required',
       
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token);
    if($user){
        
            $todosection = $user->todosection()->make();
            $todosection->sec_name = $request->sec_name;
            $todosection->job_id = $request->job_id;
            $todosection->status = '1';
            $todosection->save();
            return response()->json([
                'success' => true,
                'message' => 'You have successfully added todo section.'
            ]);
    }else{

        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);

    }
    
}

public function GettodosectionJob(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required', 
       'job_id' =>'required'
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token);
    if($user){

        $todosection = $user->todosection()->where('job_id', '=', $request->job_id)->get();
        return response()->json([
            'success' => true,
            'Todosections' => @$todosection,
            
        ]);
    }else{
        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);
    }

}


// END TODO sections

// Convert lead in to job
public function convertLeadtoJob(Request $request){
    $validator = Validator::make($request->all(), 
       [ 
          'token' => 'required',
          'name' => 'required',
          'client_id' => 'required',
          'job_type' => 'required',
          'address' => 'required',
          'city' => 'required',
          'state' => 'required',
          'pincode' => 'required',
          'contract_status' => 'required',
          'lead_id' =>'required',
         
       ]);

       if ($validator->fails()) 
       {  
           return response()->json(['error'=>$validator->errors()]); 
       }   
       $user = JWTAuth::authenticate($request->token);
       try
       {
           if($user)
           {
               $jobname = $request->name;
               $job = $user->job()->make();
               $job->name = $request->name;
               $job->client_id = $request->client_id;
               $job->job_type = $request->job_type;
               $job->address = $request->address;
               $job->city = $request->city;
               $job->state = $request->state;
               $job->pincode = $request->pincode;
               $job->contract_status = $request->contract_status;
               $job->status = '1';
                $job->save();
               
               $job_id = $job->id;
               
               $jobcontact = Jobcontacts::make();
               $jobcontact->user_id = $user->id;
               $jobcontact->job_id = $job_id;
               $jobcontact->contact_id = $request->client_id;
               $jobcontact->save();
               
               // add default value in shared contact table
               $contactshared = Contactshared::make();
               $contactshared->user_id = $user->id;
               $contactshared->contact_id = $request->client_id;
               $contactshared->job_id = $job_id;
               $contactshared->jobnotepad = 0;
               $contactshared->punchlist = 0;
               $contactshared->stage = 0;
               $contactshared->contact = 0;
               $contactshared->document = 0;
               $contactshared->calendar= 0;
               $contactshared->pictures = 0; 
               $contactshared->general = 0;
               $contactshared->todo = 0;
               $contactshared->save();

            // Convert lead in to job

            $lead = Lead::findorfail($request->lead_id);
            if($lead){
                $lead->status = 0;
                $lead->save();
            }

            // End of convert lead in to job

            //Update job id in todo section table

            //$todosections = Todosection::where('lead_id', '=', $request->lead_id)->get();
           // Retrieve all the todo section rows where lead_id matches
                $todosection = Todosection::where('lead_id', '=', $request->lead_id)->get();

                if ($todosection->isNotEmpty()) {
                    // Loop through each row and update the job_id individually
                    foreach ($todosection as $todo) {
                        $todo->job_id = $job_id; // Set the new job_id
                        $todo->save(); // Save the update
                    }
                }
              


            // Send notification
            
            //    $contacts = Contact::find($request->client_id);
            //    if($contacts){
            //        $contact_user_id = $contacts->contact_user_id;
            //        if($contact_user_id){
            //            //notification send	
            //            $msg["title"] = "New Job";
            //            $msg["body"] = "You are invited to join this job ".$jobname;
            //            $msg['type'] = "job";
            //            $msg['client_id'] = $contacts->contact_user_id;
            //            $msg['user_type'] = $this->user_type($contacts->type);
            //            $msg['move'] = 'Home';
            //            $this->sendNotification($user->id, $msg);
                       
            //        }else{
            //                $from_email = Config::get('mail.from.address');
            //                $email = $contacts->email;
            //                $name = $contacts->name;
            //                $subject = "New job added";

            //                $body = @Template::where('type', 5)->orderBy('id', 'DESC')->first()->content;
                           
            //                $content = array('name' => $name);
            //                foreach ($content as $key => $parameter) 
            //                {
            //                    $body = str_replace('{{' . $key . '}}', $parameter, $body);
            //                }

            //                if($from_email)
            //                {
            //                    Mail::send('emails.name', ['template' => $body, 'name' => $name], function ($m) use ($from_email, $email, $name, $subject) 
            //                    {
            //                        $m->from($from_email, 'See Job Run');

            //                        $m->to($email, $name)->subject($subject);
            //                    });
            //                }

                       
            //        }
            //    }
            return response()->json([
               'success' => true,
               'job_id' =>$job_id,
               'message' => 'Your  Job has been Saved Successfully.'
           ]);
           
           }else{
               return response()->json([
                   'success' => false,
                   'message' => 'Token is not valid. please contact to the admin.',
               ]);
           }
       }catch (\Exception $e) 
       {
           return response()->json(
           [
               'success' => false,
               'message' =>  $e->getMessage(),
           ]);
       }
}


// End of lead in to job

// public function Notificationtest(Request $request){

//     $job_id = 205;
//     $addtaskassignmentcontact = Contact::where('id','=',231)->first();
//     if(@$addtaskassignmentcontact->contact_user_id){
//         $startd = Carbon::parse('2024-10-02 14:41:01');
//         $formattedDate = $startd->format('l M j, Y');
//         $job_name = Job::where('id','=', $job_id)->first()->name;
//         $msg["title"] = $request->title .' , '.$job_name .' starts '.$formattedDate;
//         $msg["body"] = 'You have a new task assigned to you.';
//         $msg['type'] = "Add TaskAssignment";
//         $msg['client_id'] = $addtaskassignmentcontact->contact_user_id;
//         $msg['user_type'] = '';
//         $msg['move'] = 'Home';
//         $description ='just testing notification';
//         try {
//             $this->sendNotificationTest(160, $msg);
//             return response()->json([
//                 'success' => true,
//                 'message' => 'Notification sent successfully to user ID ' . $addtaskassignmentcontact->contact_user_id
//             ]);
           
//         } catch (Exception $e) {
//             return response()->json(
//                 [
//                     'success' => false,
//                     'message' =>  $e->getMessage(),
//                 ]);
//         }
        
//     }

// }


public function sendNotification($user_id, $msgdata = array())
{
    $client_id = $msgdata['client_id'];
    $client = new Google_Client();
    $client->setAuthConfig(public_path('firebase/seejobrun-firebase.json'));
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    
    // Fetch the access token
    $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];
    
    // Fetch the FCM token of the user
    $regfcm = FcmToken::whereUserId($client_id)->first();
   
    if ($regfcm) {
        // Convert any non-string values in $msgdata to strings
        foreach ($msgdata as $key => $value) {
            $msgdata[$key] = (string) $value;
        }
        // Save value in database

            $saveNotification = Notification::make();
			$saveNotification->user_id = $user_id;
			$saveNotification->title = $msgdata['title'];
			$saveNotification->body = $msgdata['body'];
			$saveNotification->type = $msgdata['type'];
			$saveNotification->client_id = $client_id;
			$saveNotification->save();
			
        // FCM v1 API message structure
        $data = [
            "message" => [
                "token" => $regfcm->fcmtoken,  // Send to user's FCM token
                "data" => $msgdata,  // Add any custom data payload
                "notification" => [
                    'title' => $msgdata['title'],
                    'body' => $msgdata['body'],
                ],
                "android" => [
                    "notification" => [
                        "sound" => "default"  // Add sound setting for Android specifically
                    ]
                ],
                "apns" => [
                    "payload" => [
                        "aps" => [
                            "sound" => "default"  // Add sound setting for iOS specifically
                        ]
                    ]
                ]
            ]
        ];

        $dataString = json_encode($data);  // Convert data to JSON string

        // Set headers with the OAuth access token
        $headers = [
            'Authorization: Bearer ' . $accessToken,  // Use the access token
            'Content-Type: application/json',
        ];

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/seejobrun/messages:send');  // Use FCM v1 endpoint
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);  // Pass the JSON payload

        // Execute the request and capture the response
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            // Handle cURL errors
            $error_msg = curl_error($ch);
            return response()->json(
                [
                    'success' => false,
                    'message' =>  $error_msg,
                ]);
        } else {
            
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            // Handle UNREGISTERED FCM tokens
            if ($http_code == 404 && strpos($response, '"errorCode": "UNREGISTERED"') !== false) {
                $regfcm->delete();
               
            }
        }

        // Close cURL connection
        curl_close($ch);
    } 
}

// General to do section functions

public function Addgeneraltodosection(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required',
       'sec_name' => 'required', 
       
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token);
    if($user){
        
            $todosection = $user->general_todo_section()->make();
            $todosection->sec_name = $request->sec_name;
            $todosection->status = '1';
            $todosection->save();
            return response()->json([
                'success' => true,
                'message' => 'You have successfully added general todo section.'
            ]);
    }else{

        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);

    }
    
}
public function Getgeneraltodosection(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required', 
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token);
    if($user){

        $todosection = $user->general_todo_section()->get();
        return response()->json([
            'success' => true,
            'Todosections' => @$todosection,
            
        ]);
    }else{
        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);
    }

}

public function updategeneraltodosection(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required', 
       'id' =>'required'
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token);
        if($user){
            $todosection = General_todo_section::findorfail($request->id);
            if($todosection){
                $todosection->sec_name = $request->sec_name;
                if($request->status){
                     $todosection->status = $request->status;
                }
                $todosection->save();
                return response()->json([
                    'success' => true,
                    'Todosections' =>'You have successfully updated todosection.',
                    
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not valid. please contact to the admin.',
                ]);

             }
    }else{
        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);
    } 

}

public function deletegeneraltodosection(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required', 
       'id' =>'required'
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token);
    if($user){
        $Todosection = General_todo_section::where('id','=',$request->id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'You have successfully deleted the to do section task.'
        ]);

    }else{

        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);

    }
}


// General to do Tasks
public function AddGeneraltodoTask(Request $request){

    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required', 
       'task_name' =>'required',
       'todosec_id' =>'required',
       'enddate' =>'required',
    ]);
    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token);

    if($user){
       $todosect =  General_todo_task::make();
       $todosect->task_name = $request->task_name;
       $todosect->todosec_id = $request->todosec_id;
       $todosect->description = $request->description;
       $todosect->enddate = $request->enddate;
       $todosect->status = 0;
       $todosect->save();
       return response()->json([
        'success' => true,
        'message' => 'You have successfully added todosection task.'
    ]);

    }else{

        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);
    }

}

public function deletegeneraltodoTask(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required', 
       'todotask_id' =>'required',
       
    ]);
    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token); 
    if($user){
        $Todosectiontask = General_todo_task::where('id','=',$request->todotask_id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'You have successfully deleted the to do general task.'
        ]);

    }else{

        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);

    }

}

public function updategeneraltodoTask(Request $request){
  
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required', 
       'todotask_id' =>'required',
    ]);
    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token);

    if($user){
       $todosect =  General_todo_task::findorfail($request->todotask_id);
       if($todosect){
            if($request->task_name){
                 $todosect->task_name = $request->task_name;
            }
            if($request->todosec_id){
                $todosect->todosec_id = $request->todosec_id;
            }
            if($request->taskorder){
                 $todosect->taskorder = $request->taskorder;
            }
            if($request->description){
                $todosect->description = $request->description;
            }
            if($request->enddate){
             $todosect->enddate = $request->enddate;
            }
            if($request->status){
                $todosect->status = $request->status;
            }
            $todosect->save();
            return response()->json([
                'success' => true,
                'message' => 'You have successfully updated todosection task.'
            ]);
       }else{
        return response()->json([
            'success' => false,
            'message' => 'Todo section task not found',
        ]);
     }
    }else{

        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);
    }


}

public function GetgeneraltodoWithTask(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required', 
      
    ]);
    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token);

    if($user){
     
            $data = General_todo_section::with('general_todo_task')->where('user_id','=',$user->id)->get(); 
        
        return response()->json([
            'success' => true,
            'Todosections' => @$data,
            
        ]);

    }else{
        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);
    }


}
public function update_generaltodo_task_status(Request $request){
  
    $validator = Validator::make($request->all(), 
    [ 
       'token' => 'required', 
       'todotask_id' =>'required',
       'status' =>'required'
    ]);
    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = JWTAuth::authenticate($request->token);

    if($user){
       $todosect =  General_todo_task::findorfail($request->todotask_id);
       if($todosect){
            $todosect->status = $request->status;
            $todosect->save();
            return response()->json([
                'success' => true,
                'message' => 'You have successfully updated task Status.'
            ]);
       }else{
        return response()->json([
            'success' => false,
            'message' => 'Todo section task not found',
        ]);
     }
    }else{

        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);
    }


}

}