<?php

namespace App\Http\Controllers\OAuth;

use App\Services\OAuth\ClientValidationService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Exceptions\OAuthServerException;
use Laravel\Passport\TokenRepository;
use Psr\Http\Message\ServerRequestInterface;

class AuthorizationController
{
    /**
     * @var ClientValidationService
     */
    private ClientValidationService $validationService;

    public function __construct(
        ClientValidationService $validationService
    )
    {
        $this->validationService = $validationService;
    }


    /**
     * Validate OAuth authorization request
     *
     * @param ServerRequestInterface $psrRequest
     * @param Request $request
     * @param ClientRepository $clients
     * @param TokenRepository $tokens
     * @return Response|JsonResponse
     * @throws AuthenticationException
     * @throws OAuthServerException
     */
    public function authorize(ServerRequestInterface $psrRequest,
                              Request                $request,
                              ClientRepository       $clients,
                              TokenRepository        $tokens): Response|JsonResponse
    {
        return $this->validationService->handle($psrRequest, $request, $clients, $tokens);
    }
}
