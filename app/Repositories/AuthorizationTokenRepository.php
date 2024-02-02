<?php

namespace App\Repositories;

use App\Models\AuthorizationToken;

class AuthorizationTokenRepository extends Repository implements \App\Contracts\Repository\AuthorizationTokenRepositoryInterface
{

    public function model(): string
    {
        return AuthorizationToken::class;
    }
}
