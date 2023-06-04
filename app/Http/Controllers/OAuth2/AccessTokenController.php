<?php

namespace App\Http\Controllers\OAuth2;

use App\Contracts\Repository\OAuth2\AuthCodeRepositoryInterface;
use App\Contracts\Repository\OAuth2\ClientRepositoryInterface;
use App\Contracts\Repository\OAuth2\ScopeRepositoryInterface;
use App\Services\OAuth\AccessToken\CreateAccessTokenService;
use App\Services\OAuth\AuthCode\VerifyAuthCodeService;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
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

    public function __construct(
        ClientRepositoryInterface $clientRepository,
        ScopeRepositoryInterface  $scopeRepository,
        AuthCodeRepositoryInterface $authCodeRepository,
        CreateAccessTokenService  $createAccessTokenService,
    )
    {
        $this->authCodeRepository = $authCodeRepository;
        $this->createAccessTokenService = $createAccessTokenService;
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
        if ($e = $this->validateAccessTokenRequest($request)) {
            return $e;
        }

        $jwt = (array)JWT::decode($request->input('code'), new Key(config('oauth.public_key'), 'RS256'));
        $code = $this->authCodeRepository->get($jwt['jti']);
        $token = $this->createAccessTokenService->handle($request, $code->user_id);

        $this->authCodeRepository->update($code->id, ['revoked' => 1]);

        return new JsonResponse([
            'access_token' => $token['value'],
            'token_type' => 'Bearer',
            'expires_in' => $token['expires_in'],
            'scope' => $request->input('scope')
        ]);
    }
}
