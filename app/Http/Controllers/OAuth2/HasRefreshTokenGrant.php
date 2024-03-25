<?php

namespace App\Http\Controllers\OAuth2;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait HasRefreshTokenGrant
{

    /**
     * Create access token using refresh token
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    protected function createTokenUsingRefreshToken(Request $request): JsonResponse
    {
        /**
         * Get decoded value of JWT refresh token (This has already been validated along with the request)
         */
        $jwt = (array)JWT::decode($request->input('refresh_token'), new Key(config('oauth.public_key'), 'RS256'));

        /**
         * Get refresh token entry
         */
        $refreshToken = $this->refreshTokenRepository->get($jwt['jti']);

        /**
         * Get access token entry that was used to issue refresh token
         */
        $accessToken = $this->accessTokenRepository->get($refreshToken->access_token_id);
        //TODO Store scope and user_id in refresh token entry as this access token will eventually be purged in database cleanups

        /**
         * Create new access token
         */
        $token = $this->createAccessTokenService->handle(array_merge($request->all(), [
            'scope' => $accessToken->scopes,
            'redirect_uri' => $accessToken->redirect_uri
        ]), $accessToken->user_id);

        return new JsonResponse([
            'access_token' => $token['value'],
            'token_type' => 'Bearer',
            'expires_in' => $token['expires_in'],
            'scope' => $accessToken->scopes
        ]);
    }
}
