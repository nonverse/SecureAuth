<?php

namespace App\Http\Controllers\OAuth2;

use App\Contracts\Repository\OAuth2\AuthCodeRepositoryInterface;
use App\Contracts\Repository\OAuth2\ClientRepositoryInterface;
use App\Contracts\Repository\OAuth2\RefreshTokenRepositoryInterface;
use App\Contracts\Repository\OAuth2\ScopeRepositoryInterface;
use App\Http\Controllers\Controller;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AbstractOAuth2Controller extends Controller
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
     * @var AuthCodeRepositoryInterface
     */
    private AuthCodeRepositoryInterface $authCodeRepository;

    /**
     * @var RefreshTokenRepositoryInterface
     */
    private RefreshTokenRepositoryInterface $refreshTokenRepositoryInterface;

    public function __construct(
        ClientRepositoryInterface       $clientRepository,
        ScopeRepositoryInterface        $scopeRepository,
        AuthCodeRepositoryInterface     $authCodeRepository,
        RefreshTokenRepositoryInterface $refreshTokenRepository,
    )
    {
        $this->clientRepository = $clientRepository;
        $this->scopeRepository = $scopeRepository;
        $this->authCodeRepository = $authCodeRepository;
        $this->refreshTokenRepositoryInterface = $refreshTokenRepository;
    }

    /**
     * Validate client
     *
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function validateAuthorizationRequest(Request $request): array
    {
        $request->validate([
            'client_id' => 'required',
            'redirect_uri' => 'required',
            'response_type' => 'required',
            'scope' => 'required'
        ]);

        $errors = [];
        try {
            $client = $this->clientRepository->get($request->input('client_id'));
        } catch (Exception $e) {
            return [
                'client_id' => 'Client not found'
            ];
        }

        if ($client->revoked) {
            $errors['client_id'] = 'Invalid client';
        }

        if ($client->redirect !== $request->input('redirect_uri')) {
            $errors['redirect_uri'] = 'Unable to validate redirect_uri';
        }

        if (!in_array($request->input('response_type'), config('oauth.grant_types'))) {
            $errors['response_type'] = 'Unsupported or invalid grant type';
        }

        if ($request->input('scope')) {
            $scopes = explode(' ', $request->input('scope'));

            try {
                $this->scopeRepository->getScopesById($scopes);
                foreach ($scopes as $scope) {
                    $scopeEntry = $this->scopeRepository->get($scope);
                    if ($scopeEntry->client_id && $request->input('client_id') !== $scopeEntry->client_id) {
                        $errors['scopes'] = 'scope ' . $scopeEntry->id . ' is restricted';
                    }
                }
            } catch (Exception $e) {
                $errors['scopes'] = $e->getMessage();
            }
        }

        return $errors;
    }

    /**
     * Validate access token request
     *
     * @param Request $request
     * @return JsonResponse|void
     * @throws Exception
     */
    public function validateAccessTokenRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'grant_type' => 'required',
            'code' => 'required_without:refresh_token',
            'refresh_token' => 'required_without:code',
            'redirect_uri' => 'required_without:refresh_token',
            'client_id' => 'required',
            'scope' => 'required_without:refresh_token'
        ]);

        if ($validator->fails()) {
            return new JsonResponse($validator->errors(), 422);
        }

        /**
         * This code will only run if the client is attempting to get an access token using an
         * authorization code
         */
        if ($request->input('code')) {
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
                return response()->json([
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
        }

        /**
         * Check that the requested client exists
         */
        try {
            $client = $this->clientRepository->get($request->input('client_id'));
        } catch (Exception $e) {
            return new JsonResponse([
                'error' => 'invalid_client',
                'error_description' => 'Client not found'
            ], 401);
        }

        /**
         * This code will only run if the client is attempting to get an access token using an
         * authorization code
         */
        if ($request->input('code')) {
            /**
             * Check if the requested client is the same as the one that was authorized
             */
            if ($client->id !== $code->client_id) {
                return new JsonResponse([
                    'error' => 'invalid_client',
                    'error_description' => 'Client authentication failed'
                ], 401);
            }

            /**
             * If the client is authenticating via PKCE, verify the code_verifier
             */
            if (array_key_exists('cha', $jwtCode)) {
                if (!$request->input('code_verifier')) {
                    return new JsonResponse([
                        'error' => 'invalid_request',
                        'error_description' => 'code_verifier is required for authorization via PKCE'
                    ], 400);
                }
                if (!Hash::check($request->input('code_verifier'), $jwtCode['cha'])) {
                    return new JsonResponse([
                        'error' => 'invalid_client',
                        'error_description' => 'Client authentication failed'
                    ], 401);
                }
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

        /**
         * If the client is protected using a secret, verify the client secret
         */
        if ($client->secret) {
            if (!$request->input('client_secret')) {
                return new JsonResponse([
                    'error' => 'invalid_request',
                    'error_description' => 'client_secret is required for authorization'
                ], 400);
            }
            if (!Hash::check($request->input('client_secret'), $client->secret)) {
                return new JsonResponse([
                    'error' => 'invalid_client',
                    'error_description' => 'Client authentication failed'
                ], 401);
            }
        }

        if ($request->input('refresh_token')) {

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

        /**
         * Check that a requested grant type is supported
         */
        if ($request->input('grant_type') !== 'authorization_code') {
            return new JsonResponse([
                'error' => 'unsupported_grant_type',
                'error_description' => 'Unsupported grant type'
            ], 400);
        }
    }

    /**
     * Check if user has required scopes to access application
     *
     * @param Request $request
     * @return bool
     * @throws Exception
     */
    public function validateUserScopes(Request $request): bool
    {

        $client = $this->clientRepository->get($request->input('client_id'));

        if ($client->scopes) {
            $scopes = explode(', ', substr($client->scopes, 1, -1));

            if (!$request->user()->hasScopes($scopes)) {
                return false;
            }
        }

        return true;
    }
}
