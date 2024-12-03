<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;
use Auth;
use App\Models\SelectedPlan;
use App\Models\User;
use JWTAuth;

class userplancheck
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
        try 
        {
            //$user = auth('api')->authenticate();

            $user = JWTAuth::parseToken()->authenticate();
            if($user)
            {
                $slectedPlans = $user->selectedPlan()->orderBy('id', 'desc')->first();

                //dd($slectedPlans);
                if ($slectedPlans->end_date > Carbon::now()) 
                {
                    return $next($request);
                }
                else
                {
                    return response()->json(
                    [
                        'success' => false,
                        'message' =>  'Your Plan Has been Expired.',
                    ]);
                }
            }
        } 
        catch (Exception $e) 
        {
            return response()->json(['success' => false,'message' => $e->getMessage()]);
        }
    }
}
