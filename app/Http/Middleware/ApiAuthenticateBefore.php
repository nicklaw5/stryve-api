<?php

namespace App\Http\Middleware;

use Auth;
use Larapi;
use Closure;
use App\Models\User;

class ApiAuthenticateBefore
{
    /**
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Instantiate a new instance
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
        $access_token = $request->headers->get('authorization');

        // return unauthorized if no token set
        if(!$access_token)
            return Larapi::respondUnauthorized();

        // attempt to find user from access token
        $request->user = $this->user->getUserByAccessToken($access_token);

        // if we didn't find the user return unauthorized
        if(!$request->user)
            return Larapi::respondUnauthorized();
            
        // login the user in    
        Auth::login($request->user);

        // continue with the request
        return $next($request);
    }
}
