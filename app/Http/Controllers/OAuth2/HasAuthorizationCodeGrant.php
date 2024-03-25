<?php

namespace App\Http\Controllers\OAuth2;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait HasAuthorizationCodeGrant
{

    /**
     * Create access token using authorization code
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    protected function createTokenUsingAuthorizationCode(Request $request): JsonResponse
    {

        /**
         * Get decoded value of JWT (JWT has already been validated along with the request)
         */
        $jwt = (array)JWT::decode($request->input('code'), new Key(config('oauth.public_key'), 'RS256'));

        /**
         * Get authorization code entry
         */
        $code = $this->authCodeRepository->get($jwt['jti']);

        /**
         * Create a new token
         */
        $token = $this->createAccessTokenService->handle($request->all(), $code->user_id);

        $response = [
            'access_token' => $token['value'],
            'token_type' => 'Bearer',
            'expires_in' => $token['expires_in'],
            'scope' => $request->input('scope')
        ];

        /**
         * If this is the first time that the client and user pair are requesting an access token,
         * automatically issue a long lasting refresh token
         */
        if (!$this->accessTokenRepository->getBuilder()->where(['client_id' => $code->client_id, 'user_id' => $code->user_id])->whereNot('id', $token['id'])->exists()) {
            $refreshToken = $this->createRefreshTokenService->handle($request, $this->accessTokenRepository->get($token['id']));
            $response['refresh_token'] = $refreshToken['value'];
        }

        /**
         * Invalidate the authorization code that was used
         */
        $this->authCodeRepository->update($code->id, ['revoked' => 1]);

        return new JsonResponse($response);

    }
}
