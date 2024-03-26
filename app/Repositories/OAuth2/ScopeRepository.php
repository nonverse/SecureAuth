<?php

namespace App\Repositories\OAuth2;

use App\Models\OAuth2\Scope;

class ScopeRepository extends \App\Repositories\Repository implements \App\Contracts\Repository\OAuth2\ScopeRepositoryInterface
{

    public function model(): string
    {
        return Scope::class;
    }
}
