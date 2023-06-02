<?php

namespace App\Contracts\Repository\OAuth2;

interface ScopeRepositoryInterface
{
    /**
     * Get array of scopes by IDs
     *
     * @param array $ids
     * @return array
     */
    public function getScopesById(array $ids): array;
}
