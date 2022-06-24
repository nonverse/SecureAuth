<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NoUserCookie
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
        if ($request->cookie('user')) {
            $intended = [
                'host' => urlencode($request->input('host') ?: urlencode(env('BASE_APP_URL'))),
                'resource' => urlencode($request->input('resource')  ?: urlencode('/'))
            ];

            return redirect('/login?host=' . $intended['host'] . '&resource=' . $intended['resource']);
        }
        return $next($request);
    }
}
