<?php



namespace App\Http\Middleware;



use Closure;

use Auth;

use Session;

class Checkuser

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
        $user = Auth::user();
		if (!Auth::check() || $user->is_admin!='0') {

            return redirect('/signin');

        }

        return $next($request);

    }

	

}

