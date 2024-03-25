<?php

namespace App\Http\Controllers\OAuth2;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait ValidatesRefreshTokenGrant
{
    public function validateRefreshTokenGrantRequest(Request $request)
    {

        /*
         * Grant specific validation
         */
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required'
        ]);

        if ($validator->fails()) {
            return new JsonResponse($validator->errors(), 422);
        }

        /**
         * Attempt to decode JWT and get refresh token entry
         */
        try {
            $jwtRefreshToken = (array)JWT::decode($request->get('refresh_token'), new Key(config('oauth.public_key'), 'RS256'));
            $refreshToken = $this->refreshTokenRepositoryInterface->get($jwtRefreshToken['jti']);
        } catch (ExpiredException $e) {
            return response()->json([
                'error' => 'invalid_grant',
                'error_description' => 'Refresh token has expired'
            ], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'invalid_grant',
                'error_description' => 'Refresh token is invalid'
            ], 400);
        }

        if ($refreshToken->revoked) {
            return response()->json([
                'error' => 'invalid_grant',
                'error_description' => 'Refresh token is invalid'
            ], 400);
        }
    }
}
