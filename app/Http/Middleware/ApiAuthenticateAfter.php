<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class ApiAuthenticateAfter
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
        $response = $next($request);

        // Perform action

        // log out the user if logged in
        Auth::logout();

        return $response;
    }
}
