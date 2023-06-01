<?php

namespace App\Services\OAuth\Client;

use App\Contracts\Repository\OAuth2\ClientRepositoryInterface;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Str;

class CreateClientService
{
    /**
     * @var ClientRepositoryInterface
     */
    private ClientRepositoryInterface $repository;

    /**
     * @var Hasher
     */
    private Hasher $hasher;

    /**
     * Create new client repository
     *
     * @param ClientRepositoryInterface $repository
     * @param Hasher $hasher
     */
    public function __construct(
        ClientRepositoryInterface $repository,
        Hasher                    $hasher,
    )
    {
        $this->repository = $repository;
        $this->hasher = $hasher;
    }

    public function handle(array $data): array
    {

        $secret = Str::random(40);

        $client = $this->repository->create([
            'id' => Str::uuid(),
            'name' => $data['name'],
            'user_id' => array_key_exists('user_id', $data) ? $data['user_id'] : null,
            'secret' => $this->hasher->make($secret),
            'provider' => array_key_exists('provider', $data) ? $data['provider'] : null,
            'redirect' => $data['redirect'],
            'personal_access_client' => array_key_exists('client_type', $data) && $data['client_type'] === 'personal_access_client' ? 1 : 0,
            'password_client' => array_key_exists('client_type', $data) && $data['client_type'] === 'password_client' ? 1 : 0,
            'revoked' => 0
        ], true);

        return [
            'client_id' => $client->id,
            'client_secret' => $secret
        ];
    }
}
