<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB; 
use Carbon\Carbon; 
use App\Models\User; 
use App\Models\Template;
use Mail; 
use Hash;

class ForgotPasswordController extends Controller
{
    public function showForgetPasswordForm()
	{
		return view('auth.passwords.email');
	}
	  
	public function submitForgetPasswordForm(Request $request)
	{
	  	$request->validate([
	      'email' => 'required|email|exists:users',
	  	]);

	  	//$checkurser = User::where('user_type','=','1')->where('email','=',$request->email)->first();
		$checkurser = User::where('email','=',$request->email)->first();

	  	if($checkurser)
	  	{
		  	$token = Str::random(64);

		  	DB::table('password_resets')->insert([
		      'email' => $request->email, 
		      'token' => $token, 
		      'created_at' => Carbon::now()
		    ]);

		  	$from_email = getenv('MAIL_FROM_ADDRESS');
		    $subject = "Reset Password";
		    $email = $request->email;
		    $name = "";

	        $body = @Template::where('type', 4)->orderBy('id', 'DESC')->first()->content;
	        $content = array('token' => $token);
	        foreach ($content as $key => $parameter) 
	        {
	            $body = str_replace('{{' . $key . '}}', $parameter, $body);
	        }
			try{
				Mail::send('emails.name', ['template' => $body, 'token' => $token], function ($m) use ($email,$from_email, $subject) 
				{
					$m->from($from_email, 'See Job Run');
					$m->to($email)->subject($subject);
				});
			}catch (\Exception $e) {
				// Log the error but do not show it to the user
				//Log::error('Error sending email: ' . $e->getMessage());
			}
			$checkurser1 = User::where('user_type','=','1')->where('email','=',$request->email)->first();
				if($checkurser1){
					return redirect('/login')->with('message', 'We have e-mailed your password reset link!');
				}else{
					return redirect('/user-login')->with('message', 'We have e-mailed your password reset link!');
				}
		  	//return back()->with('message', 'We have e-mailed your password reset link!');
		}
		else
		{
			return back()->with('error', 'This email id is not exist in our system.');
		}
	}
      
    public function showResetPasswordForm($token) 
    { 
        return view('auth.passwords.reset', ['token' => $token]);
    }
	  
    public function submitResetPasswordForm(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);
  
        $updatePassword = DB::table('password_resets')->where([
                                'email' => $request->email, 
                                'token' => $request->token
                              ])->first();
  
        if(!$updatePassword)
        {
            return back()->withInput()->with('error', 'Invalid token!');
        }
  
        $user = User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
 
        DB::table('password_resets')->where(['email'=> $request->email])->delete();
		$checkurser = User::where('user_type','=','1')->where('email','=',$request->email)->first();
		if($checkurser){
       		 return redirect('/login')->with('message', 'Your password has been changed!');
		}else{
			return redirect('/user-login')->with('message', 'Your password has been changed!');
		}
    }
}
