<?php

namespace App\Repositories;

use App\Models\Settings;
use Illuminate\Database\Eloquent\Model;

class SettingsRepository extends Repository implements \App\Contracts\Repository\SettingsRepositoryInterface
{

    public function model(): string
    {
        return Settings::class;
    }

    public function getUserSettings(string $uuid): object
    {
        return $this->getBuilder()->where('user_id', $uuid)->get();
    }
}
