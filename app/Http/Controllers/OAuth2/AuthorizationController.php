<?php

namespace App\Http\Controllers\OAuth2;

use App\Contracts\Repository\OAuth2\AuthCodeRepositoryInterface;
use App\Contracts\Repository\OAuth2\ClientRepositoryInterface;
use App\Contracts\Repository\OAuth2\RefreshTokenRepositoryInterface;
use App\Contracts\Repository\OAuth2\ScopeRepositoryInterface;
use App\Repositories\OAuth2\AccessTokenRepository;
use App\Services\OAuth\AuthCode\CreateAuthCodeService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorizationController extends AbstractOAuth2Controller
{
    /**
     * @var ClientRepositoryInterface
     */
    private ClientRepositoryInterface $clientRepository;

    /**
     * @var ScopeRepositoryInterface
     */
    private ScopeRepositoryInterface $scopeRepository;

    /**
     * @var RefreshTokenRepositoryInterface
     */
    private RefreshTokenRepositoryInterface $refreshTokenRepository;

    /**
     * @var AccessTokenRepository
     */
    private AccessTokenRepository $accessTokenRepository;

    /**
     * @var CreateAuthCodeService
     */
    private CreateAuthCodeService $createAuthCodeService;

    public function __construct(
        ClientRepositoryInterface       $clientRepository,
        ScopeRepositoryInterface        $scopeRepository,
        AuthCodeRepositoryInterface     $authCodeRepository,
        RefreshTokenRepositoryInterface $refreshTokenRepository,
        AccessTokenRepository           $accessTokenRepository,
        CreateAuthCodeService           $createAuthCodeService,
    )
    {
        $this->clientRepository = $clientRepository;
        $this->scopeRepository = $scopeRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->createAuthCodeService = $createAuthCodeService;
        parent::__construct($clientRepository, $scopeRepository, $authCodeRepository, $refreshTokenRepository);
    }

    /**
     * Return the data to be displayed on the authorization request page
     *
     * @throws Exception
     */
    public function show(Request $request): JsonResponse
    {

        if ($e = $this->validateAuthorizationRequest($request)) {
            return new JsonResponse([
                'data' => [
                    'message' => 'Client validation failed'
                ],
                'errors' => $e
            ], 401);
        }

        $client = $this->clientRepository->get($request->input('client_id'));

        /**
         * Automatically approve request if the user has previously authorized it and an access token was issued
         */
        if ($this->accessTokenRepository->getUsingClientAndUser($client->id, $request->user()->uuid)) {
            return $this->approve($request);
        }

        /**
         * Automatically approve request if application's redirect URI should skip the authorization prompt
         */
        if (in_array($request->input('redirect_uri'), config('oauth.skip_prompt'))) {
            return $this->approve($request);
        }

        return new JsonResponse([
            'data' => [
                'name' => $client->name,
                'scope' => $request->input('scope') ? $this->scopeRepository->getScopesById(explode(' ', $request->get('scope'))) : null
                //TODO Scopes are required
            ]
        ]);
    }

    /**
     * Approve an incoming authorization request
     *
     * @throws Exception
     */
    public function approve(Request $request)
    {
        /**
         * Validate client
         */
        if ($e = $this->validateAuthorizationRequest($request)) {
            return new JsonResponse([
                'data' => [
                    'message' => 'Client validation failed'
                ],
                'errors' => $e
            ], 401);
        }

        /**
         * If a response type of 'code' is requested, create a new authorization code
         * and return it via a JSON response
         */
        if ($request->input('response_type') === 'code') {
            $code = $this->createAuthCodeService->handle($request, [
                'user_id' => Auth::user()->uuid,
                'client_id' => $request->get('client_id'),
                'scope' => $request->get('scope')
            ]);

            return new JsonResponse([
                'data' => [
                    'approved' => true,
                    'code' => $code
                ]
            ]);
        }
    }

    /**
     * Deny an incoming authorization request
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deny(Request $request): JsonResponse
    {
        return new JsonResponse([
            'approved' => false
        ]);
    }
}

