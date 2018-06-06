<?php
namespace App\Http\Middleware;
use Alert;
use Auth;
use Closure;

class HasRole
{
	public function handle($request, Closure $next, $role="",$redirect="home")
	{
		$role=$role?$role:explode(",",$role);
		if ($role && !Auth::user()->hasAnyRole($role))
		{
			 
			Alert::add('error', 'Access denied')->flash();
			return redirect($redirect);
		}

		return $next($request);
	}
}