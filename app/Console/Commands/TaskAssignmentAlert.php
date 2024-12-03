<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use App\Models\FcmToken;
use App\Models\Taskassignment;
use App\Models\Job;
use App\Models\Contact;
use App\Models\Notification;
use Google_Client;
use Google_Service_AndroidPublisher;
class TaskAssignmentAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:taskAssignmentalert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'taskAssignment alert notifications';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {   
		$currentD = Carbon::now();
		$taskAssignment = Taskassignment::where('enddate', '>=', $currentD)->get();
	
		//$currentDate = Carbon::now();
			foreach ($taskAssignment as $taskAssi) {
				$user_id = $taskAssi->user_id;
				
				$user = User::where('id','=',$user_id)->first();
					$currdate='';
					if(@$user){
						if(!empty($user->timezone)){
							$currentDate = Carbon::now()->timezone($user->timezone);
							$currdate = $currentDate->format('Y-m-d');
						}else{
							$currentDate = Carbon::now();
							$currdate = $currentDate->format('Y-m-d'); 
						}
					}
					
					$job_id = $taskAssi->job_id;
					$job_name = Job::where('id','=', $job_id)->first()->name;
					$contact_id = $taskAssi->contact_id;
					$task_title = $taskAssi->title;
					$contacts_user_id = Contact::where('id','=',$contact_id)->first()->contact_user_id;
					 
					$enddate = Carbon::parse($taskAssi->enddate);
					$formattedDate = $enddate->format('l M j, Y');
					$oneDayBefore = $enddate->copy()->subDay();
					$twoDaysBefore = $enddate->copy()->subDays(2);
					$currdate = Carbon::now();

					if ($currdate->lt($enddate)) {
						
						$oneday = Carbon::parse($oneDayBefore)->format('Y-m-d');
						$twoday = Carbon::parse($twoDaysBefore)->format('Y-m-d');
						$currDate = Carbon::parse($currdate)->format('Y-m-d');
						
						
						$msg["title"] = $task_title .' , '.$job_name .' starts '.$formattedDate;
						$msg["body"] = '';
						$msg['type'] = "TaskAssignmentAlert";
						$msg['client_id'] = '';
						$msg['user_type'] ='';
						$msg['move'] = 'Home';
						
						
						 if($twoday==$currDate){
							if(@$contacts_user_id){
							$this->sendNotification(@$contacts_user_id, $msg); 
							echo "TaskAssignment Alert sent successfully";
							}
						 }else{
							  if($oneday==$currDate){ 
							   if(@$contacts_user_id){
									$this->sendNotification(@$contacts_user_id, $msg);
								   echo "TaskAssignment Alert sent successfully";
							   }
							 }
						 } 
						
						
					} else {
						//echo "Not sent successfully"; 
					}
			}

    }


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
	
	public function sendNotification_old($user_id, $msgdata = array())
	{		
			
			$SERVER_API_KEY = 'AAAApzVhLyU:APA91bHHGMW5eOGHIOSPekapwfsew2XhbRjTb_oD2KsQpUvuEbRe1bYA6itGLjFimsP532Z58zKJYEyqTQnEfKCcj8AorrOzbfwQ2qRMSkoIL57e-UYI_WwkROriNf4AWMDQvJME6yV5';
			
			$regfcm = FcmToken::whereUserId($user_id)->first();
		
			if(@$regfcm){
				$msgdata['sound'] = 'default';
				$data = [
					"registration_ids" => array($regfcm->fcmtoken),
					"data" => $msgdata,
					"notification" => $msgdata
				]; 
			
				/* $saveNotification = Notification::make();
				$saveNotification->user_id = $user_id;
				$saveNotification->title = $msgdata['title'];
				$saveNotification->body = $msgdata['body'];
				$saveNotification->type = $msgdata['type'];
				$saveNotification->client_id = 0;
				$saveNotification->save(); */
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
				
				echo "Task alert sent successfully";
			}
	}
}
