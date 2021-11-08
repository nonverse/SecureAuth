<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireResetToken
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
        if (!$request->query('token') || !$request->query('email')) {
            return redirect('/forgot');
        }
        return $next($request);
    }
}
