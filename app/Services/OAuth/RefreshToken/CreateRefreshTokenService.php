<?php

namespace App\Services\OAuth\RefreshToken;

use App\Contracts\Repository\OAuth2\RefreshTokenRepositoryInterface;
use App\Models\OAuth2\AccessToken;
use Carbon\CarbonImmutable;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CreateRefreshTokenService
{
    /**
     * @var RefreshTokenRepositoryInterface
     */
    private RefreshTokenRepositoryInterface $tokenRepository;

    public function __construct(
        RefreshTokenRepositoryInterface $tokenRepository
    )
    {
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * Create a new refresh token
     *
     * @param Request $request
     * @param AccessToken $accessToken
     * @return array
     */
    public function handle(Request $request, Model $accessToken): array
    {
        $id = Str::random(100);

        $payload = [
            'iss' => env('APP_URL'),
            'aud' => $request->input('redirect_uri'),
            'iat' => time(),
            'exp' => time() + config('oauth.refresh_tokens.expiry') * 60,
            'jti' => $id
        ];

        /**
         * Create new refresh token
         */
        $token = JWT::encode($payload, config('oauth.private_key'), 'RS256');

        /**
         * Create new refresh token entry
         */
        $this->tokenRepository->create([
            'id' => $id,
            'access_token_id' => $accessToken->id,
            'revoked' => 0,
            'expires_at' => CarbonImmutable::now()->addMinutes(config('oauth.refresh_tokens.expiry'))
        ]);

        return [
            'value' => $token,
            'expires_in' => config('oauth.refresh_tokens.expiry') * 60
        ];
    }
}
