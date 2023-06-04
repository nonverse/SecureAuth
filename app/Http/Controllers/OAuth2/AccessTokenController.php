<?php

namespace App\Http\Controllers\OAuth2;

use App\Contracts\Repository\OAuth2\AuthCodeRepositoryInterface;
use App\Contracts\Repository\OAuth2\ClientRepositoryInterface;
use App\Contracts\Repository\OAuth2\ScopeRepositoryInterface;
use App\Services\OAuth\AccessToken\CreateAccessTokenService;
use App\Services\OAuth\AuthCode\VerifyAuthCodeService;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccessTokenController extends AbstractOAuth2Controller
{
    /**
     * @var AuthCodeRepositoryInterface
     */
    private AuthCodeRepositoryInterface $authCodeRepository;

    /**
     * @var CreateAccessTokenService
     */
    private CreateAccessTokenService $createAccessTokenService;

    /**
     * @var VerifyAuthCodeService
     */
    private VerifyAuthCodeService $verifyAuthCodeService;

    public function __construct(
        ClientRepositoryInterface $clientRepository,
        ScopeRepositoryInterface  $scopeRepository,
        AuthCodeRepositoryInterface $authCodeRepository,
        CreateAccessTokenService  $createAccessTokenService,
        VerifyAuthCodeService     $verifyAuthCodeService
    )
    {
        $this->authCodeRepository = $authCodeRepository;
        $this->createAccessTokenService = $createAccessTokenService;
        $this->verifyAuthCodeService = $verifyAuthCodeService;
        parent::__construct($clientRepository, $scopeRepository, $authCodeRepository);
    }

    /**
     * Create a new access token
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function createToken(Request $request): JsonResponse
    {
        /**
         * Validate the access token request
         */
        $this->validateAccessTokenRequest($request);

        $jwt = (array)JWT::decode($request->input('code'), config('oauth.public_key'));
        $token = $this->createAccessTokenService->handle($request, $this->authCodeRepository->get($jwt['jti'])->user_id);

        return new JsonResponse([
            'access_token' => $token['value'],
            'token_type' => 'Bearer',
            'expires_in' => $token['expires_in'],
            'scope' => $request->input('scope')
        ]);
    }
}
