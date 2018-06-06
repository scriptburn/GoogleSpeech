<?php
namespace App\ScriptBurn\Http\Middleware;
use Auth;
use Closure;

class HasRole
{
	public function handle($request, Closure $next, $role = "", $redirect = "")
	{
		
		$role = array_filter($role ? explode(";", $role) : []);
		$user=Auth::user();

		if(!$user)
		{
			return redirect ('/admin/login');
		}
		if ( !empty($role)   && !$user->hasRole($role))
		{
			if ($redirect)
			{
				$redirect = explode(";", $redirect);
				foreach ($redirect as $rule)
				{
					$rule = explode("::", $rule);

					if (substr($rule[0], 0, 1) == '@')
					{
						$rule[0] = route(substr($rule[0], 1));
					}

					if (isset($rule[1]))
					{
						if (Auth::user()->hasRole($rule[1]))
						{
							
							return redirect($rule[0]);
						}
					}
					else
					{
						return redirect($rule[0]);
					}
				}
			}
			else
			{
				return abort(404);
			}
		}

		return $next($request);
	}
}