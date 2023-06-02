<?php

namespace App\Repositories\OAuth2;

use App\Models\OAuth2\Scope;
use Exception;

class ScopeRepository extends \App\Repositories\Repository implements \App\Contracts\Repository\OAuth2\ScopeRepositoryInterface
{

    public function model(): string
    {
        return Scope::class;
    }

    /**
     * @throws Exception
     */
    public function getScopesById(array $ids): array
    {
        $scopes = [];
        foreach ($ids as &$id) {
            $scopes[] = $this->get($id);
        }

        return $scopes;
    }
}
