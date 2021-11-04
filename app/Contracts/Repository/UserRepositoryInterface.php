<?php

namespace App\Contracts\Repository;

use App\Models\User;
use Illuminate\Support\Facades\App;

interface UserRepositoryInterface {

    /**
     * Get a list of all registered users
     *
     * @return mixed
     */
    public function index();

    /**
     * Get details for a specific user by UUID or Email
     *
     * @param $uuid
     *
     * @return mixed
     */
    public function get($uuid);

    /**
     * Create a user and persist to database
     * SHOULD NOT BE USED BY AUTH
     *
     * @param array $data
     *
     * @return void
     */
    public function create(array $data): void;

    /**
     * Update a user by UUID
     *
     * @param $uuid
     * @param array $data
     *
     * @return User|bool
     */
    public function update($uuid, array $data);

    /**
     * Delete a registered user from database
     * SHOULD NOT BE USED BY AUTH
     *
     * @param $uuid
     * @return void
     */
    public function delete($uuid): void;
}
