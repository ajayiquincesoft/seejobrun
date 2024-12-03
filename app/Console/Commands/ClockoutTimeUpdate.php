<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User;
use App\Models\Clocktime;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class ClockoutTimeUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:clockouttimeupdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clock Out Time Update';

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
		
	$todayDate = Carbon::now()->format('Y-m-d');
	 $clocktimes = Clocktime::whereNotNull('clockin')->get();	
		if($clocktimes){
			$clockInid = [];
			$clockdate =[];
			$totalSeconds = 0;
			foreach($clocktimes as $clocktime){
				
				$user_id = $clocktime->user_id;
				$clocktime->user_id;
				$clocktime->tdate;
				$clocktime->clockin;
				$clocktime->clockout;
				
				$user = User::where('id','=',$user_id)->first();
				
				$currdate='';
				if(@$user){
					$userTimeZone = $user->timezone;
					if(!empty($user->timezone)){
						
						$currentDate = Carbon::now()->timezone($user->timezone);
						$currdate = $currentDate->format('Y-m-d H:i:s');
						date_default_timezone_set($user->timezone);

					}else{
						$currentDate = Carbon::now();
						$currdate = $currentDate->format('Y-m-d H:i:s'); 
					}
					
					
				}
				
				$today = $currentDate->format('Y-m-d');
			
				if($today==$clocktime->tdate){
					$firstClocktime = $clocktime->first();
					$clockdate = $clocktime->tdate;
					if($clocktime->clockout === null) {	
						$clockInid = $clocktime->id;
						$clockoutt =  date('H:i:s', time());
						$startTime = Carbon::parse($clocktime->tdate.' '.$clocktime->clockin);
						$endTime = Carbon::parse($clocktime->tdate.' '.$clockoutt);
						$diffsecond = $endTime->diffInSeconds($startTime);
						$totalSeconds += $diffsecond; 
						
					}else{
						$startTime = Carbon::parse($clocktime->tdate.' '.$clocktime->clockin);
						$endTime = Carbon::parse($clocktime->tdate.' '.$clocktime->clockout);
						$diffsecond = $endTime->diffInSeconds($startTime);
						$totalSeconds += $diffsecond;
						
					}
					
				}
				
				/* $clockintime 		= Carbon::parse($clocktime->tdate.' '.$clocktime->clockin);
				$clockIn 			= Carbon::parse($clockintime);
				$clockOut 			= $clockIn->copy()->addHours(10);
				$clockOutTime 		= $clockOut->toDateTimeString();
				$minuteDifference 	= $currentDate->diffInMinutes($clockOut);
				$clockOutTime 		= $clockOut->toTimeString();
				
				
				
				if($minuteDifference==0){
					$clocktimedetail = Clocktime::findorfail($clocktime->id);
					$clocktimedetail->clockout = $clockOutTime;
					$clocktimedetail->clockstatus = 0;
					$clocktimedetail->save();
					echo "save successfully";	
				}else{
					echo "Not match";
				}  */
			}
			
			
		//echo 'second->>>'.$totalSeconds;
		
		/* $firsClockin = Clocktime::where('tdate','=',$today)->first();
		$firsClockinTime = $firsClockin->clockin;
		$clockintime 		= Carbon::parse($today.' '.$firsClockinTime);
		$clockOut 			= $clockintime->copy()->addHours(10);
		echo 'hour-->'.$clockOutTime = $clockOut->toDateTimeString(); */
		
		$totalClockInHours = floor($totalSeconds / 3600);
		$totalClockInMinutes = floor(($totalSeconds % 3600) / 60);
		$totalClockInSeconds = $totalSeconds % 60;
		 
		/* echo 'heyy'.$currentDateTime = \Carbon\Carbon::now();  
		$clockOut->subHours($totalClockInHours)->subMinutes($totalClockInMinutes)->subSeconds($totalClockInSeconds);
		echo $newDateTime = $clockOut->format('Y-m-d H:i:s'); */
			
		if($totalClockInHours==10 && $totalClockInMinutes < 1){
			$currdate = $currentDate->format('Y-m-d');
			if($clockInid && $clockdate==$currdate){
				$clockt = Clocktime::find($clockInid);
				$clockt->clockout = date('H:i:s', time());
				$clockt->save();
			}
		}else{
			$currentDateTime = Carbon::now(); // Get the current date and time
				// Set the time to 23:59:59
				
				$endOfDay = $currentDateTime->copy()->setTime(23, 59, 59);
				if ($currentDateTime->eq($endOfDay) && $clockInid) {
					$clockOuttIme =  date('H:i:s', time());
					$clockt = Clocktime::find($clockInid);
					$clockt->clockout = '23:59:59';
					$clockt->save();
				}
		}

		}
		
    }
	
}
