<?php

namespace App\Services\Api;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class FrontEndTokenCreationService
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * @var Encrypter
     */
    private $encrypter;

    public function __construct(
        UserRepositoryInterface $repository,
        Encrypter               $encrypter
    )
    {
        $this->repository = $repository;
        $this->encrypter = $encrypter;
    }

    /**
     * Create an API authentication token to authenticate first party
     * front end requests from an authenticated user. (This will be highly important
     * once all server authentication is made stateless)
     *
     * @param User $user
     * @return string
     */
    public function handle(User $user): string
    {
        $token = Str::random(64);
        $saved = $this->repository->update($user->uuid, [
            'api_encryption' => $this->encrypter->encryptString($token)
        ]);

        if (!$saved) {
            return false;
        }

        return $token;
    }
}
