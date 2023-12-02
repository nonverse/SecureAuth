<?php

namespace App\Services\User;

use App\Models\Scope;
use Throwable;

trait HasScopes
{
    /**
     * Check if user model has given scope
     *
     * @param string $scope
     * @return bool
     */
    public function hasScope(string $scope): bool
    {
        $scopeEntries = Scope::query()->where('uuid', $this->uuid)->get();

        $scopes = [];
        foreach ($scopeEntries as $scopeEntry) {
            $scopes[] = $scopeEntry['scope'];
        }

        if (in_array($scope, $scopes)) {
            return true;
        }

        return false;
    }

    /**
     * Check if user model has given scopes
     *
     * @param array $scopes
     * @return bool
     */
    public function hasScopes(array $scopes): bool
    {
        $scopeEntries = Scope::query()->where('uuid', $this->uuid)->get();

        $userScopes = [];
        foreach ($scopeEntries as $scopeEntry) {
            $userScopes[] = $scopeEntry['scope'];
        }

        foreach ($scopes as $scope) {
            if (!in_array($scope, $userScopes)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Add a new scope from user
     *
     * @param string $scope
     * @return void
     * @throws Throwable
     */
    public function addScope(string $scope): void
    {
        $scopeEntry = new Scope();

        $scopeEntry->uuid = $this->uuid;
        $scopeEntry->scope = $scope;

        $scopeEntry->saveOrFail();
    }

    /**
     * Remove scope from user model
     *
     * @param string $scope
     * @return void
     */
    public function removeScope(string $scope): void
    {
        Scope::query()->where([['uuid', $this->uuid], ['scope', $scope]])->forceDelete();
    }
}
