<?php

namespace App\Services\OAuth\AccessToken;

use App\Contracts\Repository\OAuth2\AccessTokenRepositoryInterface;
use Carbon\CarbonImmutable;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CreateAccessTokenService
{

    /**
     * @param AccessTokenRepositoryInterface $tokenRepository
     */
    private AccessTokenRepositoryInterface $tokenRepository;

    public function __construct(
        AccessTokenRepositoryInterface $tokenRepository,
    )
    {
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * Create a new access token
     *
     * @param array $data
     * @param string $userId
     * @return string[]
     */
    public function handle(array $data, string $userId): array
    {
        $id = Str::random(100);

        $payload = [
            'sub' => $userId,
            'iss' => env('APP_URL'),
            'aud' => $data['redirect_uri'],
            'iat' => time(),
            'exp' => time() + config('oauth.access_tokens.expiry') * 60,
            'jti' => $id
        ];

        /**
         * Create new access token
         */
        $jwt = JWT::encode($payload, config('oauth.private_key'), 'RS256');

        /**
         * Create new access token entry
         */
        $this->tokenRepository->create([
            'id' => $id,
            'user_id' => $userId,
            'client_id' => $data['client_id'],
            'scopes' => $data['scope'],
            'revoked' => 0,
            'expires_at' => CarbonImmutable::now()->addMinutes(config('oauth.access_tokens.expiry'))
        ]);

        return [
            'value' => $jwt,
            'id' => $id,
            'expires_in' => config('oauth.access_tokens.expiry') * 60
        ];
    }
}
