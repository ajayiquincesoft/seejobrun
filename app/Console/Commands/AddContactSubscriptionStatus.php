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
use App\Models\Addcontactsubscription;
use Stripe;
class AddContactSubscriptionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:addcontactsubscriptionupdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        //return 0;
		
		$contacts = Contact::whereNotNull('subscription_id')
                   ->where('status', 1)
                   ->get();

					Stripe\Stripe::setApiKey(config('app.stripe_secret'));

					foreach ($contacts as $contact) {
						$subscription_id = $contact->subscription_id;
						try {
							$current_subscription = \Stripe\Subscription::retrieve($subscription_id);
							if ($current_subscription->status == 'active') {
								$contact_subscription_end_date = $contact->subscription_end;
								$end_date_timestamp = $current_subscription->current_period_end;
								$existing_subs_end_date = date('Y-m-d H:i:s', $end_date_timestamp);

								if ($contact_subscription_end_date <= $existing_subs_end_date) {
									// Update subscription_end date in the contacts table
									$contact->subscription_end = $existing_subs_end_date;
									$contact->subscription_status = 1;
									$contact->status = 1;
									$contact->save();
									$contactSubscription = Addcontactsubscription::where('contact_id', $contact->id)
                                                      ->where('subscription_id', $subscription_id)
                                                      ->first();
									 $contactSubscription->subscription_end_date = $existing_subs_end_date;
									 $contactSubscription->subscription_status = $current_subscription->status;
									 $contactSubscription->save();				  

									echo "Updated subscription end date for contact ID {$contact->id} to {$existing_subs_end_date}\n";
								}
							}else{
								$contact->subscription_end = $existing_subs_end_date;
								$contact->subscription_status = 2;
								$contact->status = 2;
								$contact->subscription_end_reason ='Reason for end subscription '. $current_subscription->status;
								$contact->save();
								$contactSubscription = Addcontactsubscription::where('contact_id', $contact->id)
                                                      ->where('subscription_id', $subscription_id)
                                                      ->first();
								 $contactSubscription->subscription_end_date = $existing_subs_end_date;
								 $contactSubscription->subscription_status = 2;
								 $contactSubscription->save();
							}
						} catch (\Exception $e) {
							echo "Error retrieving subscription for contact ID {$contact->id}: " . $e->getMessage() . "\n";
						}
					}
						
    }
}
