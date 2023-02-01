<?php

namespace App\Services\OAuth;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Str;
use Laravel\Passport\Bridge\User;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Exceptions\OAuthServerException;
use Laravel\Passport\Http\Controllers\HandlesOAuthErrors;
use Laravel\Passport\Passport;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Psr\Http\Message\ServerRequestInterface;

class ClientValidationService
{
    use HandlesOAuthErrors;

    /**
     * @var AuthorizationServer
     */
    private AuthorizationServer $server;

    /**
     * @var ResponseFactory
     */
    private ResponseFactory $response;

    /**
     * @var StatefulGuard
     */
    private StatefulGuard $guard;

    public function __construct(
        AuthorizationServer $server,
        ResponseFactory     $response,
        StatefulGuard       $guard)
    {
        $this->server = $server;
        $this->response = $response;
        $this->guard = $guard;
    }

    /**
     * Authorize a client to access the user's account.
     *
     * @param ServerRequestInterface $psrRequest
     * @param Request $request
     * @param ClientRepository $clients
     * @param TokenRepository $tokens
     * @return Response|JsonResponse
     * @throws AuthenticationException
     * @throws OAuthServerException
     */
    public function handle(ServerRequestInterface $psrRequest,
                           Request                $request,
                           ClientRepository       $clients,
                           TokenRepository        $tokens): Response|JsonResponse
    {
        $authRequest = $this->withErrorHandling(function () use ($psrRequest) {
            return $this->server->validateAuthorizationRequest($psrRequest);
        });

        if ($this->guard->guest()) {
            return $request->get('prompt') === 'none'
                ? $this->denyRequest($authRequest)
                : $this->promptForLogin($request);
        }

        if ($request->get('prompt') === 'login' &&
            !$request->session()->get('promptedForLogin', false)) {
            $this->guard->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $this->promptForLogin($request);
        }

        $request->session()->forget('promptedForLogin');

        $scopes = $this->parseScopes($authRequest);
        $user = $this->guard->user();
        $client = $clients->find($authRequest->getClient()->getIdentifier());

        if ($request->get('prompt') !== 'consent' &&
            ($client->skipsAuthorization() || $this->hasValidToken($tokens, $user, $client, $scopes))) {
            return $this->approveRequest($authRequest, $user);
        }

        if ($request->get('prompt') === 'none') {
            return $this->denyRequest($authRequest, $user);
        }

        $request->session()->put('authToken', $authToken = Str::random());
        $request->session()->put('authRequest', $authRequest);

        return new JsonResponse([
            'data' => [
                'client' => $client,
                'user' => $user,
                'scopes' => $scopes,
                'request' => $request,
                'authToken' => $authToken,
            ]
        ]);
    }

    /**
     * Transform the authorization request's scopes into Scope instances.
     *
     * @param AuthorizationRequest $authRequest
     * @return array
     */
    protected function parseScopes(AuthorizationRequest $authRequest): array
    {
        return Passport::scopesFor(
            collect($authRequest->getScopes())->map(function ($scope) {
                return $scope->getIdentifier();
            })->unique()->all()
        );
    }

    /**
     * Determine if a valid token exists for the given user, client, and scopes.
     *
     * @param TokenRepository $tokens
     * @param Authenticatable $user
     * @param Client $client
     * @param array $scopes
     * @return bool
     */
    protected function hasValidToken(TokenRepository $tokens, Authenticatable $user, Client $client, array $scopes): bool
    {
        $token = $tokens->findValidToken($user, $client);

        return $token && $token->scopes === collect($scopes)->pluck('id')->all();
    }

    /**
     * Approve the authorization request.
     *
     * @param AuthorizationRequest $authRequest
     * @param Authenticatable $user
     * @return Response
     * @throws OAuthServerException
     */
    protected function approveRequest(AuthorizationRequest $authRequest, $user): Response
    {
        $authRequest->setUser(new User($user->getAuthIdentifier()));

        $authRequest->setAuthorizationApproved(true);

        return $this->withErrorHandling(function () use ($authRequest) {
            return $this->convertResponse(
                $this->server->completeAuthorizationRequest($authRequest, new Psr7Response)
            );
        });
    }

    /**
     * Deny the authorization request.
     *
     * @param AuthorizationRequest $authRequest
     * @param Authenticatable|null $user
     * @return Response
     * @throws OAuthServerException
     */
    protected function denyRequest(AuthorizationRequest $authRequest, $user = null): Response
    {
        if (is_null($user)) {
            $uri = $authRequest->getRedirectUri()
                ?? (is_array($authRequest->getClient()->getRedirectUri())
                    ? $authRequest->getClient()->getRedirectUri()[0]
                    : $authRequest->getClient()->getRedirectUri());

            $separator = $authRequest->getGrantTypeId() === 'implicit' ? '#' : '?';

            $uri = $uri . (str_contains($uri, $separator) ? '&' : $separator) . 'state=' . $authRequest->getState();

            return $this->withErrorHandling(function () use ($uri) {
                throw OAuthServerException::accessDenied('Unauthenticated', $uri);
            });
        }

        $authRequest->setUser(new User($user->getAuthIdentifier()));

        $authRequest->setAuthorizationApproved(false);

        return $this->withErrorHandling(function () use ($authRequest) {
            return $this->convertResponse(
                $this->server->completeAuthorizationRequest($authRequest, new Psr7Response)
            );
        });
    }

    /**
     * Prompt the user to login by throwing an AuthenticationException.
     *
     * @param Request $request
     *
     * @throws AuthenticationException
     */
    protected function promptForLogin(Request $request)
    {
        $request->session()->put('promptedForLogin', true);

        throw new AuthenticationException;
    }
}
