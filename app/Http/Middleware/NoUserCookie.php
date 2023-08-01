<?php

namespace App\Http\Middleware;

use App\Contracts\Repository\UserRepositoryInterface;
use Closure;
use Illuminate\Http\Request;

class NoUserCookie
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $repository;

    public function __construct(
        UserRepositoryInterface $userRepository
    )
    {
        $this->repository = $userRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $cookie = $request->cookie('user');

        if ($cookie) {
            $intended = [
                'host' => urlencode($request->input('host') ?: urlencode(env('BASE_APP_URL'))),
                'resource' => urlencode($request->input('resource') ?: urlencode('/'))
            ];

            return redirect('/login?host=' . $intended['host'] . '&resource=' . $intended['resource']);
        }
        return $next($request);
    }
}
