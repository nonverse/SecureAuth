<?php

namespace App\Http\Controllers\OAuth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Passport\Exceptions\InvalidAuthTokenException;
use Laravel\Passport\Exceptions\OAuthServerException;
use Laravel\Passport\Http\Controllers\HandlesOAuthErrors;
use Laravel\Passport\Http\Controllers\RetrievesAuthRequestFromSession;
use League\OAuth2\Server\AuthorizationServer;
use Nyholm\Psr7\Response as Psr7Response;

class ApproveAuthorizationController extends AbstractOAuthController
{
    use HandlesOAuthErrors, RetrievesAuthRequestFromSession;

    /**
     * The authorization server.
     *
     * @var AuthorizationServer
     */
    protected AuthorizationServer $server;

    /**
     * Create a new controller instance.
     *
     * @param AuthorizationServer $server
     * @return void
     */
    public function __construct(AuthorizationServer $server)
    {
        $this->server = $server;
    }

    /**
     * Approve the authorization request.
     *
     * @param Request $request
     * @return mixed
     * @throws InvalidAuthTokenException
     * @throws OAuthServerException
     * @throws \Exception
     */
    public function approve(Request $request)
    {
        $this->assertValidAuthToken($request);

        $authRequest = $this->getAuthRequestFromSession($request);

        $authRequest->setAuthorizationApproved(true);

        $response = $this->withErrorHandling(function () use ($authRequest) {
            return $this->convertResponseToJson(
                $this->server->completeAuthorizationRequest($authRequest, new Psr7Response)
            );
        });

        return new JsonResponse([
            'data' => [
                ...$response
            ]
        ]);
    }
}
