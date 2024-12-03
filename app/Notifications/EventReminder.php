<?php

namespace App\Notifications;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Template;
use App\Mail\TestMail;
use Mail;

class EventReminder extends Notification
{
    use Queueable;

    protected $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {						
							$event_user_id = $this->event->user_id;
							
							$user_details = User::where('id','=',$event_user_id)->first();
							$event_title = $this->event->title;
							$event_description = $this->event->description;
							$event_notification_alert = $this->event->notification_alert;
	
	
							$from_email = getenv('MAIL_FROM_ADDRESS');
							$email =$contacts->email;
							$name = $contacts->name;
							$subject = "New job added";

							$body = @Template::where('type', 7)->orderBy('id', 'DESC')->first()->content;
							
							$content = array('name' => $name,'time'=>$event_notification_alert,'event_title'=>$event_title,'event_description'=>$event_description);
							foreach ($content as $key => $parameter) 
							{
								$body = str_replace('{{' . $key . '}}', $parameter, $body);
							}

							Mail::send('emails.name', ['template' => $body, 'name' => $name,'time'=>$event_notification_alert,'event_title'=>$event_title,'event_description'=>$event_description], function ($m) use ($from_email, $email, $name, $subject) 
							{
								$m->from($from_email, 'See Job Run');

								$m->to($email, $name)->subject($subject);
							});
							
    }
}
