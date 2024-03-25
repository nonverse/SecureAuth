<?php

namespace App\Http\Controllers\OAuth2;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait ValidatesAuthorizationCodeGrant
{
    public function validateAuthorizationCodeGrantRequest(Request $request)
    {
        /*
         * Grant specific validation
         */
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'scope' => 'required'
        ]);

        if ($validator->fails()) {
            return new JsonResponse($validator->errors(), 422);
        }

        /**
         * Attempt to decode JWT
         */
        try {
            $jwtCode = (array)JWT::decode($request->input('code'), new Key(config('oauth.public_key'), 'RS256'));
        } catch (ExpiredException $e) {
            return response()->json([
                'error' => 'invalid_grant',
                'error_description' => 'Authorization code has expired'
            ], 400);
        }

        $code = $this->authCodeRepository->get($jwtCode['jti']);

        /**
         * Check if the authorization code has been revoked
         */
        if ($code->revoked) {
            return new JsonResponse([
                'error' => 'invalid_grant',
                'error_description' => 'Authorization code is invalid'
            ], 400);
        }

        /**
         * Check that the redirect uri is the same as the one that was authorized
         */
        if ($request->input('redirect_uri') !== $jwtCode['aud']) {
            return new JsonResponse([
                'error' => 'invalid_grant',
                'error_description' => 'Failed to verify redirect_uri'
            ], 400);
        }

        /**
         * Check if the requested client is the same as the one that was authorized
         */
        if ($request->input('client_id') !== $code->client_id) {
            return new JsonResponse([
                'error' => 'invalid_client',
                'error_description' => 'Client authentication failed'
            ], 401);
        }

        /**
         * Check that the scopes are the same as the those that were authorized
         */
        if ($request->input('scope') !== $code->scopes) {
            return new JsonResponse([
                'error' => 'invalid_scope',
                'error_description' => 'Failed to verify scope(s)'
            ], 400);
        }
    }
}
