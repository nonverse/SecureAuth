<?php

namespace App\Repositories;

use App\Contracts\Repository\AuthMeRepositoryInterface;
use App\Models\AuthMe;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class AuthMeRepository implements AuthMeRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function get($uuid)
    {
        return AuthMe::query()->find($uuid);
    }

    /**
     * @inheritDoc
     */
    public function update($uuid, array $data)
    {
        try {
            $authme = AuthMe::query()->findOrFail($uuid);
        } catch (ModelNotFoundException $e) {
            return false;
        }

        try {
            $authme->fill($data);
        } catch (QueryException $e) {
            return false;
        }

        return $authme;
    }

    /**
     * @inheritDoc
     */
    public function delete($uuid): bool
    {
        try {
            $authme = AuthMe::query()->findOrFail($uuid);
            $authme->delete();
        } catch (ModelNotFoundException $e) {
            return false;
        }

        return true;
    }
}
