<?php

namespace App\Services\OAuth\AuthCode;

use App\Contracts\Repository\OAuth2\AuthCodeRepositoryInterface;
use Carbon\CarbonImmutable;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CreateAuthCodeService
{
    /**
     * @var AuthCodeRepositoryInterface
     */
    private AuthCodeRepositoryInterface $repository;

    /**
     * Create new auth code service
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
    public function handle(Request $request, array $data): string
    {
        $id = Str::random(100);

        $payload = [
            'iss' => env('APP_URL'),
            'aud' => $request->input('redirect_uri'),
            'nbf' => time(),
            'iat' => time(),
            'exp' => time() + 5 * 60,
            'jti' => $id
        ];

        /**
         * Create new auth code
         */
        $jwt = JWT::encode($payload, config('oauth.private_key'), 'RS256');

        /**
         * Create new auth code entry
         */
        $this->repository->create([
            'id' => $id,
            'user_id' => $data['user_id'],
            'client_id' => $data['client_id'],
            'scopes' => $data['scopes'],
            'revoked' => 0,
            'expires_at' => CarbonImmutable::now()->addMinutes(5)
        ]);

        return $jwt;
    }
}
