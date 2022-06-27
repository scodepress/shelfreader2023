<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckIsLocalAdminMiddleware
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
	    if($request->user()->privs === 1 || $request->user()->privs === 2)
	    { 
		    return $next($request);
	    } else {
		    return redirect()->route('shelf');
	    }
    }
}
