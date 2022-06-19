<?php

namespace App\Repositories;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function get($uuid): User
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
}
