<?php

namespace App\Providers;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Event;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
		Event::listen(Authenticated::class, function (Authenticated $event) {
            $user = $event->user;
            if ($user->timezone) {
                $timezone = $user->timezone;
                app('config')->set('app.timezone', $timezone);
				date_default_timezone_set($timezone);
            }
        });
    }
}
