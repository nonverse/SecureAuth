<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->cookie('user')) {
            $intended = [
                'host' => urlencode($request->input('host') ?: urlencode(env('VITE_ACCOUNT_APP'))),
                'resource' => urlencode($request->input('resource')  ?: urlencode('/'))
            ];

            return redirect('/?host=' . $intended['host'] . '&resource=' . $intended['resource']);
        }

        return $next($request);
    }
}
