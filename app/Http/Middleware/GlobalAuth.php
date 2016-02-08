<?php

namespace App\Http\Middleware;

use Closure;

class GlobalAuth
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

        if(!Auth::user())
        {

        }

        return $next($request);
    }
}
