<?php

namespace App\Http\Controllers\OAuth2;

use App\Contracts\Repository\OAuth2\AccessTokenRepositoryInterface;
use App\Contracts\Repository\OAuth2\AuthCodeRepositoryInterface;
use App\Contracts\Repository\OAuth2\ClientRepositoryInterface;
use App\Contracts\Repository\OAuth2\RefreshTokenRepositoryInterface;
use App\Contracts\Repository\OAuth2\ScopeRepositoryInterface;
use App\Services\OAuth\AccessToken\CreateAccessTokenService;
use App\Services\OAuth\RefreshToken\CreateRefreshTokenService;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccessTokenController extends AbstractOAuth2Controller
{
    use HasAuthorizationCodeGrant, HasRefreshTokenGrant;

    /**
     * @var AccessTokenRepositoryInterface
     */
    private AccessTokenRepositoryInterface $accessTokenRepository;

    /**
     * @var AuthCodeRepositoryInterface
     */
    private AuthCodeRepositoryInterface $authCodeRepository;

    /**
     * @var RefreshTokenRepositoryInterface
     */
    private RefreshTokenRepositoryInterface $refreshTokenRepository;

    /**
     * @var CreateAccessTokenService
     */
    private CreateAccessTokenService $createAccessTokenService;

    /**
     * @var CreateRefreshTokenService
     */
    private CreateRefreshTokenService $createRefreshTokenService;

    public function __construct(
        AccessTokenRepositoryInterface  $accessTokenRepository,
        ClientRepositoryInterface       $clientRepository,
        ScopeRepositoryInterface        $scopeRepository,
        AuthCodeRepositoryInterface     $authCodeRepository,
        RefreshTokenRepositoryInterface $refreshTokenRepository,
        CreateAccessTokenService        $createAccessTokenService,
        CreateRefreshTokenService       $createRefreshTokenService,
    )
    {
        $this->accessTokenRepository = $accessTokenRepository;
        $this->authCodeRepository = $authCodeRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->createAccessTokenService = $createAccessTokenService;
        $this->createRefreshTokenService = $createRefreshTokenService;
        parent::__construct($clientRepository, $scopeRepository, $authCodeRepository, $refreshTokenRepository);
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

        switch ($request->input('grant_type')) {
            case 'authorization_code':
                return $this->createTokenUsingAuthorizationCode($request);
            case 'refresh_token':
                return $this->createTokenUsingRefreshToken($request);
        }
    }
}
