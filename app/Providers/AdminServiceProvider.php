<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */

    public const HOME = '/Admin/user/';

    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
	
	 protected function authenticated(Request $request, $user)
    {
        if ($user->user_type == 1) { // Assuming 1 is for admin
            return redirect(AdminServiceProvider::HOME); // Redirect to admin home
        }

        return redirect()->route('user.dashboard'); // Redirect to user dashboard
    }
}
