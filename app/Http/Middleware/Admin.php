<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
      /*  if (! Auth::check()) {
        return redirect()->route('login');
      } */

        if( Auth::guard()->user()->user_type == 1){
           return $next($request);   
        }
		Auth::logout();
		return redirect()->route('user-login');
    }
}
