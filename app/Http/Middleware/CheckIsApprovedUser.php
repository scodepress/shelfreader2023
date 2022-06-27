<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckIsApprovedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
	    if($request->user()->privs === 3 && $request->user()->approved === 0)
	    { 
		return Inertia::render('Welcome'); 

	    } else {

		    return $next($request);
	    }
    }
}
