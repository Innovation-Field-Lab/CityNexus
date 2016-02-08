<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Contracts\Auth\Guard;

class Authenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            if ($request->ajax()) {
                if ($request->getUri() == '/api-query/add-to') {
                    return $next($request);
                } else {
                    return response('Unauthorized.', 401);
                }
            } else {

                if(User::count() == null)
                {
                    return view('auth.register');
                } else {
                    return redirect()->guest('auth/login');
                }
            }
        }

        return $next($request);
    }
}
