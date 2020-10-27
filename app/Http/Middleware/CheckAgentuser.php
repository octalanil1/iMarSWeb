<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Session;
use view;
class CheckAgentuser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		if (Auth::user()->user_type=="agent") {
		return $next($request);	
        }else{
		Session::put('msg', '<strong class="alert alert-danger">You have to login with Professional Account.</strong>');
            return redirect('/myaccount');
		}
        
    }
	
}
