<?php

namespace App\Repositories\OAuth2;

use App\Models\OAuth2\AuthCode;

class AuthCodeRepository extends \App\Repositories\Repository implements \App\Contracts\Repository\OAuth2\AuthCodeRepositoryInterface
{

    public function model(): string
    {
        return AuthCode::class;
    }
}
