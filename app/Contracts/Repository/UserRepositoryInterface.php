<?php

namespace App\Contracts\Repository;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Get a specific user by UUID or Email
     *
     * @param $uuid
     * @return User|null
     */
    public function get($uuid): mixed;

    /**
     * Update a user's account store
     *
     * @param $uuid
     * @param array $data
     * @return mixed
     */
    public function update($uuid, array $data): mixed;
}
