<?php

namespace App\Providers;

use App\Contracts\Repository\AuthMeRepositoryInterface;
use App\Contracts\Repository\UserRepositoryInterface;
use App\Repositories\AuthMeRepository;
use App\Repositories\UserRepository;
use Carbon\Laravel\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AuthMeRepositoryInterface::class, AuthMeRepository::class);
    }

}
