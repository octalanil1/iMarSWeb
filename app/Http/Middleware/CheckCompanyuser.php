<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Session;
use view;
class CheckCompanyuser
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
		if (Auth::user()->user_type=="company") {
		return $next($request);	
        }else{	
		Session::put('msg', '<strong class="alert alert-danger">You have to login with Comapny Account.</strong>');	
            return redirect('/myaccount');
		}
        
    }
	
}
