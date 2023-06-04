<?php

namespace App\Http\Controllers\OAuth2;

use App\Contracts\Repository\OAuth2\AuthCodeRepositoryInterface;
use App\Contracts\Repository\OAuth2\ClientRepositoryInterface;
use App\Contracts\Repository\OAuth2\ScopeRepositoryInterface;
use App\Http\Controllers\Controller;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    public function __construct(
        ClientRepositoryInterface   $clientRepository,
        ScopeRepositoryInterface    $scopeRepository,
        AuthCodeRepositoryInterface $authCodeRepository
    )
    {
        $this->clientRepository = $clientRepository;
        $this->scopeRepository = $scopeRepository;
        $this->authCodeRepository = $authCodeRepository;
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
        $request->validate([
            'grant_type' => 'required',
            'code' => 'required',
            'redirect_uri' => 'required',
            'client_id' => 'required',
            'scope' => 'required'
        ]);

        $jwt = (array)JWT::decode($request->input('code'), config('oauth.public_key'));
        $code = $this->authCodeRepository->get($jwt['jti']);

        /**
         * Check that the requested client exists
         */
        try {
            $client = $this->clientRepository->get($request->input('client_id'));
        } catch (Exception $e) {
            return new JsonResponse([
                'error' => 'invalid_client',
                'error_description' => 'Client not found'
            ], 400);
        }

        /**
         * Check that a supported grant type is requested
         */
        if ($request->input('grant_type') !== 'authorization_code') {
            return new JsonResponse([
                'error' => 'unsupported_grant_type',
                'error_description' => 'Unsupported grant type'
            ], 400);
        }

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
         * Check that the redirect uri is the same as the one that was authorized
         */
        if ($request->input('redirect_uri') !== $jwt['aud']) {
            return new JsonResponse([
                'error' => 'invalid_grant',
                'error_description' => 'Failed to verify redirect_uri'
            ], 400);
        }

        /**
         * That that the scopes are the same as the those that were authorized
         */
        if ($request->input('scope') !== $code->scopes) {
            return new JsonResponse([
                'error' => 'invalid_scope',
                'error_description' => 'Failed to verify scope(s)'
            ], 400);
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

        /**
         * If the client is authenticating via PKCE, verify the code_verifier
         */
        if ($jwt['cha']) {
            if (!$request->input('code_verifier')) {
                return new JsonResponse([
                    'error' => 'invalid_request',
                    'error_description' => 'code_verifier is required for authorization via PKCE'
                ], 400);
            }
            if (!Hash::check($request->input('code_verifier'), $jwt['cha'])) {
                return new JsonResponse([
                    'error' => 'invalid_client',
                    'error_description' => 'Client authentication failed'
                ], 401);
            }
        }
    }
}
