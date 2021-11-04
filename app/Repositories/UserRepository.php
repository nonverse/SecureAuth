<?php

namespace App\Repositories;

use App\Contracts\Repository\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{

    /**
     * Get a list of all registered users
     *
     * @return mixed
     */
    public function index()
    {
        // TODO: Implement index() method.
    }

    /**
     * Get details for a specific user by UUID or Email
     *
     * @param $uuid
     *
     * @return mixed
     */
    public function get($uuid)
    {
        $user = [];
        if (Str::isUuid($uuid)) {
            $user = User::query()->find($uuid);
        } else if (filter_var($uuid, FILTER_VALIDATE_EMAIL)) {
            $user = User::query()->where('email', $uuid)->first();
        }

        return $user;
    }

    /**
     * Create a user and persist to database
     * SHOULD NOT BE USED BY AUTH
     *
     * @param array $data
     *
     * @return void
     */
    public function create(array $data): void
    {
        // TODO: Implement create() method.
    }

    /**
     * Update a user by UUID
     *
     * @param $uuid
     * @param array $data
     *
     * @return User|bool
     */
    public function update($uuid, array $data)
    {
        try {
            $user = User::query()->find($uuid)->firstOrFail();
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

    /**
     * Delete a registered user from database
     * SHOULD NOT BE USED BY AUTH
     *
     * @param $uuid
     * @return void
     */
    public function delete($uuid): void
    {
        // TODO: Implement delete() method.
    }
}
