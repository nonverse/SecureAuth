<?php

namespace App\Providers;

use App\Contracts\Repository\ActionRepositoryInterface;
use App\Contracts\Repository\AuthorizationTokenRepositoryInterface;
use App\Contracts\Repository\OAuth2\AccessTokenRepositoryInterface;
use App\Contracts\Repository\OAuth2\AuthCodeRepositoryInterface;
use App\Contracts\Repository\OAuth2\ClientRepositoryInterface;
use App\Contracts\Repository\OAuth2\RefreshTokenRepositoryInterface;
use App\Contracts\Repository\OAuth2\ScopeRepositoryInterface;
use App\Contracts\Repository\RecoveryRepositoryInterface;
use App\Contracts\Repository\RepositoryInterface;
use App\Contracts\Repository\UserRepositoryInterface;
use App\Repositories\ActionRepository;
use App\Repositories\AuthorizationTokenRepository;
use App\Repositories\OAuth2\AccessTokenRepository;
use App\Repositories\OAuth2\AuthCodeRepository;
use App\Repositories\OAuth2\ClientRepository;
use App\Repositories\OAuth2\RefreshTokenRepository;
use App\Repositories\OAuth2\ScopeRepository;
use App\Repositories\RecoveryRepository;
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
        // Base
        $this->app->bind(RepositoryInterface::class, Repository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RecoveryRepositoryInterface::class, RecoveryRepository::class);
        $this->app->bind(AuthorizationTokenRepositoryInterface::class, AuthorizationTokenRepository::class);
        $this->app->bind(ActionRepositoryInterface::class, ActionRepository::class);

        //OAuth2
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(AuthCodeRepositoryInterface::class, AuthCodeRepository::class);
        $this->app->bind(AccessTokenRepositoryInterface::class, AccessTokenRepository::class);
        $this->app->bind(RefreshTokenRepositoryInterface::class, RefreshTokenRepository::class);
        $this->app->bind(ScopeRepositoryInterface::class, ScopeRepository::class);
    }
}
