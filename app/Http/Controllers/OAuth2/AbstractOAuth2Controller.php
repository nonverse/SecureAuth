<?php

namespace App\Http\Controllers\OAuth2;

use App\Contracts\Repository\OAuth2\AuthCodeRepositoryInterface;
use App\Contracts\Repository\OAuth2\ClientRepositoryInterface;
use App\Contracts\Repository\OAuth2\RefreshTokenRepositoryInterface;
use App\Contracts\Repository\OAuth2\ScopeRepositoryInterface;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AbstractOAuth2Controller extends Controller
{
    use ValidatesAuthorizationCodeGrant, ValidatesRefreshTokenGrant;

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

        if (!in_array($request->input('response_type'), config('oauth.response_types'))) {
            $errors['response_type'] = 'Unsupported or invalid response type';
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
        /**
         * Core validation
         */
        $validator = Validator::make($request->all(), [
            'grant_type' => 'required',
            'redirect_uri' => 'required_without:refresh_token',
            'client_id' => 'required',
        ]);

        if ($validator->fails()) {
            return new JsonResponse($validator->errors(), 422);
        }

        /**
         * Check that the requested client exists and check client_secret if required
         */
        try {
            $client = $this->clientRepository->get($request->input('client_id'));

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

        } catch (Exception $e) {
            return new JsonResponse([
                'error' => 'invalid_client',
                'error_description' => 'Client not found'
            ], 401);
        }

        switch ($request->input('grant_type')) {
            case 'authorization_code':
                return $this->validateAuthorizationCodeGrantRequest($request);
            case 'refresh_token':
                return $this->validateRefreshTokenGrantRequest($request);
            default:
                return new JsonResponse([
                    'error' => 'invalid_grant_type',
                    'error_description' => 'Requested grant type does not exists or is invalid'
                ]);
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
