<?php

namespace App\Services\Authorization;

use App\Contracts\Repository\AuthorizationTokenRepositoryInterface;
use Carbon\CarbonImmutable;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CreateAuthorizationTokenService
{
    /**
     * @var AuthorizationTokenRepositoryInterface
     */
    private AuthorizationTokenRepositoryInterface $repository;

    public function __construct(
        AuthorizationTokenRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * Create a new authorization token and entry
     *
     * @param Request $request
     * @param array $data
     * @return string
     */
    public function handle(Request $request, array $data): string
    {
        $id = Str::random(100);

        $payload = [
            'iss' => env('APP_URL'),
            'aud' => env('VITE_ACCOUNT_APP'),
            'iat' => time(),
            'exp' => time() + 5 * 60,
            'aci' => $request->input('action_id'),
            'jti' => $id
        ];

        /**
         * Create new authorization token
         */
        $jwt = JWT::encode($payload, config('oauth.private_key'), 'RS256');

        /**
         * Create new authorization token entry
         */
        $this->repository->create([
            'id' => $id,
            'user_id' => $request->user()->uuid,
            'action_id' => $data['action_id'],
            'expires_at' => CarbonImmutable::now()->addMinutes(5),
        ]);

        return $jwt;
    }
}
