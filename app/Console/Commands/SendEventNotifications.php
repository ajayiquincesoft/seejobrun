<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Notifications\EventReminder;
use App\Models\Events;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use App\Models\FcmToken;
use App\Models\Notification;
use Google_Client;
use Google_Service_AndroidPublisher;
class SendEventNotifications extends Command
{
    protected $signature = 'send:event-notifications';
    protected $description = 'Send event notifications';

    public function handle()
    {
		
        // Event Start
        $events = Events::where('status','=',1)->get();
        foreach ($events as $event) {
			$user_id = $event->user_id;
			$user = User::where('id','=',$user_id)->first();
			$currdate='';
			if(@$user){
				if(!empty($user->timezone)){
					$currentDate = Carbon::now()->timezone($user->timezone);
					$currdate = $currentDate->format('Y-m-d H:i');


				}else{
					$currentDate = Carbon::now();
					$currdate = $currentDate->format('Y-m-d H:i:s'); 
				}
			}
			
            $start = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($event->startdate)));
            $end = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($event->enddate)));
			
	
			$interval = CarbonInterval::day();
            $period = CarbonPeriod::create($start, '1 day', $end);
				
				 foreach ($period as $date) {
						$startDate = Carbon::parse($event->startdate);
						$newStartDate = $startDate->subMinutes($event->notification_alert);
						$alert_datetime = $newStartDate->format('Y-m-d H:i:s');
					
						$currdates = strtotime($currdate);
						$alert_datetime = strtotime($alert_datetime);
						$time_difference = round(($alert_datetime - $currdates) / 60);
						
						if($time_difference==0){
							$msg["title"] = $event->title ." Starts in ".$event->notification_alert." minutes";
							$msg["body"] = '';
							$msg['type'] = "Event";
							$msg['client_id'] = '';
							$msg['user_type'] ='';
							$msg['move'] = 'Home';
							
							$this->sendNotification($user_id, $msg);
						}
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
	
	// public function sendNotification($user_id, $msgdata = array())
	// {		
			
	// 		$SERVER_API_KEY = 'AAAApzVhLyU:APA91bHHGMW5eOGHIOSPekapwfsew2XhbRjTb_oD2KsQpUvuEbRe1bYA6itGLjFimsP532Z58zKJYEyqTQnEfKCcj8AorrOzbfwQ2qRMSkoIL57e-UYI_WwkROriNf4AWMDQvJME6yV5';
			
	// 		$regfcm = FcmToken::whereUserId($user_id)->first();
			
	// 		if(@$regfcm){
	// 			$msgdata['sound'] = 'default';
	// 			$data = [
	// 				"registration_ids" => array($regfcm->fcmtoken),
	// 				"data" => $msgdata,
	// 				"notification" => $msgdata
	// 			]; 
			
	// 			/* $saveNotification = Notification::make();
	// 			$saveNotification->user_id = $user_id;
	// 			$saveNotification->title = $msgdata['title'];
	// 			$saveNotification->body = $msgdata['body'];
	// 			$saveNotification->type = $msgdata['type'];
	// 			$saveNotification->client_id = 0;
	// 			$saveNotification->save(); */
	// 			$dataString = json_encode($data);

	// 			$headers = [
	// 				'Authorization: key=' . $SERVER_API_KEY,
	// 				'Content-Type: application/json',
	// 			];

	// 			$ch = curl_init();

	// 			curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
	// 			curl_setopt($ch, CURLOPT_POST, true);
	// 			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	// 			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	// 			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// 			curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

	// 			$response = curl_exec($ch);
				
	// 			echo "Notification sent successfully";
	// 		}
	// }
}
