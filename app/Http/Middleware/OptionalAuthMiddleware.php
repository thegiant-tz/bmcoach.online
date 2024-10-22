<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OptionalAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        // Temporarily disable token requirement to check if a token is provided
        if ($request->bearerToken()) {
            // Attempt to authenticate the user using the provided token
            $this->authenticate($request);
        }


        

        return $next($request);
    }

    protected function authenticate($request)
    {
        // Attempt to authenticate the user
        try {
            $guard = Auth::guard('api');
            if ($guard->check()) {
                Auth::setUser($guard->user());
            }
        } catch (\Exception $e) {
            // Handle exceptions if needed
        }
    }
}
