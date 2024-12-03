<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
	
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
	 protected $commands = [
        Commands\SendEventNotifications::class,
    ];
	
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
		$schedule->command('send:event-notifications')->everyMinute();
		$schedule->command('command:clockouttimeupdate')->everyMinute();
		$schedule->command('command:taskAssignmentalert')->daily();
		$schedule->command('command:addcontactsubscriptionupdate')->daily('06:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
