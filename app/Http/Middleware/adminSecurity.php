<?php

namespace App\Http\Middleware;

use Closure;
use Session,Auth,Redirect;

class adminSecurity
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
        //echo 1; exit;
        $user = Auth::user();
		if (!Auth::check() || $user->is_admin!='1') {

            return Redirect::to('/admin/login');

        }

      
        return $next($request);
		
    }
}
