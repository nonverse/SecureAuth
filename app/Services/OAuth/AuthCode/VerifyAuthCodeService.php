<?php

namespace App\Services\OAuth\AuthCode;

use App\Contracts\Repository\OAuth2\AuthCodeRepositoryInterface;
use Carbon\CarbonImmutable;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class VerifyAuthCodeService
{
    /**
     * @var AuthCodeRepositoryInterface
     */
    private AuthCodeRepositoryInterface $repository;

    /**
     * Create new auth code verification service
     *
     * @param AuthCodeRepositoryInterface $repository
     */
    public function __construct(
        AuthCodeRepositoryInterface $repository,
    )
    {
        $this->repository = $repository;
    }

    /**
     * @throws Exception
     */
    public function handle(Request $request, string $jwt): array
    {
        /**
         * Get decoded value of JWT and get database entry using ID
         */
        $decoded = (array)JWT::decode($jwt, file_get_contents('./storage/oauth-public.key'));
        $code = $this->repository->get($decoded['jti']);

        /**
         * Check if the redirect uri in the request matches that of the authorization code
         */
        if ($decoded['aud'] !== $request->input('redirect_uri')) {
            return [
                'result' => false,
                'error' => 'Failed to validate redirect_uri'
            ];
        }

        /**
         * Check if the scopes on the request matches those of the authorization code
         */
        if ($code->scopes !== $request->input('scopes')) {
            return [
                'result' => false,
                'error' => 'Failed to validate scopes'
            ];
        }

        /**
         * Check that the authorization code is not expired
         */
        if (CarbonImmutable::now()->isAfter($code->expires_at)) {
            return [
                'result' => false,
                'error' => 'Authorization code has expired'
            ];
        }

        /**
         * Check that the authorization code is not revoked
         */
        if ($code->revoked) {
            return [
                'result' => false,
                'error' => 'Authorization code has been revoked'
            ];
        }

        /**
         * Revoke authorization code
         */
        $this->repository->update($code->id, ['revoked' => 1]);

        return [
            'result' => true
        ];
    }
}
