<?php

namespace App\Repositories\OAuth2;

use App\Models\OAuth2\RefreshToken;

class RefreshTokenRepository extends \App\Repositories\Repository implements \App\Contracts\Repository\OAuth2\RefreshTokenRepositoryInterface
{

    public function model(): string
    {
        return RefreshToken::class;
    }
}
