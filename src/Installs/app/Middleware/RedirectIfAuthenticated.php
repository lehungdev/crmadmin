<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (Auth::guard($guard)->check()) {
            return redirect('/');
        }

        return $next($request);
    }
}