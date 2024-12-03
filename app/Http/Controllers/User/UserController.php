<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Mail;
use Auth;
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
use Session;
use Intervention\Image\Facades\Image;
class UserController extends Controller
{
    //
	 public function getUserLogin()
    {
        if (Auth::check()) return \redirect()->route('user.dashboard');

        return view('user/login');
    }
	public function postUserLogin(Request $request){
		
			$credentials = $request->only('email', 'password');
			//valid credential
			$validator = Validator::make($credentials, [
				'email' => 'required|email',
				'password' => 'required|string|min:4|max:50'
			]);

			//Send failed response if request is not valid
			if ($validator->fails()) {
				return response()->withErrors($validator);
			}
			$credentials['is_varified'] = 1;
		
			
			if (!Auth::guard()->attempt($credentials)) {
				return redirect()->back()->withErrors(['Error' => 'Your credentials are incorrect, or your email address has not been confirmed.']);
			} else {

				return redirect()->route('user.dashboard')->withSuccess('Logged in  successfully');
			}
	}
	 
	 public function logout(){
		$user = Auth::guard()->user();
		if ($user) {
			Auth::logout();
			Session::flush();
			return redirect('user-login');
		} else {
			return redirect('user-login');
		}
	 }
	
	public function register(){
		return view('user/register');
	}
	public function Postregister(Request $request){
	
		 $validator = Validator::make($request->all(), 
		 [ 
           'name' => 'required',
           'email' => 'required|email|unique:users', 
           'mobile' => 'required',
		   'timezone' => 'required',
		   'password' => 'required|string|min:4',
			
		   
			]);  
	
		
        if ($validator->fails()) 
        {  
            return redirect()->back()->withErrors($validator);
        }   
          try 
        {
			
			
			$now = Carbon::now();
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
           /*  if ($request->profile_pic) 
            {
                $frontimage = str_replace("data:image/jpeg;base64,", '', $request->profile_pic);
                $frontimage1 = str_replace(" ","+",$frontimage);

                $profile_pic = time() . '.jpeg';
                file_put_contents($profile_pic, base64_decode($frontimage1));
                $user->profile_pic = $profile_pic;
            } */
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

							try{
								Mail::send('emails.name', ['template' => $body, 'name' => $name, 'user_id' => $encrypt_user_id,'register_otp'=> $register_otp], function ($m) use ($from_email, $email, $name, $subject,$register_otp) 
								{
									$m->from($from_email, 'See Job Run');

									$m->to($email, $name)->subject($subject);
								});
							 }catch (\Exception $e) {
								// Log the error but do not show it to the user
								//Log::error('Error sending email: ' . $e->getMessage());
							}
			/**************End Twilio OTP Send*****************/
			


			return redirect()->route('otpAuthregister')->with("To complete your registration please verify your email now.");
        }
        catch (\Exception $e) 
        {
            return back()->withErrors($e->getMessage())->withInput($request->all());
        }
		
	}
	public function otpauthregister(Request $request){
	
		return view('user/registerotpauth');
	}
	public function verifyRegisterOtp(Request $request){
		$validator = Validator::make($request->all(), 
		[ 
		  'registerotp' => 'required',
		 
		]);  
	   
	   if ($validator->fails()) 
	   {  
		   return redirect()->back()->withErrors($validator);
	   }	
	   if($request->registerotp){
		$register_otp = $request->registerotp;
		$now = Carbon::now();
		$user_details = User::where('register_otp', $register_otp)->first();
	
        if ($user_details) 
        {
            if ($user_details->is_varified) 
            {
				
				return redirect()->back()->with('success', 'Your email is already confirmed.');

            } 
            else 
            {
                $update = User::where('register_otp', $register_otp)->update(['is_varified' => 1, 'email_verified_at' => $now]);
				return redirect()->route('user-login')->with('success', 'Your OTP has been successfully verified. Thank you for confirmation with SeeJobRun.');
				
            }
        } 
        else 
        {
            
			return redirect()->back()->withErrors(['Error' => 'Your OTP is invalid. Please contact to the admin.']);
			
        }

	   }else{

		return redirect()->back()->withErrors(['Error' => 'Please fill the OTP.']);
	   }

	}
	 public function dashboard(Request $request){
		 
		$user = Auth::user();
		$user_id = $user->id;
       
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
						
						//$invol_user_lead = job::with(['user', 'jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->whereHas('jobcontact', function($q) use($cont_id) {$q->where('contact_id', '=', $cont_id); })->where('job_type','=','Lead')->count();
						//$login_user_lead = job::with(['user','jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->where('user_id','=',$user_id)->where('job_type','=','Lead')->count();
						//$total_leads = $invol_user_lead + $login_user_lead ;
						
				
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
				$total_job_count = Job::where('user_id','=',$user_id)->where('job_type','!=','Lead')->where('job_type','!=','Archived')->count();
                $data['username'] = $user->name;
				$data['total_leads'] = $total_leads;
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
            }

		return view('user/dashboard',compact('user','data'));
		
	 }
	 
	 public function jobs(Request $request){
			
			
			$user = Auth::user();
			
			$search = $request->search;
			$jobtype = $request->filter;
			
			$user_id = $user->id;
			$contact = Contact::where('contact_user_id','=',$user_id)->first();		
		
			$contact_id = @$contact->id;
			
			$query = Job::query();

				// Apply search filter
				if ($search) {
					$query->where('name', 'like', '%' . $search . '%');
						 
				}

				// Apply filter option
				if ($jobtype) {
					$query->where('job_type', $jobtype);
				}

				// Apply additional jobcontact filtering if needed
				$query->whereHas('jobcontact', function ($query) use ($user_id, $contact_id) {
					$query->where('user_id', '=', $user_id)
						  ->orWhere('contact_id', '=', $contact_id);
				});

				// Eager load relationships
				$data = $query->with(['user', 'contact', 'jobcontact'])
			  ->where('job_type','!=','Lead')
              ->orderBy('contract_status', 'desc')
			  ->orderBy('id', 'desc'); // Order by id in descending order
              if ($jobtype !== 'Archived') {
				$data->where('job_type', '!=', 'Archived');
			}
			
			// Fetch the results
			$data = $data->get();
				
			$All_contacts = $user->contact()->with(['contactshared'])->get();
			$current_user_subscription = $this->checkUserSubscription($user);
			
			return view('user/jobs',compact('data','All_contacts','current_user_subscription'));
		 
	 }
	 public function addjob(Request $request){
		
		 
		  $validator = Validator::make($request->all(), 
			[ 
			   
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
				 return redirect()->back()->withErrors($validator);
			} 
			$user = Auth::user();
			$current_user_subscription = $this->checkUserSubscription($user);
			$active_subscription = $current_user_subscription['subscription_status'] ;
			if($user)
			{
				if($active_subscription =='active'){
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
					if($request->Lock_box_code){
						$job->Lock_box_code = $request->Lock_box_code;
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
                                {  try{
                                    Mail::send('emails.name', ['template' => $body, 'name' => $name], function ($m) use ($from_email, $email, $name, $subject) 
                                    {
                                        $m->from($from_email, 'See Job Run');

                                        $m->to($email, $name)->subject($subject);
                                    });
									}catch (\Exception $e) {
										// Log the error but do not show it to the user
										//Log::error('Error sending email: ' . $e->getMessage());
									}
                                }

							
						}
					}
				
				 return redirect()->back()->withSuccess("Your  Job has been Saved Successfully");
				}else{
					return back()->withErrors('You have no active plan.Please purchase the plan.');
				}
				
			}else{

				return back()->withErrors('User is not valid. please contact to the admin.');
			}			
		
		
	 }
	 

	public function deleteJob(Request $request){
		$validator = Validator::make($request->all(), 
		[ 
		   'job_id' => 'required',
		]);
 
		if ($validator->fails()) 
		{  
			 return redirect()->back()->withErrors($validator);
		} 
		$user = Auth::user();

		if($user){
			$job = Job::find($request->job_id);
				$job->delete();
				
				return redirect()->route('user.jobs')->withSuccess('You have successfully deleted your job.');


		}else{
			
			return back()->withErrors('User is not valid. please contact to the admin.');
		}

	}
	public function addContact(Request $request){
		//dd($request->all());
		if($request->type)
        {
            if($request->type == "1" || $request->type == "7")
            {
                $validator = Validator::make($request->all(), 
                [ 
                  
                   
                   'name' => 'required',
                   'email' => 'required',
                   
                ]);
            }
            else if($request->type == "2" || $request->type == "4" || $request->type == "5" || $request->type == "6")
            {
                $validator = Validator::make($request->all(), 
                [ 
                  
                   
                   'name' => 'required',
                   'email' => 'required',
                   

                ]);
            }
            else if($request->type == "3")
            {
                $validator = Validator::make($request->all(), 
                [ 
                   
                   'name' => 'required',
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
			
			$user = Auth::user();
            
            try
            {
                if($user)
                {
					
					$contact_user = Contact::where('user_id','=',$user->id)->where('email','=',$request->email)->first();
					if($contact_user){
						 return redirect()->back()->withErrors("Contact user already exist.Please try with another email id.");
					}else{
						
                    $contact = $user->contact()->make();
					if($request->email){
						$contact_user = User::where('email','=',$request->email)->first();
						if($contact_user){
							$contact->contact_user_id = $contact_user->id;
							$contact_user_id = $contact_user->id;
						}
					}
              /*      if($request->profile_pic){
						$frontimage = str_replace("data:image/jpeg;base64,", '', $request->profile_pic);
						$frontimage1 = str_replace(" ","+",$frontimage);
						$files_name = time() . '.jpeg';
						file_put_contents($files_name, base64_decode($frontimage1));
						$contact->profile_pic = $files_name;
				   } */
				   if ($request->profile_pic) {
						$file = $request->file('profile_pic');
						$filename = ((string)(microtime(true) * 10000)) . "-" . $file->getClientOriginalName();
						$destinationPath = public_path('/');
						
						$file->move($destinationPath, $filename);
						$contact->profile_pic = $filename;
					}
					
                    $contact->name = $request->name;
                    $contact->mobile = $request->mobile;
                    $contact->email = $request->email;
                    $contact->address = $request->address;
                    $contact->city = $request->city;
                    $contact->state = $request->state;
                    $contact->pincode = $request->pincode;
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
										try{
											Mail::send('emails.name', ['template' => $body, 'contact_firstname' => $contact_firstname,'contact_id'=> $contactID, 'contractor_id' => $user_id,'contractor_name'=>$contractor_name], function ($m) use ($from_email, $contact_email, $contact_firstname,$contactID,$contractor_name,$subject) 
											{
												$m->from($from_email, 'See Job Run');

												$m->to($contact_email, $contact_firstname)->subject($subject);
											});
										}catch (\Exception $e) {
											// Log the error but do not show it to the user
											//Log::error('Error sending email: ' . $e->getMessage());
										}
									}
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
									}catch (\Exception $e) {
										// Log the error but do not show it to the user
										//Log::error('Error sending email: ' . $e->getMessage());
									}
								}
								$message = "Hi {$contact_firstname},\n\n{$contractor_name} added you to SEE JOB RUN for project management.\nPlease download the application from the following links:\n\nAndroid – https://play.google.com/store/apps/details?id=com.clockk\niPhone – https://apps.apple.com/in/app/see-job-run/id6443558941\n\nThank you,\nAdmin See Job Run";
									$account_sid = getenv("TWILIO_SID");
									$auth_token = getenv("TWILIO_TOKEN");
									$twilio_number = getenv("TWILIO_FROM");
								
									$client = new Client($account_sid, $auth_token);
									$client->messages->create($receiverNumber, [
										'from' => $twilio_number, 
										'body' => $message]); 
			
							}
							
						
							return redirect()->back()->withSuccess("Your Contact has been Saved Successfully.");
							
						} else {

							return back()->withErrors('You don’t have sufficient credit. Please purchase more credit before proceeding.');
						}
				  }
                }
                else
                {
                  
					return back()->withErrors('Something went wrong. Please try again.');
                }
            }
            catch (\Exception $e) 
            {
              
				return back()->withErrors($e->getMessage());
            }
        }
        else
        {
			return back()->withErrors('The Type field is required.');
        }
	

	
	}


//job detail page
public function getJob($id){

	 $job_id= $id;
	  $user = Auth::user();
	  $user_id = $user->id;

	 if($user){

				// Job tabs permissions
				$jobcontacts = Jobcontacts::where('job_id', '=', $job_id)->get();
				$jobtabspermission = [];
				if ($jobcontacts->isNotEmpty()) {
					foreach ($jobcontacts as $jobcontact) {
						if ($jobcontact->contact_id) {
							$jobtabspermission['contactshared'] = Contact::with(['contactshared1' => function ($query) use ($job_id) {
								$query->where('job_id', '=', $job_id);
							}])->where('contact_user_id', '=', $user->id)->get();
						}
					}
					if (empty($jobtabspermission['contactshared']) || $jobtabspermission['contactshared']->isEmpty()) {
						$jobtabspermission['contactshared'] = [
							[
								"contactshared1" => []
							]
						];
					}
				} else {
					$jobtabspermission['contactshared'] = [
						[
							"contactshared1" => []
						]
					];
				}

		
		$job_details = job::with(['jobinspection','jobinspection.contact','contact','contact.contactshared'])->where('id','=',$job_id)->get();
		
		$contact = contact::where('contact_user_id','=',$user_id)->first();		
		$contact_id= $contact->id;
		$jobcontacts = Jobcontacts::where('job_id', '=', $job_id)->get();
		$data = [];
		
		if ($jobcontacts->isNotEmpty()) {
			foreach ($jobcontacts as $jobcontact) {
				if ($jobcontact->contact_id) {
					$data['contactshared'] = Contact::with(['contactshared1' => function ($query) use ($job_id,$user) {
						$query->where('job_id', '=', $job_id);
					}])->where('contact_user_id', '=', $user->id)->get();
				}
			}
			if (empty($data['contactshared']) || $data['contactshared']->isEmpty()) {
				$data['contactshared'] = [
					[
						"contactshared1" => collect() // or array() depending on your preference
					]
				];
			}
		}
		
		// Start Tasks

			// $tasks = Job::with([
			// 	'contact',
			// 	'taskassignment' => function ($query) use ($user_id, $contact_id) {
			// 		$query->where('user_id', '=', $user_id)
			// 			->orWhere('contact_id', '=', $contact_id)
			// 			//->orderBy('startdate', 'desc');
			// 			->orderBy('status', 'asc') 
			// 			->orderBy('startdate', 'desc'); 
			// 	},
			// 	'taskassignment.taskassignmentimages',
			// ])
			// ->where('id', '=', $job_id)
			// ->get();

			$tasks = Job::with([
				'contact',
				'taskassignment' => function ($query) use ($user_id, $contact_id) {
					$query->where('user_id', '=', $user_id)
						->orWhere('contact_id', '=', $contact_id)
						->orderBy('status', 'asc') // Checked tasks first
						->orderByRaw("CASE 
							WHEN startdate < CURDATE() THEN 2 
							ELSE 1 END") // Upcoming dates first, past dates last for unchecked tasks
						->orderBy('startdate', 'asc'); // Sort by date within each group
				},
				'taskassignment.taskassignmentimages',
			])
			->where('id', '=', $job_id)
			->get();
			
			



		//End Task

		$allcontact = $user->contact()->with(['contactshared'])->where('status', '=', 1)->get();
		
		//Job General

		$jobgeneral = job::with(['jobinspection','jobinspection.contact','contact','contact.contactshared'])->where('id','=',$job_id)->paginate(10);
		//dd($jobgeneral);
		
		//Punch list
		$punchlists='';
		$jobcontacts1 = Jobcontacts::where('job_id','=',$job_id)->where('user_id','=',$user_id)->get();
	
		if(count(@$jobcontacts1)>0){
			
			$punchlists = job::with(['jobinspection','jobinspection.contact','punchlist','punchlist.punchlistimg'])->where('id','=',$job_id)->paginate(10);
		}else{
				
				$contactss = Contact::where('contact_user_id','=',$user_id)->first();
				
		
				$ContactsharedPermission = Contactshared::where('contact_id','=',$contactss->id)->where('job_id','=',$job_id)->first();
				
				if(@$ContactsharedPermission->punchlist==1){
				
					$punchlists = job::with(['jobinspection','jobinspection.contact','punchlist.punchlistimg', 'punchlist' => function ($q) use ($user_id,$contactss,$job_id) {
							$q->where('user_id', "=", $user_id);
							$q->orwhere('contact_id', "=", $contactss->id);
							$q->orwhere('job_id', "=", $job_id);
						}])->where('id', '=',$job_id)->paginate(10);
					
				}else{
					
					$punchlists = job::with(['jobinspection','jobinspection.contact','punchlist.punchlistimg', 'punchlist' => function ($q) use ($user_id,$contactss,$job_id) {
							$q->where('user_id', "=", $user_id);
							$q->where('job_id', "=", $job_id);
							$q->orwhere('contact_id', "=", $contactss->id);
						}])->where('id', '=', $job_id)->paginate(10);
					
				}
			
			
		}

	

		
	// Punch List Approve
	

	$getpunchlistdate = Punchlist::where('job_id', '=', $job_id)->orderBy('updated_at', 'desc')->first();
	$getpunchlist = Punchlist::where('job_id', '=', $job_id)->get();
	
	$punchapprove = []; // Initialize the punch approve array
	if ($getpunchlist->isNotEmpty()) { // Check if the result is not empty
		$arrpunch = [];
	
		foreach ($getpunchlist as $punch) {
			$arrpunch[] = $punch->status; // Add status to the array
		}
	
		// Check if there is a punchlist with status "0"
		$key = array_search("0", $arrpunch);
		if ($key !== false) {
			$punchapprove['get_approvedpunchlists'] = [
				'message' => "Click to Approve 100% Completion",
				'status' => '0',
				'submessage' => "To be approved by client",
			];
		} else {
			if ($getpunchlistdate) { // Ensure there's a valid $getpunchlistdate before accessing it
				$punchapprove['get_approvedpunchlists'] = [
					'message' => 'Approved By Client On ' . Carbon::createFromFormat('Y-m-d H:i:s', $getpunchlistdate->updated_at)->format('F d, Y'),
					'status' => '1',
					'submessage' => "Client approved sign off",
				];
			}
		}
	}
	
	// end approve punch list
	

	// Get Stages


	   $stages = job::with(['jobstage','jobstage.stage'])->where('id','=',$job_id)->paginate(10);

	// job document

	   $jobdocument = Job::with(['jobmedia' => function ($query) {
				$query->orderBy('created_at', 'desc'); // Order jobmedia by created_at in descending order
			}, 'jobmedia.media'])
			->whereHas('jobmedia', function ($q) use ($job_id) {
				$q->where('job_id', '=', $job_id)
				->whereHas('media', function ($q) {
					$q->where('type', '=', 1); // Filter by type in media table
				});
			})
			->where('id', '=', $job_id) // Filter by specific job ID
			->paginate(10);

	//Job pictures
			$jobpictures = Job::with(['jobmedia' => function ($query) {
				$query->orderBy('created_at', 'desc'); // Order jobmedia by created_at in descending order
			}, 'jobmedia.media'])
			->whereHas('jobmedia', function ($q) use ($job_id) {
				$q->where('job_id', '=', $job_id)
				->whereHas('media', function ($q) {
					$q->where('type', '=', 2); // Filter by type in media table
				});
			})
			->where('id', '=', $job_id) // Filter by specific job ID
			->paginate(10);

	// Job Contact	
		$jobcontacts = job::with(['jobcontact','jobcontact.contact','jobcontact.contact.contactshared'=> function ($query) use ($job_id) {
			$query->where('job_id', '=', $job_id);
		}])->where('id','=',$job_id)->paginate(10);

		
		//Job Do sections and task
		$allsections = Todosection::with('todosectiontask')->where('user_id','=',$user->id)->where('job_id','=',$job_id)->get(); 
		return view('user/job_details', compact('jobtabspermission','data','job_details','tasks','allcontact','jobgeneral','punchlists','punchapprove','stages','jobdocument','jobpictures','jobcontacts','allsections'));
		

	 }
  
  }

  public function GetTasksAndPunchlistBydate(Request $request){
	 $event_date = $eventDate = $request->event_date;
	
		$job_id = $request->job_id;
		$user = Auth::user();
		
		if($user)
		{
			$user_id = $user->id;
			$contact = contact::where('contact_user_id','=',$user_id)->first();		
			$contact_id= $contact->id;

			$tasks = Job::with([
				'contact',
				'taskassignment' => function ($query) use ($user_id, $contact_id, $event_date) {
					$query->where(function ($query) use ($user_id, $contact_id) {
							$query->where('user_id', '=', $user_id)
								  ->orWhere('contact_id', '=', $contact_id);
						})
						->whereRaw('? BETWEEN DATE(startdate) AND DATE(enddate)', [$event_date])   // Check if enddate is after or on the event date
						->orderBy('status', 'asc')                   // Order by status in the taskassignment table
						->orderBy('title', 'asc');
				},
			])
			->where('id', '=', $job_id)
			->get();
			

			$punchlists = Job::with([
				'contact',
				'punchlist' => function ($query) use ($user_id, $contact_id, $event_date) {
					$query->where(function ($query) use ($user_id, $contact_id) {
						$query->where('user_id', '=', $user_id)
							  ->orWhere('contact_id', '=', $contact_id);
					})
					->whereRaw('? BETWEEN DATE(startdate) AND DATE(enddate)', [$event_date])
					->orderBy('status', 'asc')
					->orderBy('title', 'asc');
				},
			])
			->where('id', '=', $job_id)
			->get();
			
				//dd($tasks);
		 return response()->json(['success' => true,  'data' => $tasks,'punchlist'=>$punchlists]);	

		}else{
			return response()->json([
				'success' => false, 
				'message' => 'Unable to find data .Please try again or contact to admin.',
			]);
		}
  }

// Task Assignment in job  functions

public function AddTaskAssignment(Request $request){
	
	$validator = Validator::make($request->all(), 
	[ 
	   'job_id' => 'required',
	   'title' => 'required',

	   'startdate' => 'required',
	   'enddate' => 'required',
	]);

	if ($validator->fails()) 
	{  
		return redirect()->back()->withErrors($validator);
	}  

	$user = Auth::user();
	try
	{
		if($user)
		{
		   if($request->assign_to !='addcontact'){
			$taskassignment = $user->taskassignment()->make();
			$taskassignment->job_id = $request->job_id;
			$taskassignment->title = $request->title;
			$taskassignment->room = $request->room;
			$taskassignment->priority = $request->priority;
			$taskassignment->contact_id = $request->assign_to;
		   
			$startd = Carbon::createFromFormat('M d, Y', $request->startdate);
			$startdate = $startd->format('Y-m-d');
			$taskassignment->startdate = $startdate;
		   
			$enddate = Carbon::createFromFormat('M d, Y', $request->enddate);;
			$enddatedate = $enddate->format('Y-m-d');
			$taskassignment->enddate =   $enddatedate;

			//$taskassignment->enddate = $request->enddate;
			$taskassignment->description = $request->description;
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

		   if ($request->hasFile('new_images')) {
			   $i = 0; // Initialize the counter
		   
			   foreach ($request->file('new_images') as $file) {
				   $i++;
				   $taskassignmentimg = $user->taskassignmentimages()->make();
				   $taskassignmentimg->taskassignment_id = $taskassignment->id;
		   
				   $fileName = time() . $i . $file->getClientOriginalName();
				   $destinationPath = public_path('/');
				   $file->move($destinationPath, $fileName); // Corrected from $filename to $fileName
				   $taskassignmentimg->image = $fileName;
		   
				   $taskassignmentimg->save();
			   }
		   }
			return redirect()->back()->withSuccess("Your Taskassignment has been added successfully.");
		}else{
			return back()->withErrors('You need to select the Assign to field');
		}
		}
		else
		{
			return back()->withErrors('User is not valid. please contact to the admin.');
		}
	}
	catch (\Exception $e) 
	{
	   
		return back()->withErrors($e->getMessage());
	}
   

}

public function approvedSingleTask(Request $request){
	$validator = Validator::make($request->all(), [
		'taskassignment_id' => 'required',
		'status' => 'required',
	]);

	if ($validator->fails()) 
	{  
		return response()->json(['error'=>$validator->errors()]); 
	}  

	$user = Auth::user();
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
                        'message' => 'Your Task has been update successfully.',
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
                    'message' => 'User is not valid. please contact to the admin.',
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
  public function delete_taskassignment(Request $request)
  {
	  $validator = Validator::make($request->all(), [
		
		  'id' => 'required'
	  ]);

	  if ($validator->fails()) 
	  {
		  return response()->json(['error' => $validator->messages()]);
	  }

	  $user = Auth::user();

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
				  'message' => 'User is not valid. please contact to the admin.',
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

// Task Assignment attachment
public function delete_taskassignmentattachement(Request $request)
  {
	$validator = Validator::make($request->all(), [
		'id' => 'required',
		'taskassignment_id'=>'required'
	]);

	if ($validator->fails()) 
	{
		return redirect()->back()->withErrors($validator);
	}

	$user = Auth::user();

	try
	{
		if($user)
		{
			$image = Taskassignmentimages::where('id', $request->id)
			->where('taskassignment_id', $request->taskassignment_id)
			->first();
				
					if ($image) {
						// Unlink the image file from the server
						$imagePath = public_path($image->image); // Get the file path
						if (file_exists($imagePath)) {
							unlink($imagePath); // Delete the file
						}
				
						// Delete the record from the database
						$image->delete();
				
						return response()->json([
							'success' => true,
							'message' => 'Assigned Task Attachment has been deleted successfully.',
						]);
					} else {
						return response()->json([
							'success' => false,
							'message' => 'Unable to delete task assignment attachment. Please try again or contact the admin.',
						]);
					}
		}
		else
		{
			return response()->json([
				'success' => false,
				'message' => 'User is not valid. please contact to the admin.',
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
		'id'    => 'required',
		'job_id' => 'required',
		'title' => 'required',
		'assign_to' => 'required',
		'startdate' => 'required',
		'enddate' => 'required',
	 ]);

	 if ($validator->fails()) 
	 {  
		return redirect()->back()->withErrors($validator);
	 } 

	 $user = Auth::user();

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
			
			 $startd = Carbon::createFromFormat('M d, Y', $request->startdate);
			 $startdate = $startd->format('Y-m-d');
			 $taskassignment->startdate = $startdate;
			
			 $enddate = Carbon::createFromFormat('M d, Y', $request->enddate);;
			 $enddatedate = $enddate->format('Y-m-d');
			 $taskassignment->enddate =   $enddatedate;

			 //$taskassignment->enddate = $request->enddate;
			 $taskassignment->description = $request->description;

			 $taskassignment->save();

			if ($request->hasFile('new_images')) {
				$i = 0; // Initialize the counter
			
				foreach ($request->file('new_images') as $file) {
					$i++;
					$taskassignmentimg = $user->taskassignmentimages()->make();
					$taskassignmentimg->taskassignment_id = $taskassignment->id;
			
					$fileName = time() . $i . $file->getClientOriginalName();
					$destinationPath = public_path('/');
					$file->move($destinationPath, $fileName); // Corrected from $filename to $fileName
					$taskassignmentimg->image = $fileName;
			
					$taskassignmentimg->save();
				}
			}
			
			 return redirect()->back()->withSuccess("Your Taskassignment has been updated.");
		 }
		 else
		 {
			 return back()->withErrors('User is not valid. please contact to the admin.');
		 }
	 }
	 catch (\Exception $e) 
	 {
		
		 return back()->withErrors($e->getMessage());
	 }
	
}
public function updatefPunchlist(Request $request){
	$validator = Validator::make($request->all(), [
		'punchlist_id' => 'required',
		'status' => 'required',
	]);

	if ($validator->fails()) 
	{  
		return response()->json(['error'=>$validator->errors()]); 
	}  

	$user = Auth::user();
	try
        {
            if($user)
            {
                $punchlist = Punchlist::findorfail($request->punchlist_id);
                $punchlist->status = $request->status;

                if ($punchlist->save()) 
                {
					
                    return response()->json([
                        'success' => true, 
                        'message' => 'Your Punchlist Item has been update successfully.',
                    ]);
                } 
                else 
                {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Unable to update Punchlist Item.Please try again or contact to admin.',
                    ]);
                } 
            }
            else
            {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not valid. please contact to the admin.',
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
public function updateSinglePunchlist(Request $request){
	$validator = Validator::make($request->all(), 
	 [ 
		'id'    => 'required',
		'job_id' => 'required',
		'title' => 'required',
		'assign_to' => 'required',
		'startdate' => 'required',
		'enddate' => 'required',
	 ]);

	 if ($validator->fails()) 
	 {  
		return redirect()->back()->withErrors($validator);
	 } 

	 $user = Auth::user();
	 try
        {
			if($user){
				
				$user_id = $user->id;
				$startd = Carbon::createFromFormat('M d, Y', $request->startdate);
				$startdate = $startd->format('Y-m-d');
				$fstartdate = $startdate;
				
				$enddate = Carbon::createFromFormat('M d, Y', $request->enddate);;
				$enddatedate = $enddate->format('Y-m-d');
				$fenddate =   $enddatedate;

				$punchlist = punchlist::where('id','=',$request->id)->where('user_id','=',$user_id)->where('job_id','=',$request->job_id)
				->update(['title'=>$request->title,'description'=>$request->description,'room'=>$request->room,
				'priority'=>$request->priority,'contact_id'=>$request->assign_to,'startdate'=>$fstartdate,
				'enddate'=>$enddatedate]);
				
				
				if ($request->hasFile('punchlist_images')) {
					$i = 0; // Initialize the counter
				
					foreach ($request->file('punchlist_images') as $file) {
						$i++;
						$punchimg = $user->punchlistimg()->make();

                        $punchimg->punch_id = $request->id;
				
						$fileName = time() . $i . $file->getClientOriginalName();
						$destinationPath = public_path('/');
						$file->move($destinationPath, $fileName); // Corrected from $filename to $fileName
						$punchimg->image = $fileName;
				
						$punchimg->save();
					}
				}
				
				return redirect()->back()->with(['success'=>'Your punchlist has been update successfully.','activeTab'=>'final-punchlist']);
				
			}else{
				return redirect()->back()->with([
					'error' => 'User is not valid. please contact to the admin.',
					'activeTab' => 'final-punchlist'
				]);
			}
		}catch (\Exception $e) 
		{
			return redirect()->back()->with([
				'error' => $e->getMessage(),
				'activeTab' => 'final-punchlist'
			]);
		}

}
public function deleteSinglePunchlistAttachment(Request $request)
  {
	$validator = Validator::make($request->all(), [
		'id' => 'required',
		'punch_id'=>'required'
	]);

	if ($validator->fails()) 
	{
		return redirect()->back()->withErrors($validator);
	}

	$user = Auth::user();

	try
	{
		if($user)
		{
			$image = Punchlistimg::where('id', $request->id)
			->where('punch_id', $request->punch_id)
			->first();
				
					if ($image) {
						// Unlink the image file from the server
						$imagePath = public_path($image->image); // Get the file path
						if (file_exists($imagePath)) {
							unlink($imagePath); // Delete the file
						}
				
						// Delete the record from the database
						$image->delete();
				
						return response()->json([
							'success' => true,
							'message' => 'Assigned Punchlist Attachment has been deleted successfully.',
						]);
					} else {
						return response()->json([
							'success' => false,
							'message' => 'Unable to delete punch list assignment attachment. Please try again or contact the admin.',
						]);
					}
		}
		else
		{
			return response()->json([
				'success' => false,
				'message' => 'User is not valid. please contact to the admin.',
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
public function deleteSinglePunchlist(Request $request)
{
	  $validator = Validator::make($request->all(), [
		
		  'id' => 'required'
	  ]);

	  if ($validator->fails()) 
	  {
		  return response()->json(['error' => $validator->messages()]);
	  }

	  $user = Auth::user();

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
					  'message' => 'Unable to delete punchlist .Please try again or contact to admin.',
				  ]);
			  }
		  }
		  else
		  {
			  return response()->json([
				  'success' => false,
				  'message' => 'User is not valid. please contact to the admin.',
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
public function AdddfPunchlist(Request $request){

	$validator = Validator::make($request->all(), 
	[ 
	   'job_id' => 'required',
	   'title' => 'required',
	   'startdate' => 'required',
	   'enddate' => 'required',
	]);

	if ($validator->fails()) 
	{  
		return redirect()->back()->withErrors($validator);
	}  

	$user = Auth::user();

	try
	{
		if($user)
		{
		   if($request->assign_to !='addcontact'){
			$punchlist = $user->punchlist()->make();;
			$punchlist->job_id = $request->job_id;
			$punchlist->title = $request->title;
			$punchlist->room = $request->room;
			$punchlist->priority = $request->priority;
			$punchlist->contact_id = $request->assign_to;
		   
			$startd = Carbon::createFromFormat('M d, Y', $request->startdate);
			$startdate = $startd->format('Y-m-d');
			$punchlist->startdate = $startdate;
		   
			$enddate = Carbon::createFromFormat('M d, Y', $request->enddate);;
			$enddatedate = $enddate->format('Y-m-d');
			$punchlist->enddate =   $enddatedate;

			//$taskassignment->enddate = $request->enddate;
			$punchlist->description = $request->description;
			if(!empty($request->room) && $request->room !=null && $request->room !='null' ){
				$punchlist->room = $request->room;
			}else{
				$punchlist->room ='none' ;
			}
			if(!empty($request->description) && $request->description !=null && $request->description !='null' ){
				$punchlist->description = $request->description;
			}else{
				$punchlist->description='';
			}

			$punchlist->save();

		   if ($request->hasFile('new_images')) {
			   $i = 0; // Initialize the counter
		   
			   foreach ($request->file('new_images') as $file) {
				   $i++;
				   $punchlistimg = $user->punchlistimg()->make();
				   $punchlistimg->punch_id = $punchlist->id;
		   
				   $fileName = time() . $i . $file->getClientOriginalName();
				   $destinationPath = public_path('/');
				   $file->move($destinationPath, $fileName); // Corrected from $filename to $fileName
				   $punchlistimg->image = $fileName;
		   
				   $punchlistimg->save();
			   }
		   }
			
			return redirect()->back()->with(['success'=>'Your Punchlist has been added successfully.','activeTab'=>'final-punchlist']);
		}else{
			return back()->withErrors('You need to select the Assign to field');
		}
		
		}
		else
		{
			
			return redirect()->back()->with([
				'error' => 'User is not valid. please contact to the admin.',
				'activeTab' => 'final-punchlist'
			]);
		}
	}
	catch (\Exception $e) 
	{
	   
		
		return redirect()->back()->with([
			'error' => $e->getMessage(),
			'activeTab' => 'final-punchlist'
		]);
	}

}

public function updateAllPunchlist(Request $request){
	$validator = Validator::make($request->all(), [
		'job_id' => 'required',
	]);

	if ($validator->fails()) 
	{  
		return response()->json(['error'=>$validator->errors()]); 
	}  

   $user = Auth::user();

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
				'message' => 'User is not valid. please contact to the admin.',
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
public function updateJobGeneral(Request $request){
	$validator = Validator::make($request->all(), 
	 [ 
		'job_id'  => 'required',
  
	 ]);

	 if ($validator->fails()) 
	 {  
		return redirect()->back()->withErrors($validator);
	 } 

	 $user = Auth::user();

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
					if($request->name){
						$job->name = $request->name;
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
						if($request->Lock_box_code==' '){
							$job->Lock_box_code = '';
						}elseif($request->Lock_box_code==0){
							$job->Lock_box_code = 0;
						}else{
							$job->Lock_box_code = $request->Lock_box_code;
						}
						if($request->job_type){
							$job->job_type = $request->job_type;
						}
						if($request->contract_status==0 OR $request->contract_status==1){
							$job->contract_status = $request->contract_status;
						}
						
						if($request->job_type=='Archived'){
								$job->status = 2;
						}else{
						
							$job->status = 1;
						}
					
					if ($job->save()) 
					{
			
						return redirect()->back()->with(['success'=>'Job has been updated successfully','activeTab'=>'general']);
						
					} 
					else 
					{
						return back()->withErrors('Unable to update Job.Please try again or contact to admin.');
					} 
				}
				else
				{
					return back()->withErrors('Unable to update Job.Please try again or contact to admin.');
				}
			}
			else
			{
				return back()->withErrors('Unable to update Job.Please try again or contact to admin.');
			}
		}
		catch (\Exception $e) 
		{
		
			return back()->withErrors($e->getMessage());
		}

}

Public function AddJobStage(Request $request){
	$validator = Validator::make($request->all(), 
	[ 
	   'name' => 'required',
	   'job_id' => 'required',
	]);

	if ($validator->fails()) 
	{  
		return redirect()->back()->withErrors($validator);
	}  

	$user = Auth::user();

	try
	{
		if($user)
		{
		   
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
			
			return redirect()->back()->with(['success'=>'Your job Stage has been Saved Successfully..','activeTab'=>'stage']);
		}
		else
		{
			
			return redirect()->back()->with([
				'error' => 'User is not valid. please contact to the admin.',
				'activeTab' => 'stage'
			]);
		}
	}
	catch (\Exception $e) 
	{
	   
		
		return redirect()->back()->with([
			'error' => $e->getMessage(),
			'activeTab' => 'stage'
		]);
	}

}
public function GetJobStageByid(Request $request){
		$stage_id = $request->id;
		$jobstage  = stage::where('id','=',$request->id)->first();

		return response()->json($jobstage);

}

public function updateStage(Request $request){
	$validator = Validator::make($request->all(), 
	[ 
	   'id' => 'required',
	]);

	if ($validator->fails()) 
	{  
		return redirect()->back()->withErrors($validator);
	}  

	$user = Auth::user();

	try
	{
		if($user)
		{
		   
			$stage = Stage::findorfail($request->id);
					
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
					
			$stage->save();

			return redirect()->back()->with(['success'=>'Stage has been updated successfully.','activeTab'=>'stage']);
		}
		else
		{
			
			return redirect()->back()->with([
				'error' => 'User is not valid. please contact to the admin.',
				'activeTab' => 'stage'
			]);
		}
	}
	catch (\Exception $e) 
	{
	   
		
		return redirect()->back()->with([
			'error' => $e->getMessage(),
			'activeTab' => 'stage'
		]);
	}

}

public function stageorder(Request $request){
    if ($request->has('rearrangeorder')) {
        $reorderArray = $request->input('rearrangeorder');  // Retrieve the rearranged order

        foreach ($reorderArray as $reorder) {
            // Assuming $reorder contains 'stage_id' and 'position'
            $stage = Jobstage::where('job_id', $request->job_id)
                ->where('stage_id', $reorder['stage_id'])
                ->first();

            if ($stage) {
                $stage->stage_order = $reorder['position'];
                $stage->save();
            }
        }

        // Return success response
		return response()->json([
			'success' => true, 
			'message' => 'Stages order updated successfully.',
		]);
    } else {
		return response()->json([
			'success' => false, 
			'message' => 'Something went wrong !. Please contact to admin.',
		]);
    }

}
public function deleteStage(Request $request){
	$validator = Validator::make($request->all(), [
		
		'id' => 'required'
	]);

	if ($validator->fails()) 
	{
		return response()->json(['error' => $validator->messages()]);
	}

	$user = Auth::user();

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
				'message' => 'User is not valid. please contact to the admin.',
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
public function addJobDocument(Request $request){
	$validator = Validator::make($request->all(), 
        [ 
		   'job_id'=> 'required',
           'name' => 'required',
		   'file_name' => 'required|file|mimes:doc,pdf,txt,csv,xlsx,docx',
        ]);
	
        if ($validator->fails()) 
		{  
			return redirect()->back()->withErrors($validator)->with([
				'error' => 'Validation errors occurred.', // Optional error message
				'activeTab' => 'document'
			]);
		}
  
		
		$user = Auth::user();
		
		 try
        {  
		if($user){
			
			$file = $request->file('file_name');
			$extension = $file->getClientOriginalExtension();
			$file_name1 = 'media/' . time() . '.' . $extension;
			$file->Move(public_path('media'), $file_name1);
		    $media = $user->media()->make(); 
			$media->name = $request->name; 
			$media->image = $file_name1; 
			$media->status = 1; 
			$media->type = 1; 
			$media->save();

				$media_id = $media->id;
				if($media_id){
					$jobmedia = $user->jobmedia()->make();
					$jobmedia->job_id = $request->job_id;
					$jobmedia->media_id = $media_id;
					$jobmedia->save();
					
					return redirect()->back()->with(['success'=>'Your documents have been saved.','activeTab'=>'document']);

					
				} 
			}else{
				
				return redirect()->back()->with([
					'error' => 'User is not valid. please contact to the admin.',
					'activeTab' => 'document'
				]);
			}
		 }catch (\Exception $e) 
		{
			return redirect()->back()->with([
				'error' => $e->getMessage(),
				'activeTab' => 'document'
			]);
			
		} 

}



public function deleteJobAttachment(Request $request){

	$validator = Validator::make($request->all(), [
		'media_id'=>'required'
	]);

	if ($validator->fails()) 
	{
		return response()->json(['error' => $validator->messages()]);
	}

	$user = Auth::user();

	try
	{
		if($user)
		{
			$jobmedia = $user->jobmedia()->where('media_id', '=', $request->media_id)->delete();
				$media 	  = $user->media()->where('id', '=', $request->media_id)->delete();
				
				
				 return response()->json([
					'success' => true,
					'message' => 'Your JobMedia has been deleted Successfully.'
				]);
		}
		else{
			return response()->json([
				'success' => false,
				'message' => 'User is not valid. please contact to the admin.',
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

// Job pictures
public function addJobPicture(Request $request){
	$validator = Validator::make($request->all(), 
        [ 
		   'job_id'=> 'required',
           'name' => 'required',
		   'file_name' => 'required|file|mimes:jpeg,png,gif,jpg|max:2048',
        ]);
	
        if ($validator->fails()) 
		{  
			return redirect()->back()->withErrors($validator)->with([
				'error' => 'Validation errors occurred.', // Optional error message
				'activeTab' => 'pictures'
			]);
		}
  
		
		$user = Auth::user();
		
		 try
        {  
		if($user){
			
			
			    $file = $request->file('file_name');
				$extension = 'jpeg'; // We will convert to JPEG
				$file_name1 = 'media/' . time() . '.' . $extension;

				// Create an instance of Image
				$image = Image::make($file);
				
				// Convert to JPEG and save
				$image->save(public_path($file_name1));

				// Now save the media information
				$media = $user->media()->make();
				$media->name = $request->name; 
				$media->image = $file_name1; 
				$media->status = 1; 
				$media->type = 2; 
				$media->save();
				$media_id = $media->id;
				if($media_id){
					$jobmedia = $user->jobmedia()->make();
					$jobmedia->job_id = $request->job_id;
					$jobmedia->media_id = $media_id;
					$jobmedia->save();
					
					return redirect()->back()->with(['success'=>'Your photo has been saved.','activeTab'=>'pictures']);

					
				} 
			}else{
				
				return redirect()->back()->with([
					'error' => 'User is not valid. please contact to the admin.',
					'activeTab' => 'pictures'
				]);
			}
		 }catch (\Exception $e) 
		{
			return redirect()->back()->with([
				'error' => $e->getMessage(),
				'activeTab' => 'pictures'
			]);
			
		} 

}
public function deleteJobpicture(Request $request){

	$validator = Validator::make($request->all(), [
		'media_id'=>'required'
	]);

	if ($validator->fails()) 
	{
		
		return redirect()->back()->withErrors($validator)->with([
			'error' => 'Validation errors occurred.', // Optional error message
			'activeTab' => 'pictures'
		]);
	}

	$user = Auth::user();

	try
	{
		if($user)
		{
			$jobmedia = $user->jobmedia()->where('media_id', '=', $request->media_id)->delete();
				$media 	  = $user->media()->where('id', '=', $request->media_id)->delete();
				
				
				 return response()->json([
					'success' => true,
					'message' => 'Your Job Picture has been deleted Successfully.'
				]);
		}
		else{
			return response()->json([
				'success' => false,
				'message' => 'User is not valid. please contact to the admin.',
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


// job Contact tab
public function UpdateContactsharedPermission(Request $request){
	$validator = Validator::make($request->all(), 
	[ 
	   
	   'contact_id' => 'required',
	   'job_id' => 'required',
	]);

	if ($validator->fails()) 
	{  
		
		return redirect()->back()->withErrors($validator)->with([
			'error' => 'Validation errors occurred.', 
			'activeTab' => 'contacts'
		]);
	}  
	//dd($request->all());
	$user = Auth::user();
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

				return redirect()->back()->with(['success'=>'You have updated Contactshared Permission Successfully.','activeTab'=>'contacts']);
	}else{
			return redirect()->back()->with([
				'error' => 'User is not valid. please contact to the admin.',
				'activeTab' => 'contacts'
			]);
	}


}
public function addJobContactsbyJobId(Request $request){
	$validator = Validator::make($request->all(), 
	[ 
	   
	   'contact_id' => 'required',
	   'job_id' => 'required',
	]);

	if ($validator->fails()) 
	{  
		
		return redirect()->back()->withErrors($validator)->with([
			'error' => 'Validation errors occurred.', 
			'activeTab' => 'contacts'
		]);
	}  
	
	$user = Auth::user();
	if($user){
		$jobcontactcount = Jobcontacts::where('job_id','=',$request->job_id)->where('contact_id','=',$request->contact_id)->count();
		
		if($jobcontactcount>0){
			
			return redirect()->back()->with(['error'=>'Contact already Exist.','activeTab'=>'contacts']);
			

		}else{
					$jobcontact = $user->jobcontact()->make();
                    $jobcontact->job_id = $request->job_id;
                    $jobcontact->contact_id = $request->contact_id;
                    $jobcontact->save();
					
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
					$contactshared->todo = 0;
                    $contactshared->save();
					$job = Job::where('id','=',$request->job_id)->first();
					$contacts = Contact::find($request->contact_id);
					$contact_user_id = $contacts->contact_user_id;
						if($contact_user_id){
							//notification send	
							$msg["title"] = "New Job";
							$msg["body"] = "You are invited to join this job ".$job->name;
							$msg['type'] = "job";
							$msg['client_id'] = $contacts->contact_user_id;
							$msg['user_type'] = $contacts->type_name;
							$msg['move'] = 'Home';
							$this->sendNotification($user->id, $msg);
							
						}
					return redirect()->back()->with(['success'=>'Your Job Contact has been Saved Successfully.','activeTab'=>'contacts']);			

		}
	}else{
		return redirect()->back()->with(['error'=>'User is not valid. please contact to the admin.','activeTab'=>'contacts']);
	}

}
public function deleteJobContact(Request $request){
	$validator = Validator::make($request->all(), [
		'contact_id'=>'required',
		'job_id'=>'required'
	]);

	if ($validator->fails()) 
	{
		
		return redirect()->back()->withErrors($validator)->with([
			'error' => 'Validation errors occurred.', // Optional error message
			'activeTab' => 'contacts'
		]);
	}

	$user = Auth::user();

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
		else{
			return response()->json([
				'success' => false,
				'message' => 'User is not valid. please contact to the admin.',
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



Public function GetSingleContact(Request $request){
	// dd($request->all());
	$contact_id = $request->input('contact_id');
	$contact  = Contact::where('id','=',$contact_id)->first();
	return response()->json($contact);
}

// My daily Tasks
Public function myDailyTasks(Request $request){
	$user = Auth::user();
	if ($user) {
		$user_id = $user->id;
		$contact = contact::where('contact_user_id', '=', $user_id)->first();		
		$contact_id = @$contact->id;	
		$mydailytasks = Job::with([
			'taskassignment' => function ($query) use ($user_id, $contact_id) {
				$query->where(function ($subQuery) use ($user_id, $contact_id) {
					$subQuery->where('user_id', '=', $user_id)
							 ->orWhere('contact_id', '=', $contact_id);
				})->where('show_and_hide', '!=', 1); // Correct placement of the filter
			},
			'taskassignment.taskassignmentimages'
		])->get();
		
		
		
		$today = Carbon::today();  // Get current date without time
		
		// Filter tasks for today, past due, and completed tasks
		$todayTasks = [];
		$pastDueTasks = [];
		$completedTasks = [];
	
		foreach ($mydailytasks as $job) {
			$jobname = $job->name;  // Assuming 'jobname' is a field in the Job model
			foreach ($job->taskassignment as $task) {
				$startDate = Carbon::parse($task->startdate);  // Parse to Carbon object
				$endDate = Carbon::parse($task->enddate);      // Parse to Carbon object
				$completed = $task->status == 1;  // Completed tasks have status = 1
				
				if ($completed) {
					$completedTasks[] = [
						'jobname' => $jobname,
						'task' => $task
					];
				} elseif ($endDate->lt($today)) {  // Compare with lessThan (lt)
					$pastDueTasks[] = [
						'jobname' => $jobname,
						'task' => $task
					];
				} elseif ($startDate->lte($today) && $endDate->gte($today)) {  // Compare if it falls within today
					$todayTasks[] = [
						'jobname' => $jobname,
						'task' => $task
					];
				}
			}
		}
	}
	//dd($todayTasks);
	return view('user/mydailytasks', compact('todayTasks','completedTasks','pastDueTasks'));
}

public function gettaskbydate(Request $request){
	$user = Auth::user();
	$event_date = $request->clickdate;

	if ($user) {
		$user_id = $user->id;
		$contact = contact::where('contact_user_id', '=', $user_id)->first();		
	    $contact_id = $contact->id;	
		
		$mydailytasks = Job::whereHas('taskassignment', function ($query) use ($user_id, $contact_id, $event_date) {
        $query->where(function ($query) use ($user_id, $contact_id) {
            $query->where('user_id', '=', $user_id)
                  ->orWhere('contact_id', '=', $contact_id);
        })
        ->whereRaw('? BETWEEN DATE(startdate) AND DATE(enddate)', [$event_date]);
		})->with([
			'taskassignment' => function ($query) use ($user_id, $contact_id, $event_date) {
				$query->where(function ($query) use ($user_id, $contact_id) {
					$query->where('user_id', '=', $user_id)
						->orWhere('contact_id', '=', $contact_id);
				})
				->whereRaw('? BETWEEN DATE(startdate) AND DATE(enddate)', [$event_date]);
			},
			'taskassignment.taskassignmentimages'
		])->get();

		
		//dd($mydailytasks);
		
			
		return response()->json(['success' => true,  'tasks' => $mydailytasks]);	

	}else{
		return response()->json([
			'success' => false, 
			'message' => 'Unable to find data .Please try again or contact to admin.',
		]);
	}




}
public function showAndHideTask(Request $request){
	$validator = Validator::make($request->all(), [
		'id'=>'required',
		'show_and_hide'=>'required'
	]);

	if ($validator->fails()) 
		{  
			 return redirect()->back()->withErrors($validator);
		} 

	$user = Auth::user();
		
	try
	{
		if($user)
		{
			if($request->show_and_hide==1){
				$showhideStatus = 0;
			}else{
				$showhideStatus = 1;
			}

			$taskassignment = Taskassignment::findorfail($request->id);
			$taskassignment->show_and_hide = $showhideStatus;

			if ($taskassignment->save()) 
			{
				
				return redirect()->back()->withSuccess("Your Task Assignment status has been update successfully.");
			} 
			else 
			{
				return redirect()->back()->withErrors("Unable to update Task Assignment.Please try again or contact to admin.");
			} 
		}
		else{
			
			return redirect()->back()->withErrors("User is not valid. please contact to the admin.");
		}
	}
	catch (\Exception $e) 
	{
		
			return redirect()->back()->withErrors($e->getMessage());	
	}
	

}
public function HiddenTasks(Request $request){
	$user = Auth::user();
	
	if ($user) {
		$user_id = $user->id;
		$contact = contact::where('contact_user_id', '=', $user_id)->first();		
		$contact_id = @$contact->id;	
	
		$hiddentasks = Job::with([
			'taskassignment' => function ($query) use ($user_id, $contact_id) {
				$query->where(function ($subQuery) use ($user_id, $contact_id) {
					$subQuery->where('user_id', '=', $user_id)
							 ->orWhere('contact_id', '=', $contact_id);
				})->where('show_and_hide', '=', 1); // Correct placement of the filter
			},
			'taskassignment.taskassignmentimages'
		])->get();
	}
	//dd($hiddentasks);
return view('user/mydailytasks', compact('hiddentasks'));

}

//My Contacts tab

Public function GetAllMyContact(Request $request){

	$user = Auth::user();
	$search = $request->search;

	// Initialize the query
	$query = $user->contact()->with(['contactshared']);

	// Apply filter conditions only if a filter is provided
	if (!empty($request->filter)) {
		if ($request->filter == 'Pending') {
			$query->where('status', 0);
		} elseif ($request->filter == 'Archived') {
			$query->where('status', 2);
		} else {
			$query->where('type', $request->filter);
		}
	}else {
		// When no specific filter is applied, exclude items with status = 2
		$query->where('status', '!=', 2);
	}

	// Add search functionality if a search term is provided
	if (!empty($search)) {
		$query->where(function ($q) use ($search) {
			$q->where('name', 'LIKE', '%' . $search . '%')
			->orWhere('email', 'LIKE', '%' . $search . '%');
		});
	}

	// Retrieve the filtered and searched contacts
	$allContacts = $query->paginate(10);

	
	return view('user/my_contact',compact('allContacts'));
			
}

public function DeleteContact(Request $request){
		
	$validator = Validator::make($request->all(), 
	[ 
	   
	   'id' => 'required',
	]);

	if ($validator->fails()) 
	{  
		return response()->json(['error'=>$validator->errors()]); 
	}  
	
	$user = Auth::user();
	
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

public function updateContact(Request $request){
	$validator = Validator::make($request->all(), 
	[ 
	   'id' => 'required',
	  
	]);

	if ($validator->fails()) 
	{  
		return redirect()->back()->withErrors($validator);
	} 
	
	$user = Auth::user();
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
               
                
				if ($request->profile_pic) {
					$file = $request->file('profile_pic');
					$filename = ((string)(microtime(true) * 10000)) . "-" . $file->getClientOriginalName();
					$destinationPath = public_path('/');
					
					$file->move($destinationPath, $filename);
					$contact->profile_pic = $filename;
				}
                $contact->save();
				

		return redirect()->back()->withSuccess("You have successfully updated the contact");
	}else{
		return redirect()->back()->withErrors("Something went wrong!.");
	}

}
//Archive and unarchive contact

public function ArchiveOrUnArchiveContact(Request $request){

	$validator = Validator::make($request->all(), 
	[ 
	   'contact_id' => 'required',
	   'status' => 'required'	  
	]);

	if ($validator->fails()) 
	{  
		return redirect()->back()->withErrors($validator);
	} 
	
	$user = Auth::user();
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
			
		   return redirect()->back()->withSuccess("You have successfully archived the contact.");
			
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
						
						   return redirect()->back()->withErrors($api_error);
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
						
					   return redirect()->back()->withSuccess("You have successfully unarchived the contact.");
						
					}
				} 
			}else{
				$archivecontact->status=1;
				$archivecontact->save();
			}	
			
		   return redirect()->back()->withSuccess("You have successfully unarchived the contact."); 
		}
		
		
	}else{
		
			return redirect()->back()->withErrors("User is not valid. please contact to the admin.");
	}


}

public function CreditContact(Request $request){
	$validator = Validator::make($request->all(), 
	[ 
	   'price' => 'required',
	   'contact_credit' => 'required'	  
	]);

	if ($validator->fails()) 
	{  
		return redirect()->back()->withErrors($validator);
	} 
	
	$user = Auth::user();
	if($user){
		return redirect()->route('BuyCreditcontact')->withErrors($validator)->withInput();
	}else{
		return redirect()->back()->withErrors("User is not valid. please contact to the admin.");
	}
}

public function Buycreditcontact(){
	return view('user/buycreditcontact');
}
public function Buycreditcontacts(Request $request){
	//dd($request->all());
	$validator = Validator::make($request->all(), 
	[ 
	   'contact_credit' => 'required',
	   'stripeToken' => 'required',
	   'amount' => 'required'	  
	]);

	if ($validator->fails()) 
	{  
		return redirect()->back()->withErrors($validator);
	} 
	
	$user = Auth::user();

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
		
					return redirect()->back()->withErrors($e->getMessage());
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
							return redirect()->back()->withErrors($e->getMessage());
						}	

					}
				}
			}catch (Exception $e) {
				
			   return redirect()->back()->withErrors($e->getMessage());
			}
	
		return redirect()->back()->withSuccess('You have successfully buy '.$total_credits); 
			   
	}else{
		
			return redirect()->back()->withErrors('User is not valid. please contact to the admin.');
	}


}
// change orders
public function changeOrders(Request $request){
 	$user = Auth::user();
	$user_id= $user->id;

	$contact_id = $user->contactuserid()->where('contact_user_id','=',$user_id)->get();
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
	
	$query = $user->contact()->with(['contactshared']);
	$query->whereNotIn('status', [0, 2]);
	$recepeints = $query->get();

 return view('user/changeorder', compact('jobdata','recepeints'));

}
public function getchangeOrderListByJobid(Request $request){
	$jobId = $request->input('job_id');
	$user = Auth::user();
	try
	{
		if($user)
		{
			
			$jobcontact = Contact::where('contact_user_id','=',$user->id)->first();
			
			if(@$jobcontact){
				//$changeorderdata = Changeorder::with(['job','item'])->where('job_id','=',$request->job_id)->where('client_id', 'LIKE', '%'.$jobcontact->id.'%')->orWhere('user_id','=',$user->id)->get();
				$changeorderdata = Changeorder::with(['job','item'])->where('job_id','=',$jobId)->get();
			}else{
			
				$changeorderdata = $user->changeorder()->with(['job','item'])->where('job_id','=',$jobId)->get();
				
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
				'message' => 'user is not valid. please contact to the admin.',
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

public function singleChangeOrderdetails(Request $request){
  $oder_id = $request->order_id;
	$user = Auth::user();
	try{
		if($user){
			
			//$changeorder_details = $user->changeorder()->with(['job','item'])->where('id','=',$request->id)->get();
			$changeorder_details = Changeorder::with(['job','user','user.meta'=> function($q) {
                        $q->where('key','=','Business_name');
                    },'item'])->where('id','=',$oder_id)->get();
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
				'message' => 'User is not valid. please contact to the admin.',
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

public function UpdateChangeOrder(Request $request){
	$validator = Validator::make($request->all(), 
	[ 
		'id' => 'required',
		'status' => 'required',	  
	]);

	if ($validator->fails()) 
	{  
		return redirect()->back()->withErrors($validator);
	} 
	//dd($request->all());
	$user = Auth::user();
	
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
					if (strpos($request->digital_sign, 'data:image/jpeg;base64,') === 0) {
						$digitalsign_image = str_replace("data:image/jpeg;base64,", '', $request->digital_sign);
						$digitalsign = str_replace(" ","+",$digitalsign_image);
						$digitalsign_img = time() . '.jpeg';
						file_put_contents($digitalsign_img, base64_decode($digitalsign));
						$changeorder->digital_sign = $digitalsign_img;
					}
					if (strpos($request->digital_sign, 'data:image/png;base64,') === 0) {

						$digitalsign_image = str_replace("data:image/png;base64,", '', $request->digital_sign);
						$digitalsign = str_replace(" ","+",$digitalsign_image);
						$digitalsign_img = time() . '.png';
						file_put_contents($digitalsign_img, base64_decode($digitalsign));
						$changeorder->digital_sign = $digitalsign_img;
					}
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
					
					return redirect()->back()->withSuccess('Change Order Status has been updated successfully.'); 
				} 
				else 
				{
					return redirect()->back()->withErrors('Unable To Update Change Order Status. Please Try Again or Contact To Admin.');
				} 
			}
			else
			{
				return redirect()->back()->withErrors('You can not update this Change Order.');
			}
		}
		else
		{
			
			return redirect()->back()->withErrors('user is not valid. please contact to the admin.');
		}
	}
	catch (\Exception $e) 
	{
		return redirect()->back()->withErrors( $e->getMessage());
	}


}
public function addChangeOrder(Request $request){
	
	$validator = Validator::make($request->all(), 
        [ 
          
           'job_id' => 'required',
           'clientId' => 'required',
           'date' => 'required',
           'title' => 'required'
		  
        ]);

		if ($validator->fails()) 
		{  
			return redirect()->back()->withErrors($validator);
		} 
		
		$user = Auth::user();
		try
        {
            if($user)
            {
				//dd($request->all());
                $changeorder = $user->changeorder()->make();
                $changeorder->title = $request->title;
                $changeorder->job_id = $request->job_id;
				
				$clientIDS = $request->clientId;
				//dd($clientIDS);
				
				foreach(@$clientIDS as $clientidsss){
					
						$client_ids[] = $clientidsss;
						
						if(@$clientidsss){
							
							$contacts = Contact::where('id', '=', $clientidsss)->whereNotNull('contact_user_id')->first();
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
				
				$changeorddate = Carbon::createFromFormat('M d, Y', $request->date);
				$changeorderdate = $changeorddate->format('Y-m-d');

				$changeorder->client_id = serialize(@$client_ids);
                $changeorder->date = $changeorderdate;
				$changeorder->receiptNo = $request->receiptNo;
                $changeorder->status = 'New';
                $changeorder->save();
			
			   
				if($request->ItemAll){
					$items  = $request->ItemAll;
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
		
				return redirect()->back()->withSuccess('Your New ChangeOrder has been Saved Successfully.'); 	
            }
            else
            {
				return redirect()->back()->withErrors('User is not valid. please contact to the admin.');
            }
        }
        catch (\Exception $e) 
        {
			return redirect()->back()->withErrors($e->getMessage());
        }

}


// Events OR My Appointments
public function AddEvent(Request $request){
		
    $validator = Validator::make($request->all(), [
	   'title'=>'required',
	   'startdate'=>'required',
	   'enddate'=>'required',
	   'notification_alert'=>'required'
   ]);
   
   if ($validator->fails()) 
	{  
		return redirect()->back()->withErrors($validator);
	}  

	$user = Auth::user();

	//dd($request->all());
   if($user){
	   
	   $events = Events::make();
	   $events->user_id = $user->id;
	   $events->title =$request->title;
	   $events->description =$request->description;
	   $startd = Carbon::createFromFormat('M d, Y, g:i A', $request->startdate);
	   $startdate = $startd->format('Y-m-d H:i:s'); 
	   $events->startdate = $startdate;
	   $endd = Carbon::createFromFormat('M d, Y, g:i A', $request->enddate);
	   $enddate = $endd->format('Y-m-d H:i:s');
	   $events->enddate = $enddate;
	   $events->notification_alert = $request->notification_alert;
	   $events->status = 1;
	   $events->save();
	   
	   return redirect()->back()->withSuccess("You have successfully added Event.");
   }else{
	 
	   return back()->withErrors('User is not valid. please contact to the admin.');
   }
   
}	
public function getEvents(Request $request){
	$user = Auth::user();

	$events = Events::where('user_id',$user->id)->where('status',1)->get();

		$today = Carbon::today(); // Get today's date
		//echo "hellooo->>>".$today;
		$Todaysevents = Events::where('user_id', $user->id)
						->where('status', 1)
						->whereDate('startdate', '<=', $today)
						->whereDate('enddate', '>=', $today)
						->get();
	//dd($Todaysevents);

	return view('user/myappointment',compact('events','Todaysevents'));
}

public function GetEventsBydate(Request $request){
	$event_date = $request->event_date; // This is in 'Y-m-dTH:i:s' format
    $user = Auth::user();

    if ($user) {
        // Convert the event date to a Carbon instance if needed
        $eventDateTime = Carbon::parse($event_date); // Parse to Carbon object

        // You can also get just the date part if needed:
        $eventDateOnly = $eventDateTime->toDateString(); // '2024-08-22'

        $Events = Events::where('user_id', $user->id)
            ->where('status', 1)
            ->whereRaw('? BETWEEN startdate AND enddate', [$event_date]) // Check if event date is between startdate and enddate
            ->orderBy('status', 'asc')  // Order by status
            ->orderBy('title', 'asc')   // Order by title
            ->get();

        return response()->json(['success' => true, 'Events' => $Events]);    
    }else{
		   return response()->json([
			   'success' => false, 
			   'message' => 'Unable to find data .Please try again or contact to admin.',
		   ]);
	   }
}

public function editevent(Request $request){
	$validator = Validator::make($request->all(), 
	[ 
	   'id' => 'required',
	   'title' => 'required',
	   'startdate' => 'required',
	   'enddate' => 'required'
	   
	]);

	if ($validator->fails()) 
	{  
		return redirect()->back()->withErrors($validator);
	}  

	$user = Auth::user();
	if($user){
		/* $startdate = Carbon::createFromFormat('Y-m-d H:i:s', $request->startdate);
		$enddate = Carbon::createFromFormat('Y-m-d H:i:s', $request->enddate); */
		
		$events = Events::findorfail($request->id);
		$events->title =$request->title;
		if($request->description){
			$events->description = $request->description;
		}
		if($request->startdate){
			
			$startd = Carbon::createFromFormat('M d, Y, g:i A', $request->startdate);
			$startdate = $startd->format('Y-m-d H:i:s'); 
			
			$events->startdate = $startdate;
		}
		if($request->enddate){
			$endd = Carbon::createFromFormat('M d, Y, g:i A', $request->enddate);
			$enddate = $endd->format('Y-m-d H:i:s');
			$events->enddate = $enddate;
		}
		if($request->notification_alert){
			$events->notification_alert = $request->notification_alert;
		}
		$events->save();
		return redirect()->back()->withSuccess("You have successfully updated Event.");
	}else{
		return back()->withErrors('User is not valid. please contact to the admin.');
	}
	

}
public function deleteEvent(Request $request){
	$validator = Validator::make($request->all(), [
		   'id'	=> 'required'
	   ]);
	   
	   if ($validator->fails()) 
	   {
		   return response()->json(['error' => $validator->messages()], 200);
	   }
	   $user = Auth::user();
	   if($user){
		   
		   $events = Events::findorfail($request->id);
		   $events->delete();
		   return response()->json(['success' => true, 'message' => 'You have successfully Deleted Event.']);
	   }else{
		   return response()->json([
				   'success' => false,
				   'message' => 'User is not valid. please contact to the admin.',
			   ]);
	   }
   
}


// Todo list

public function GetTodoList(Request $request){
	$user = Auth::user();
	$allsections = General_todo_section::with('general_todo_task')->where('user_id','=',$user->id)->get(); 
	//dd($allsections);
	return view('user/todolist',compact('allsections'));
}

public function AddGenTodoSection(Request $request){

	$validator = Validator::make($request->all(), 
    [ 
       'sec_name' => 'required', 
       
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
	$user = Auth::user();
    if($user){
        
            $todosection = $user->general_todo_section()->make();
            $todosection->sec_name = $request->sec_name;
            $todosection->status = '1';
            $todosection->save();
            
			return response()->json(['section' => $todosection]);
    }else{

        return response()->json([
            'success' => false,
            'message' => 'user is not valid. please contact to the admin.',
        ]);

    }

}
public function UpdateToDoGeneralSection(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'id' =>'required'
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
    $user = $user = Auth::user();
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
                    'message' => 'User is not valid. please contact to the admin.',
                ]);

             }
    }else{
        return response()->json([
            'success' => false,
            'message' => 'User is not valid. please contact to the admin.',
        ]);
    } 

}

public function DeleteToDoGeneralSection(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'id' =>'required'
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
	
    $user = Auth::user();
    if($user){
        $Todosection = General_todo_section::where('id','=',$request->id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'You have successfully deleted the to do section task.'
        ]);

    }else{

        return response()->json([
            'success' => false,
            'message' => 'User is not valid. please contact to the admin.',
        ]);

    }
}

// Add general to do task

public function Addgentodotask(Request $request){

    $validator = Validator::make($request->all(), 
    [ 
     
       'task_name' =>'required',
       'todosec_id' =>'required',
       
    ]);
    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
	$user = Auth::user();

    if($user){
		$enddatedate='';
       $todosect =  General_todo_task::make();
       $todosect->task_name = $request->task_name;
       $todosect->todosec_id = $request->todosec_id;
       $todosect->description = $request->description;
	   if($request->enddate){
	   $enddate = Carbon::createFromFormat('M d, Y', $request->enddate);;
	    $enddatedate = $enddate->format('Y-m-d');
	   }else{
		$enddatedate= Carbon::now();
	   }
       $todosect->enddate = $enddatedate;
       $todosect->status = 0;
       $todosect->save();
		return response()->json(['task' => $todosect]);

    }else{

        return response()->json([
            'success' => false,
            'message' => 'user is not valid. please contact to the admin.',
        ]);
    }

}

public function updategentodoTaskStatus(Request $request){
  
    $validator = Validator::make($request->all(), 
    [ 
       'todotask_id' =>'required',
       'status' =>'required'
    ]);
    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 

	$user = Auth::user();

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
            'message' => 'User is not valid. please contact to the admin.',
        ]);
    }


}
public function deletegentodoTask(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'todotask_id' =>'required',
       
    ]);
    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
	$user = Auth::user();
    if($user){
        $Todosectiontask = General_todo_task::where('id','=',$request->todotask_id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'You have successfully deleted the to do general task.'
        ]);

    }else{

        return response()->json([
            'success' => false,
            'message' => 'User is not valid. please contact to the admin.',
        ]);

    }

}
public function updategentodoTask(Request $request){
  
    $validator = Validator::make($request->all(), 
    [ 
       'todotask_id' =>'required',
    ]);
	if ($validator->fails()) 
	{  
		return redirect()->back()->withErrors($validator);
	} 

	$user = Auth::user();

    if($user){
       $todosect =  General_todo_task::findorfail($request->todotask_id);
       if($todosect){
            if($request->task_name){
                 $todosect->task_name = $request->task_name;
            }
            if(@$request->todosec_id){
                $todosect->todosec_id = $request->todosec_id;
            }
            if(@$request->taskorder){
                 $todosect->taskorder = $request->taskorder;
            }
            if($request->description){
                $todosect->description = $request->description;
            }
            if($request->enddate){
			$enddate = Carbon::createFromFormat('M d, Y', $request->enddate);;
			$enddatedate = $enddate->format('Y-m-d');
             $todosect->enddate = $enddatedate;
            }
            if($request->status){
                $todosect->status = $request->status;
            }
            $todosect->save();
           
			return redirect()->back()->withSuccess("You have successfully updated todosection task.");
       }else{
        
		return redirect()->back()->withErrors("Todo section task not found");
     }
    }else{

		return redirect()->back()->withErrors("User is not valid. please contact to the admin.");
    }


}

//TimeCards details

public function GetTimeCard(Request $request){

	$user = Auth::user();
		$searchTerm = $request->input('search'); 
	
		$query = $user->contact()->where('type', '=', '3');
		if ($searchTerm) {
			$query->where('name', 'like', '%' . $searchTerm . '%'); 
		}
		// Execute the query
		$getemployees = $query->get();

		return view('user/timecard', compact('getemployees'));

}

public function getemployeetimecard($id){
	$employee_id= $id;
	$user = Auth::user();
	$user_id = $user->id;
	$getjob = $user->jobcontact()->with(['job'])->where('contact_id','=',$employee_id)->get();
	//dd($getjob);
	$contact = Contact::where('id','=',$employee_id)->first();
	
	return view('user/singletimecard',compact('getjob','employee_id','contact'));
}

Public function GetTimeCardsInfo(Request $request){
	$validator = Validator::make($request->all(), [
			'from_date'=>'required',
			'to_date' =>'required'
        ]);
        
		if ($validator->fails()) 
		{
			return response()->json(['error' => $validator->messages()], 200);
		}
       

		$fromda = Carbon::createFromFormat('M d, Y', $request->from_date);
		$from_date = $fromda->format('Y-m-d');

		$toda = Carbon::createFromFormat('M d, Y', $request->to_date);
		$to_date = $toda->format('Y-m-d');
	
	$user = Auth::user();
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
			// $clockdetails = Clocktime::where('user_id','=',$contact_user)
			// 		->whereBetween('tdate', [$from_date, $to_date])
			// 		->orderBy('id', 'asc')
			// 		->get();
		
			$clockdetails = Clocktime::where('user_id','=',$contact_user)
					->whereBetween('tdate', [$from_date, $to_date])
					->orderBy('id', 'asc')
					->get();

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
				
			  $clockdet = $user->clocktime()->where('job_id','=',$request->jobid)->whereBetween('tdate',[$from_date,$to_date])->get();
			
			  if(count($clockdet)>0){
				$clockdetails = $user->clocktime()->where('job_id','=',$request->jobid)->where('user_id','=',$contact_user)->whereBetween('tdate',[$from_date,$to_date])->orderBy('id', 'asc')->get();  
			  }else{
				$clockdetails = Clocktime::where('job_id','=',$request->jobid)->where('user_id','=',$contact_user)->whereBetween('tdate',[$from_date,$to_date])->orderBy('id', 'asc')->get();
			
			  }
			 foreach($clockdetails as $clockdetail){
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
public function Singletimecarddetails(Request $request){

	$contactId = $request->contact_id;
    $tdate = $request->tdate;

	$user = Auth::user();

	try
        {
		$totalSeconds =0;
		$contact = Contact::where('id','=',$request->jobcontact_id)->first();
		$jobcontact = Jobcontacts::where('contact_id','=',$request->jobcontact_id)->first();
		$job_id = $jobcontact->job_id;
		$jobdetails = Job::where('id','=',$job_id)->first();
		$job_name = $jobdetails->name;
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

				$timesheet = $clockdetails; 
				$totalhours = $htotal;

			} catch (\Exception $e) 
			{
				return redirect()->back()->withErrors($e->getMessage());
		}

		//dd($timesheet);

	return view('user/singletimecarddetails',compact('timesheet','totalhours','job_name'));

}
public function EditClockinClockout(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'id' => 'required',
			'clockin' => 'required',
			'clockout' => 'required'
        ]);

        if ($validator->fails()) 
        {
			return redirect()->back()->withErrors($validator);
        }

		$user = Auth::user();

        try
        {
            if($user)
            {
					//dd($checktime);
                    $clocktime = Clocktime::findorfail($request->id);
                    $clocktime->clockin = $request->clockin;                    
					$clocktime->clockout = $request->clockout;
                    $clocktime->save();
					return redirect()->back()->withSuccess("Clockin-Clockout updated Successfully!");
              
            }
            else
            {
        
				return redirect()->back()->withErrors("User is not valid. please contact to the admin.");
            }
        }
        catch (\Exception $e) 
        {
          
			return redirect()->back()->withErrors($e->getMessage());	
        }
    }


// ClockIn Functions
public function Getclockin(){
	$user = Auth::user();
	if($user){
		$user_id = $user->id;
		$contact_id = $user->contactuserid()->where('contact_user_id','=',$user_id)->get();
		
			$cont_id = $contact_id[0]->id;
			$jobdata = job::with(['user', 'jobstage','jobmedia','jobcontact','jobinspection','jobstage.stage','jobmedia.media','jobcontact.contact','jobinspection.contact','contact','punchlist','punchlist.punchlistimg'])->whereHas('jobcontact', function($q) use($cont_id) {$q->where('contact_id', '=', $cont_id); })->where('status','!=',2)->paginate(10);
	}else{
		
		return redirect()->back()->withErrors("User is not valid. please contact to the admin.");
		
	}
	$clocktimeId='';
	$clockin_jobid='';
	$data =array();
	if(session('clocktime_id') && session('clockin_jobid')){
		$tday_date = Carbon::now()->format('Y-m-d');
		$clocktimeId = session('clocktime_id');
		$clockin_jobid = session('clockin_jobid');
		$results = $user->clocktime()->where('tdate','=',$tday_date)->where('job_id','=',$clockin_jobid)->orderBy('id', 'desc')->first();
		
		 if($results){
			 $timeclocks = $user->clocktime()->where('tdate','=',$tday_date)->where('job_id','=',$clockin_jobid)->get();
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
				
			}else{
				$data['totalhours']='0 hrs 0 min';
				$data['status'] ='Clock In';  
				$data['id']=0;
				
			}

			$data['job_id']= $clockin_jobid;
		}

	return view('user/clockin',compact('jobdata','data'));
} 


public function GetAllClocks(Request $request){
	$validator = Validator::make($request->all(), [
			'job_id' => 'required',
			'from_date'=>'required',
			'to_date' =>'required'
        ]);
        
	if ($validator->fails()) 
	{
		return response()->json(['error' => $validator->messages()], 200);
	}
	$user = Auth::user();
		$fromda = Carbon::createFromFormat('M d, Y', $request->from_date);
		$from_date = $fromda->format('Y-m-d');

		$toda = Carbon::createFromFormat('M d, Y', $request->to_date);
		$to_date = $toda->format('Y-m-d');
	 try
        {
		  $clockdetails = $user->clocktime()->where('job_id','=',$request->job_id)->whereBetween('tdate',[$from_date,$to_date])->groupBy('tdate')->orderBy('id','DESC')->get();
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

public function gettimesheetdetails(Request $request){
	   $user = Auth::user();
	    $totalSeconds =0;
		$clockdetails = Clocktime::where('tdate','=',$request->tdate)->where('job_id','=',$request->job_id)->get();
		
		$jobdetails = Job::where('id','=',$request->job_id)->first();

		foreach($clockdetails as $clockdetail){
			
			if($clockdetail->clockout=== null){
				$clockoutt =  date('H:i:s', time());
				$startTime = Carbon::parse($clockdetail->tdate.' '.$clockdetail->clockin);
				$endTime = Carbon::parse($clockdetail->tdate.' '.$clockoutt);
				$dsecond = $endTime->diffInSeconds($startTime);
				$totalSeconds += @$diffsecond;
						
			}
			else{
				$startTime = Carbon::parse($clockdetail->tdate.' '.$clockdetail->clockin);
				$endTime = Carbon::parse($clockdetail->tdate.' '.$clockdetail->clockout);
				$diffsecond = $endTime->diffInSeconds($startTime);
				$totalSeconds += @$diffsecond;
			}
		}
		 $day = floor($totalSeconds / 86400);
		 $totalhours = floor(($totalSeconds -($day*86400)) / 3600).' hrs '.floor(($totalSeconds / 60) % 60).' min';
		 
		//  return response()->json(
        //     [
        //         'success' => true,
        //         'tsheetdetail' => $clockdetails,
        //         'totalhours' => $htotal,
        //     ]);
//dd($clockdetails);
	return view('user/timesheetdetail',compact('clockdetails','totalhours','jobdetails'));
}

// clockin function

Public function addClockin(Request $request){
	$validator = Validator::make($request->all(), [
				'tdate' =>'required',
				'job_id' =>'required'	
			]);
			
		if ($validator->fails()) 
		{  
			return redirect()->back()->withErrors($validator);
		}  
	
		$user = Auth::user();
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

						session(['clocktime_id' => $clocktime->id,'clockin_jobid'=>$request->job_id]);
						$startOfMonth = Carbon::now()->startOfMonth();
						$endOfMonth = Carbon::now()->endOfMonth();

						$clockdetails = $user->clocktime()
							->where('job_id', '=', $request->job_id)
							->whereBetween('tdate', [$startOfMonth, $endOfMonth])
							->groupBy('tdate')
							->orderBy('id', 'DESC')
							->get();
						//dd($clockdetails);
						session(['clockdetails' => $clockdetails]);
    					return redirect()->back()->withSuccess('Clock-in successful!');
						//  return response()->json([
						// 	'success' => true,
						// 	'clockin_id'=>$clocktime->id,
						// 	'message' => 'You have successfully clockedin.',
						// ]);
						
					}else{
						
						return redirect()->back()->withErrors("User is not valid. please contact to the admin.");
						
					}
				   
		}else{
			return redirect()->back()->withErrors("User is not valid. please contact to the admin.");
		}
}


// Clock out function
Public function updateClockout(Request $request){
	//dd($request->all());
	$validator = Validator::make($request->all(), [
				'id' =>'required',
				'job_id' =>'required',
				'injoyed'=>'required',
				
				
			]);
			
		if ($validator->fails()) 
		{  
			return redirect()->back()->withErrors($validator);
		}  
	
		$user = Auth::user();
		
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
					session()->forget(['clocktime_id', 'clockin_jobid']);
					session()->forget('clockdetails');

				 
					return redirect()->back()->withSuccess('You have successfully clockout.');
		}else{
			return redirect()->back()->withErrors("User is not valid. please contact to the admin.");
		}
}

// User profile update
public function Updateprofile(Request $request){
	$user = Auth::user();
	$currentgateway = $this->checkUserSubscription($user);
	$selectedplans = $user->selectedPlan()->orderBy('id', 'desc')->first();
	
	return view('user/updateprofile', compact('user','currentgateway','selectedplans'));
}
 public function PostUpdateprofile(Request $request){
	//dd($request->all());
	$user = Auth::user();

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
		// if($request->profile_pic)
		// {
		// 	$frontimage = str_replace("data:image/jpeg;base64,", '', $request->profile_pic);
		// 	$frontimage1 = str_replace(" ","+",$frontimage);

		// 	$profile_pic = time() . '.jpeg';
		// 	file_put_contents($profile_pic, base64_decode($frontimage1));
		// 	$user->profile_pic = $profile_pic;
		// }
		if ($request->profile_pic) {
			$user->name = $request->name;
		}
		if ($request->profile_pic) {
			$file = $request->file('profile_pic');
			$filename = ((string)(microtime(true) * 10000)) . "-" . $file->getClientOriginalName();
			$destinationPath = public_path('/');
			
			$file->move($destinationPath, $filename);
			$user->profile_pic = $filename;
		}

		if($request->mobile)
		{
			$user->updateMeta('Mobile' , $request->mobile);
		}
		
		$user->save();
		return redirect()->back()->withSuccess('Profile updated successfully.');
	} 
	else 
	{
		return redirect()->back()->withErrors("User is not valid. please contact to the admin.");
	}
 }

//Privacy Policy
public function PrivacyPolicy(){
	return view('user/privacypolicy');
}

// Leads functions here

 public function leads(Request $request){

	$user = Auth::user();
	$leadsQuery = Lead::where('user_id', $user->id)->where('status', 1);

		if ($request->has('search') && $request->search !== '') {
			$leadsQuery->where('deal_name', 'like', '%' . $request->search . '%');
		}

	$leads = $leadsQuery->get();

	return view('user/leads', compact('leads'));
 }

 // Add Leads

public function addLead(Request $request){
	$validator = Validator::make($request->all(), [
		'deal_name' =>'required',
		'name' =>'required'	
	]);
		
	if ($validator->fails()) 
	{  
		return redirect()->back()->withErrors($validator);
	}  
//dd($request->all());
	$user = Auth::user();
		if($user){
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
			return redirect()->back()->withSuccess('Your Lead has been Saved Successfully.');
		}else{

			return redirect()->back()->withErrors("User is not valid. please contact to the admin.");

		}
  }

// Lead details function
public function leadDetails($id){
	$lead_id = $id;
	$user = Auth::user();
	$lead = Lead::where('id', $lead_id)->where('user_id', $user->id)->first();
	$All_contacts = $user->contact()->with(['contactshared'])->get();
	//dd($lead);
	$allsections = Todosection::with('todosectiontask')->where('user_id','=',$user->id)->where('lead_id','=',$lead_id)->get(); 
	
	//dd($allsections);
	
	return view('user/leaddetail', compact('lead','All_contacts','allsections'));	
}

// Update Lead 

public function updateLead(Request $request){
	$validator = Validator::make($request->all(), [
		'lead_id' =>'required',
	]);
		
	if ($validator->fails()) 
	{  
		return redirect()->back()->withErrors($validator);
	}  
//dd($request->all());
	$user = Auth::user();
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

		return redirect()->back()->with([
			'success' => 'Your Lead has been updated successfully.',
			'activeTab' => 'tab2' // Replace 'tabName' with the name of the tab you want to keep active
		]);

		}else{

			return redirect()->back()->withErrors("User is not valid. please contact to the admin.");

		}
  }
public function deleteLead(Request $request){

		$validator = Validator::make($request->all(), [
			'lead_id' =>'required',
		]);
			
		if ($validator->fails()) 
		{  
			return redirect()->back()->withErrors($validator);
		}  
		$user = Auth::user();
		if($user){

		$lead = Lead::where('user_id','=',$user->id)->where('id','=',$request->lead_id)->delete();

		return redirect()->route('leads')->withSuccess('Your Lead has been deleted Successfully.');

		}else{
			return redirect()->back()->withErrors("User is not valid. please contact to the admin.");
		}
	
}

  // Convert lead in to job
public function convertleadtojob(Request $request){
	//dd($request->all());
    $validator = Validator::make($request->all(), 
       [ 
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
		   return redirect()->back()->withErrors($validator);
	   }  
	   $user = Auth::user();
	   $current_user_subscription = $this->checkUserSubscription($user);
	   $active_subscription = $current_user_subscription['subscription_status'] ;
       try
       {
           if($user)
           {
			 
			if($active_subscription =='active'){
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
           
           return redirect()->back()->with([
				'success' => 'Your  Job has been Saved Successfully.',
				'activeTab' => 'tab2' // Replace 'tabName' with the name of the tab you want to keep active
			]);
			}else{
				return redirect()->back()->withErrors("You have no active plan. Please purchase the plan.");
			}

           }else{
			return redirect()->back()->withErrors("User is not valid. please contact to the admin.");
           }
       }catch (\Exception $e) 
       {
		   return redirect()->back()->withErrors($e->getMessage());
       }
}

// Add To Do section 

public function AddTodosection(Request $request){
	$validator = Validator::make($request->all(), 
    [ 
       'sec_name' => 'required', 
	   
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
	$user = Auth::user();
    if($user){
        
		$todosection = $user->todosection()->make();
		$todosection->sec_name = $request->sec_name;
		if($request->job_id){
			$todosection->job_id = $request->job_id;
		}else{
			$todosection->lead_id = $request->lead_id;
		}
		
		$todosection->status = '1';
		$todosection->save();
            
			return response()->json(['section' => $todosection]);
    }else{

        return response()->json([
            'success' => false,
            'message' => 'user is not valid. please contact to the admin.',
        ]);

    }	
    
}
public function updateTodosection(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
       'id' =>'required'
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
	$user = Auth::user();
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
                    'message' => 'User is not valid. please contact to the admin.',
                ]);

             }
    }else{
        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);
    } 

}

Public function deleteTodosection(Request $request){
	$validator = Validator::make($request->all(), 
    [ 
       'id' =>'required'
    ]);

    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
	
    $user = Auth::user();
    if($user){
        $Todosection = Todosection::where('id','=',$request->id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'You have successfully deleted the to do section task.'
        ]);

    }else{

        return response()->json([
            'success' => false,
            'message' => 'User is not valid. please contact to the admin.',
        ]);

    }

}
//Add TODO SECTION TASK

public function AddtodoSectiontask(Request $request){

    $validator = Validator::make($request->all(), 
    [ 
       'task_name' =>'required',
       'todosec_id' =>'required',
       
    ]);
    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    }

	$user = Auth::user();
    if($user){
		$enddatedate='';
       $todosect =  Todosectiontask::make();
       $todosect->task_name = $request->task_name;
       $todosect->todosec_id = $request->todosec_id;
       $todosect->description = $request->description;
	   if($request->enddate){
	   	$enddate = Carbon::createFromFormat('M d, Y', $request->enddate);
	        $enddatedate = $enddate->format('Y-m-d');
		}else{
			$enddatedate= Carbon::now();
		}
       $todosect->enddate = $enddatedate;
       $todosect->status = 0;
       $todosect->save();
       return response()->json(['task' => $todosect]);
	}else{

        return response()->json([
            'success' => false,
            'message' => 'Token is not valid. please contact to the admin.',
        ]);
    }

}


// Update to do section task

public function updatetodoSectiontask(Request $request){
	
	$validator = Validator::make($request->all(), 
    [ 
       'todosectask_id' =>'required',
    ]);
	if ($validator->fails()) 
	{  
		return redirect()->back()->withErrors($validator);
	} 

	$user = Auth::user();
   //dd($request->all());
	if($user){
		$todosect =  Todosectiontask::findorfail($request->todosectask_id);
		

		if($todosect){
			 if($request->task_name){
				  $todosect->task_name = $request->task_name;
			 }
			 //$todosect->todosec_id = $request->todosec_id;
			 if($request->taskorder){
				  $todosect->taskorder = $request->taskorder;
			 }
			 if($request->description){
				 $todosect->description = $request->description;
			 }
			 if($request->enddate){
				$enddate = Carbon::createFromFormat('M d, Y', $request->enddate);;
				$enddatedate = $enddate->format('Y-m-d');
			  	$todosect->enddate = $enddatedate;
			 }
			 if($request->status){
				 $todosect->status = $request->status;
			 }
			 $todosect->save();
			 return redirect()->back()->withSuccess("You have successfully updated todosection task.");
		}else{
		
		 return redirect()->back()->withErrors("You have successfully updated todosection task.");
	  }
	 }else{

		 return redirect()->back()->withErrors("User is not valid. please contact to the admin.");
	 }

}

public function updatetodoTaskStatus(Request $request){
  
    $validator = Validator::make($request->all(), 
    [ 
      
       'todosectask_id' =>'required',
       'status' =>'required'
    ]);
    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
   
	$user = Auth::user();
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

public function deletetodoTask(Request $request){
    $validator = Validator::make($request->all(), 
    [ 
      
       'todosectask_id' =>'required',
       
    ]);
    if ($validator->fails()) 
    {  
        return response()->json(['error'=>$validator->errors()]); 
    } 
	$user = Auth::user();
    if($user){
        $Todosectiontask = Todosectiontask::where('id','=',$request->todosectask_id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'You have successfully deleted the to do section task.'
        ]);

    }else{

        return response()->json([
            'success' => false,
            'message' => 'User is not valid. please contact to the admin.',
        ]);

    }

}

// Web FCM token
public function webFcmToken(Request $request){
	$user = Auth::user();
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
// Get all Notifications

public function webNotification(){

	$user = Auth::user();
	$notifications = Notification::where('client_id', $user->id)->orderBy('created_at', 'desc')->paginate(10);
	return view('user/webnotification',compact('notifications'));
}

public function webNotificationstsus(Request $request){
	$validator = Validator::make($request->all(), [
		'id' => 'required',
		'status' => 'required'

	]);
	if ($validator->fails()) {
		return response()->json(['error' => $validator->messages()], 200);
	}

	$user = Auth::user();
	if ($user) {
		$notification = Notification::find($request->id);
		$notification->status = $request->status;
		$notification->save();
		return response()->json(['success' => true,  'message' => 'Notification Update Successfully.']);
	} else {
		return response()->json(['success' => false, 'message' => 'Unauthorized user.Please try again or contact to admin.']);
	}
}

// plans 
Public function plans(){
	$user = Auth::user();
	$Plans = Plan::where('status',1)->get();
	$SelectedPlan = SelectedPlan::where('user_id',$user->id)->first();
	$current_user_subscription = $this->checkUserSubscription($user);
	
	return view('user/plan', compact('Plans','SelectedPlan','current_user_subscription'));
}


Public function purchasePlan(Request $request){

	$validator = Validator::make($request->all(), 
	[ 
	   
	   'stripeToken' => 'required',
	   'selected_plan'=>'required'
	]);

	if ($validator->fails()) 
	{  
		return redirect()->back()->withErrors($validator);
	} 
	
//dd($request->all());
	$user = Auth::user();

	if($user){
		$user_id = $user->id;
		$Plans = Plan::where('id',$request->selected_plan)->first();
		$plan_amount = $Plans->price;
		$cleanPrice = str_replace('$', '', $Plans->price);
		if($request->selected_plan==3){
			    $selectedPlan = $user->selectedPlan()->make();
				$selectedPlan->user_id = $user->id; 
				$selectedPlan->plan_id = $request->selected_plan;
				$selectedPlan->status = 1;
				$selectedPlan->start_date = Carbon::now();
				$selectedPlan->end_date = Carbon::now()->addDays(60);
				$selectedPlan->save();
		}else{

		
		Stripe\Stripe::setApiKey(config('app.stripe_secret'));
			try {

				$customer = Stripe\Customer::create(array(
					'email' => $user->email,
					'source'  => $request->stripeToken
				));
				$stripe_customer_id = $customer->id;
			  } catch (Exception $e) {
                    $api_error = $e->getMessage();
                }
				
			if (empty($api_error) && $stripe_customer_id) {
				try {

					$interval = '';

					switch($request->selected_plan) {
						case 1:
							$interval = 'month'; // Monthly plan
							break;
						case 2:
							$interval = 'year';  // Yearly plan
							break;
						default:
							$interval = ''; // Invalid plan or other cases
							break;
					}

					$priceCents = $cleanPrice * 100;
					$plan = \Stripe\Plan::create(array(
						"product" => [
							"name" => $Plans->name
						],
						"amount" => $priceCents,
						"currency" => 'USD',
						"interval" => $interval,
						
					));
				} catch (Exception $e) {
					$api_error = $e->getMessage();
				}

				if (empty($api_error) && $plan){
					try {

						$subscription = \Stripe\Subscription::create(array(
							"customer" => $stripe_customer_id,
							"items" => array(
								array(
									"plan" => $plan->id,
								),
							),
						));
						} catch (Exception $e) {
						$api_error = $e->getMessage();
				     }
					 if (empty($api_error) && $subscription) {
						/* record save in payment table */
						if($subscription->status=='active'){
							$subscr_status = 1;
						}else{
							$subscr_status = 0;
						}
						$customerpay = Payment::make();
						$customerpay->user_id = $user_id;
						$customerpay->amount = $cleanPrice;
						$customerpay->status = 1;
						$customerpay->transaction_id = '';
						$customerpay->subscription_status = $subscr_status;
						$customerpay->subscription_id = $subscription->id;
						$customerpay->payment_date = date('Y-m-d H:i:s', $subscription->created);
						
						$customerpay->save();
						/* record save in payment table */
					// Save value in selected plan table
					$selectedplans = $user->selectedPlan()->orderBy('id', 'desc')->first();
					if($selectedplans){
							if($selectedplans->plan_id != 3 ){
								$existingsubscription = \Stripe\Subscription::retrieve($selectedplans->subscription_id);
								if ($existingsubscription->status === 'active') {
									$existingsubscription->cancel();
								}
							}
						    $selectedpln = SelectedPlan::where('user_id','=',$user_id)->orderBy('id', 'desc')->first();
							 $selectedpln->start_date =  date('Y-m-d', $subscription->current_period_start);
							 $selectedpln->end_date = date('Y-m-d', $subscription->current_period_end);
							 $selectedpln->subscription_status = $subscr_status; 
							 $selectedpln->subscription_id = $subscription->id;
							 $selectedpln->plan_id = $request->selected_plan;
							 $selectedpln->status = 1;		
							 $selectedpln->receipt	='';								 
							 $selectedpln->purchase_token = '';
							 $selectedpln->save();
					}else{
							$selectedPlan = SelectedPlan::make();
							$selectedPlan->user_id = $user_id;
							$selectedPlan->plan_id = $request->selected_plan;
							$selectedPlan->start_date = date('Y-m-d', $subscription->current_period_start);
							$selectedPlan->end_date = date('Y-m-d', $subscription->current_period_end);
							$selectedPlan->status =  1;
							$selectedPlan->purchase_token = $purchaseToken;
							$selectedPlan->subscription_id =  $subscription->id;
							$selectedPlan->receipt='';
							$selectedPlan->subscription_status = $subscr_status;
							$selectedPlan->save();	
					}
					 }else{
						return redirect()->back()->withErrors("Something went wrong.Please try later or contact to support.");
					 }
				}else{
					return redirect()->back()->withErrors("Something went wrong.Please try later or contact to support.");	
				}
			}else{	
			    return redirect()->back()->withErrors("Something went wrong.Please try later or contact to support.");
			}
	    }	
		return redirect()->back()->withSuccess('You have successfully purchased '.$Plans->name.' plan'); 		   
	}else{
			return redirect()->back()->withErrors('User is not valid. please contact to the admin.');
	}

}
// Cancel Subscription

public function Cancelstripeplan(Request $request){

	$user = Auth::user();
	$selectedplans = $user->selectedPlan()->where('plan_id','=',$request->plan_id)->orderBy('id', 'desc')->first();
	Stripe\Stripe::setApiKey(config('app.stripe_secret'));
	$existingsubscription = \Stripe\Subscription::retrieve($selectedplans->subscription_id);
	if ($existingsubscription->status === 'active') {
		$existingsubscription->cancel();
		$selectedpln = SelectedPlan::where('user_id','=',$user_id)->orderBy('id', 'desc')->first();
	
		$selectedpln->end_date = date('Y-m-d', $subscription->current_period_end);
		
		$selectedpln->status = 0;		
		$selectedpln->receipt	='';								 
		$selectedpln->purchase_token = '';
		$selectedpln->save();
		return redirect()->back()->withSuccess('You have successfully canceled your plan');

	}else{
		$selectedpln = SelectedPlan::where('user_id','=',$user_id)->orderBy('id', 'desc')->first();
		$selectedpln->end_date = date('Y-m-d', $subscription->current_period_end);
		$selectedpln->status = 0;		
		$selectedpln->receipt	='';								 
		$selectedpln->purchase_token = '';
		$selectedpln->save();
		return redirect()->back()->withSuccess('You have successfully canceled your plan');
	}

}
// current user subscriptions

function checkUserSubscription($user)
{
    
	$user_plan = SelectedPlan:: where('user_id',$user->id)->orderBy('id', 'desc')->first(); 
	$response = [
        'subscription_status' => 'notactive',
		'payment_gateway'=>'nogateway'  
    ];
	if($user_plan){
		$payment_gateway = $this->identifyPaymentGateway($user_plan->subscription_id);
		if($payment_gateway=='stripe'){
			Stripe\Stripe::setApiKey(config('app.stripe_secret_test'));
			$existingsubscription = \Stripe\Subscription::retrieve($user_plan->subscription_id);
			if ($existingsubscription->status === 'active') {
				$response = [
					'subscription_status' => 'active',
					'payment_gateway'=>'stripe'  
				];
			}else{
				$response = [
					'subscription_status' => 'notactive',
					'payment_gateway'=>'stripe'  
				];
			}


		}
		if($payment_gateway=='googleplay'){

				if($user_plan->plan_id==1){
					$productId = 'plan_monthly';
				}elseif($user_plan->plan_id==2){
					$productId = 'plan_yearly';
				}
				//$slectedPlans->subscription_id;
				
				$client = new Google_Client();
				$client->setApplicationName('seejobrun');
				$client->setAuthConfig(storage_path('app/google_play_credentials.json'));
				$client->setScopes([Google_Service_AndroidPublisher::ANDROIDPUBLISHER]);
				
				
				$androidPublisher = new Google_Service_AndroidPublisher($client);
				$packageName = 'com.clockk';
				
				try {
					$subscription = $androidPublisher->purchases_subscriptions->get($packageName, $productId, $user_plan->purchase_token);
				
					// Determine subscription status
					$isActive = isset($subscription['paymentState']) && $subscription['paymentState'] == 1 
								&& empty($subscription['cancelReason']);
				
					$response = [
						'subscription_status' => $isActive ? 'active' : 'notactive',
						'payment_gateway' => 'googleplay',
					];
				
				
				} catch (Exception $e) {
					$response = [
						'subscription_status' => 'notactive',
						'payment_gateway' => 'googleplay',
						
					];
				
					
				}
					


		}
		if($payment_gateway=='itune'){
			$receipt= $user_plan->receipt ;

			try {
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://buy.itunes.apple.com/verifyReceipt', 
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS => json_encode([
						'receipt-data' => $receipt,
						'password' => 'd664e45baee94e9c8fbbbae38205129e',
						'exclude-old-transactions' => true,
					]),
					CURLOPT_HTTPHEADER => array(
						'Content-Type: application/json',
					),
				));
				$response = curl_exec($curl);
				curl_close($curl);
			
				$responseData = json_decode($response);
			
				// Validate the response
				if ($responseData->status === 0) {
					$latestReceiptInfo = $responseData->latest_receipt_info;
					$expiresTimestamp = strtotime($latestReceiptInfo[0]->expires_date);
					$currentTimestamp = time();
			
					$isActive = $expiresTimestamp > $currentTimestamp;
			
					// Set response based on active status
					$response = [
						'subscription_status' => $isActive ? 'active' : 'notactive',
						'payment_gateway' => 'itune',
					];
			
					// Update plan in the database
					// $slectedPlans = SelectedPlan::where('user_id', '=', $user->id)->orderBy('id', 'desc')->first();
					// $slectedPlans->status = $isActive ? 0 : 1;
					// $slectedPlans->subscription_status = $isActive ? 0 : 1;
					// $slectedPlans->save();
			
					// // Include updated plan in the response
					// $response['data'] = $slectedPlans;
			
					
				} else {
					// Handle failed validation
					$response = [
						'subscription_status' => 'notactive',
						'payment_gateway' => 'itune',
					];
					
				}
			} catch (Exception $e) {
				$response = [
					'subscription_status' => 'notactive',
					'payment_gateway' => 'itune',
				];
				// Handle exceptions
				
			}
			

		}

	}else{
		$response = [
			'subscription_status' => 'notactive',
			'payment_gateway'=>'nogateway'  
		];

	}

	return $response;
}

// identify payment gate way
function identifyPaymentGateway($subscriptionId) {
    if (str_starts_with($subscriptionId, 'GP-')) {
        return 'googleplay';
    } elseif (str_starts_with($subscriptionId, 'sub_')){
        return 'stripe';
    }else {
        return 'itune';
    }
}

// User type
public function user_type($type)
{
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

 // Send notification function 
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

        // Save notification to the database
        $saveNotification = new Notification();
        $saveNotification->user_id = $user_id;
        $saveNotification->title = $msgdata['title'];
        $saveNotification->body = $msgdata['body'];
        $saveNotification->type = $msgdata['type'];
        $saveNotification->client_id = $client_id;
        $saveNotification->save();

        // Prepare the message for FCM v1 API
        $data = [
            "message" => [
                "token" => $regfcm->fcmtoken,
                "data" => $msgdata,
                "notification" => [
                    'title' => $msgdata['title'],
                    'body' => $msgdata['body'],
                ],
                "android" => [
                    "notification" => [
                        "sound" => "default"
                    ]
                ],
                "apns" => [
                    "payload" => [
                        "aps" => [
                            "sound" => "default"
                        ]
                    ]
                ]
            ]
        ];

        $dataString = json_encode($data);

        // Set headers with the OAuth access token
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/seejobrun/messages:send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        // Execute the request and capture the response
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            return response()->json(['success' => false, 'message' => $error_msg]);
        } else {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_code == 404 && strpos($response, '"errorCode": "UNREGISTERED"') !== false) {
                $regfcm->delete();  // Delete unregistered token
            }
        }

        // Close cURL connection
        curl_close($ch);
    }
}

 
 public function sendNotification_old($user_id, $msgdata = array())
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

}
