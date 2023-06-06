<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Session\SessionManager;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class StartSession extends \Illuminate\Session\Middleware\StartSession
{
    public function __construct(SessionManager $manager, callable $cacheFactoryResolver = null)
    {
        parent::__construct($manager, $cacheFactoryResolver);
    }

    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure(Request): (Response) $next
     * @return Response
     */
    public function handle($request, Closure $next): Response
    {
        if (!$this->sessionConfigured()) {
            return $next($request);
        }

        $session = $this->getSession($request);

        if ($this->manager->shouldBlock() ||
            ($request->route() instanceof Route && $request->route()->locksFor())) {
            return $this->handleRequestWhileBlocking($request, $session, $next);
        }


        return $this->handleStatefulRequest($request, $session, $next);
    }

    /**
     * Handle the given request within session state.
     *
     * @param Request $request
     * @param Session $session
     * @param Closure $next
     * @return mixed
     */
    protected function handleStatefulRequest(Request $request, $session, Closure $next): mixed
    {
        // If a session driver has been configured, we will need to start the session here
        // so that the data is ready for an application. Note that the Laravel sessions
        // do not make use of PHP "native" sessions in any way since they are crappy.
        $request->setLaravelSession(
            $this->startSession($request, $session)
        );

        $this->collectGarbage($session);

        $response = $next($request);

        $this->storeCurrentUrl($request, $session);

        $this->addCookieToResponse($response, $session);
        $this->addUserSessionCookieToResponse($request, $response, $session);


        // Again, if the session has been configured we will need to close out the session
        // so that the attributes may be persisted to some storage medium. We will also
        // add the session identifier cookie to the application response headers now.
        $this->saveSession($request);

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param Session $session
     * @return void
     */
    protected function addUserSessionCookieToResponse(Request $request, Response $response, Session $session): void
    {

        if ($this->sessionIsPersistent($this->manager->getSessionConfig()) && $request->user()) {
            $payload = [
                'iss' => env('APP_URL'),
                'sub' => $request->user()->uuid,
                'iat' => time(),
                'exp' => time() + $this->getSessionLifetimeInSeconds()
            ];

            $response->headers->setCookie(new Cookie(
                'user_session', JWT::encode($payload, config('oauth.private_key'), 'RS256'), $this->getCookieExpirationDate(), null, env('SESSION_PARENT_DOMAIN')
            ));
        }
    }
}
