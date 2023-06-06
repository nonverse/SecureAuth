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
     * @param Request $request
     * @param string $userId
     * @return string[]
     */
    public function handle(Request $request, string $userId): array
    {
        $id = Str::random(100);

        $payload = [
            'iss' => env('APP_URL'),
            'aud' => $request->input('redirect_uri'),
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
            'client_id' => $request->input('client_id'),
            'scopes' => $request->input('scope'),
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
