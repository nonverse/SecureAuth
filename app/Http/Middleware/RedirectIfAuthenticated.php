<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @param string|null ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if ($request->has('args') && str_contains($request->input('args'), 'ignore_session')) {
            return $next($request);
        }
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if ($request->input('host')) {
                    return redirect('https://' . $request->input('host') . $request->input('resource') ? $request->input('resource') : '/');
                }

                return redirect(env('VITE_ACCOUNT_APP'));
            }
        }

        return $next($request);
    }
}
