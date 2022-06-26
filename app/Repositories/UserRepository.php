<?php

namespace App\Repositories;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function get($uuid): User|null
    {
        $user = [];
        if (Str::isUuid($uuid)) {
            $user = User::query()->find($uuid);
            /*
             * If the provided identifier is an email, search for user using
             * their email instead of UUID
             */
        } else if (filter_var($uuid, FILTER_VALIDATE_EMAIL)) {
            $user = User::query()->where('email', $uuid)->first();
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function update($uuid, array $data): mixed
    {
        try {
            $user = User::query()->findOrFail($uuid);
        } catch (ModelNotFoundException $e) {
            return false;
        }

        try {
            $user->fill($data);
            if ($user->isDirty()) {
                $user->save();
            }
        } catch (QueryException $e) {
            return false;
        }

        return $user;
    }
}
