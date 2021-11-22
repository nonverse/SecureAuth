<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Auth\AbstractAuthenticationController;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $authController = new AbstractAuthenticationController;
                $intended = $authController->retrieveIntended($request);
                return redirect('http://' . $intended['host'] . $intended['resource']);
            }
        }

        return $next($request);
    }
}
