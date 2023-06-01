<?php

namespace App\Providers;

use App\Contracts\Repository\RepositoryInterface;
use App\Contracts\Repository\UserRepositoryInterface;
use App\Repositories\Repository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(RepositoryInterface::class, Repository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }
}
